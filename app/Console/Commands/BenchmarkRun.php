<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Book;
use App\Models\User;
use App\Models\Loan;
use App\Models\Uses;

class BenchmarkRun extends Command
{
    protected $signature = "benchmark:run
        {system : mysql ili mongo}
        {--volumes=5000,10000,100000,1000000 : lista obima podataka, odvojenih zarezom}
        {--seed-chunk=2000 : koliko zapisa se ubacuje po jednom bulk insert pozivu prilikom punjenja baze}";

    protected $description = "Pokreće ceo set benchmark testova za više obima podataka i upisuje rezultate u storage/benchmark_results.csv";

    protected array $rows = [];
    protected string $system;
    protected string $bookKey;
    protected string $userKey;

    public function handle()
    {
        $this->system = $this->argument("system");

        if (!in_array($this->system, ["mysql", "mongo"])) {
            $this->error("Argument mora biti 'mysql' ili 'mongo'.");
            return 1;
        }

        $this->bookKey = $this->system === "mysql" ? "bookId" : "id";
        $this->userKey = $this->system === "mysql" ? "userId" : "id";

        $volumes = array_map("intval", explode(",", $this->option("volumes")));
        sort($volumes);

        foreach ($volumes as $volume) {
            $this->info("=== Obim: {$volume} ===");
            $this->ensureVolume($volume);
            $this->runAllTests($volume);
        }

        $this->writeCsv();
        $this->info("Готово. Резултати у storage/benchmark_results.csv");
        return 0;
    }

    /**
     * Vraća red za seed-ovanje User zapisa, prilagođen šemi svakog sistema.
     * MySQL `users` tabela NEMA accountNumber kolonu (to polje postoji samo
     * na Subscription entitetu u relacionom modelu). Mongo `users` kolekcija
     * accountNumber čuva direktno na korisniku — otud grananje ovde.
     *
     * Koristi random_bytes() umesto uniqid() za email — uniqid() se zasniva
     * na sistemskom mikrovremenu i pri brzim uzastopnim pozivima (bulk insert
     * u petlji) može da ponovi istu vrednost, što na velikim obimima (500k+)
     * puca na UNIQUE ograničenju email kolone.
     */
    protected function userSeedRow(): array
    {
        $unique = bin2hex(random_bytes(8));

        $base = [
            "name" => "Ime" . $unique,
            "surname" => "Prezime" . $unique,
            "email" => "seed{$unique}@test.com",
            "password" => '$2y$10$abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQ', // unapred preračunat hash, ne bcrypt() u petlji
            "userType" => "user",
            "numberOfLoans" => rand(0, 5),
            "created_at" => now(),
            "updated_at" => now(),
        ];

        if ($this->system === "mongo") {
            $base["accountNumber"] = (string) rand(1000000000, 9999999999);
        }

        return $base;
    }

    protected function ensureVolume(int $volume): void
    {
        $this->topUp(Book::class, $volume, fn() => [
            "title" => "Seed knjiga " . bin2hex(random_bytes(6)),
            "author" => "Autor " . rand(1, 500),
            "genre" => ["fiction", "drama", "sci-fi", "biography"][rand(0, 3)],
            "description" => "opis",
            "numberOfPages" => rand(80, 700),
            "language" => "sr",
            "publicationDate" => now()->subDays(rand(1, 3000)),
            "publisher" => "Izdavac " . rand(1, 50),
            "remainingForLoan" => rand(0, 15),
            "remainingForSell" => rand(0, 10),
            "created_at" => now(),
            "updated_at" => now(),
        ]);

        $this->topUp(User::class, $volume, fn() => $this->userSeedRow());

        $bookIds = Book::select($this->bookKey)->limit(1000)->pluck($this->bookKey)->all();
        $userIds = User::select($this->userKey)->limit(1000)->pluck($this->userKey)->all();

        $this->topUp(Loan::class, $volume, function () use ($bookIds, $userIds) {
            return [
                "bookId" => $bookIds[array_rand($bookIds)],
                "userId" => $userIds[array_rand($userIds)],
                "loanDate" => now()->subDays(rand(0, 60)),
                "endReturnDate" => now()->addDays(30),
                "status" => "manual_pickup_requested",
                "deliveryType" => rand(0, 1) ? "physical" : "library",
                "remaining" => rand(0, 10),
                "low_stock" => false,
                "created_at" => now(),
                "updated_at" => now(),
            ];
        });

        $this->topUp(Uses::class, $volume, function () use ($bookIds, $userIds) {
            return [
                "bookId" => $bookIds[array_rand($bookIds)],
                "userId" => $userIds[array_rand($userIds)],
                "type" => ["view", "loan", "sell"][rand(0, 2)],
                "points" => rand(1, 10),
                "date" => now()->subDays(rand(0, 45)),
                "created_at" => now(),
                "updated_at" => now(),
            ];
        });
    }

    protected function topUp(string $modelClass, int $target, callable $rowFactory): void
    {
        $current = $modelClass::count();
        $missing = $target - $current;

        if ($missing <= 0) {
            return;
        }

        $chunkSize = (int) $this->option("seed-chunk");
        $bar = $this->output->createProgressBar($missing);
        $bar->start();

        while ($missing > 0) {
            $batch = min($chunkSize, $missing);
            $rows = [];
            for ($i = 0; $i < $batch; $i++) {
                $rows[] = $rowFactory();
            }
            $modelClass::insert($rows);
            $missing -= $batch;
            $bar->advance($batch);
        }

        $bar->finish();
        $this->newLine();
    }

    protected function runAllTests(int $volume): void
    {
        $bookCount = Book::count();
        $userCount = User::count();

        $randomBook = Book::skip(random_int(0, $bookCount - 1))->first();
        $randomUser = User::skip(random_int(0, $userCount - 1))->first();
        $randomBookId = $randomBook->{$this->bookKey};
        $randomUserId = $randomUser->{$this->userKey};

        // KNJIGA — pojedinačan upis
        $this->measure("book_insert_single", 30, $volume, function () {
            Book::create([
                "title" => "Test knjiga " . bin2hex(random_bytes(6)),
                "author" => "Test Autor",
                "genre" => "fiction",
                "description" => "opis",
                "numberOfPages" => 200,
                "language" => "sr",
                "publicationDate" => now(),
                "publisher" => "Test",
                "remainingForLoan" => 5,
                "remainingForSell" => 5,
            ]);
        });

        // KNJIGA — masovni upis (500 zapisa)
        $this->measure("book_insert_bulk_500", 10, $volume, function () {
            $data = [];
            for ($i = 0; $i < 500; $i++) {
                $data[] = [
                    "title" => "Bulk knjiga " . bin2hex(random_bytes(6)),
                    "author" => "Autor",
                    "genre" => "drama",
                    "description" => "opis",
                    "numberOfPages" => 150,
                    "language" => "sr",
                    "publicationDate" => now(),
                    "publisher" => "Test",
                    "remainingForLoan" => 3,
                    "remainingForSell" => 3,
                    "created_at" => now(),
                    "updated_at" => now(),
                ];
            }
            Book::insert($data);
        });

        // KNJIGA — čitanje po identifikatoru
        $this->measure("book_find_by_id", 50, $volume, function () use ($randomBookId) {
            $this->system === "mysql"
                ? Book::where("bookId", $randomBookId)->first()
                : Book::find($randomBookId);
        });

        // KNJIGA — filtriranje po žanru
        $this->measure("book_filter_by_genre", 50, $volume, function () {
            Book::where("genre", "fiction")->limit(20)->get();
        });

        // KNJIGA — opseg po broju strana
        $this->measure("book_filter_by_pages_range", 50, $volume, function () {
            Book::whereBetween("numberOfPages", [100, 300])->limit(20)->get();
        });

        // KNJIGA — delimično pretraživanje naslova
        $this->measure("book_search_title_partial", 30, $volume, function () {
            Book::where("title", "like", "%knjiga%")->limit(20)->get();
        });

        // KNJIGA — ažuriranje jednog zapisa
        $this->measure("book_update_single", 50, $volume, function () use ($randomBookId) {
            $this->system === "mysql"
                ? Book::where("bookId", $randomBookId)->update(["remainingForLoan" => rand(0, 20)])
                : Book::where("_id", $randomBookId)->update(["remainingForLoan" => rand(0, 20)]);
        });

        // KNJIGA — masovno ažuriranje po žanru
        $this->measure("book_update_bulk_by_genre", 20, $volume, function () {
            Book::where("genre", "drama")->update(["remainingForLoan" => rand(0, 20)]);
        });

        // KNJIGA — brisanje jednog zapisa (kreira pa briše)
        $this->measure("book_delete_single", 30, $volume, function () {
            $book = Book::create([
                "title" => "Za brisanje " . bin2hex(random_bytes(6)),
                "author" => "X",
                "genre" => "fiction",
                "description" => "opis",
                "numberOfPages" => 100,
                "language" => "sr",
                "publicationDate" => now(),
                "publisher" => "X",
                "remainingForLoan" => 0,
                "remainingForSell" => 0,
            ]);
            $book->delete();
        });

        // KNJIGA — prebrojavanje svih zapisa
        $this->measure("book_count_all", 20, $volume, function () {
            Book::count();
        });

        // KNJIGA — paginirano čitanje
        $midPage = max(1, intdiv($bookCount, 40));
        $this->measure("book_paginated_read", 30, $volume, function () use ($midPage) {
            Book::orderBy("title")->forPage($midPage, 20)->get();
        });

        // KORISNIK — pojedinačan upis (grananje zbog accountNumber razlike)
        $this->measure("user_insert_single", 30, $volume, function () {
            $unique = bin2hex(random_bytes(8));
            $row = [
                "name" => "Test",
                "surname" => "Korisnik " . $unique,
                "email" => "test{$unique}@test.com",
                "password" => '$2y$10$abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQ',
                "userType" => "user",
                "numberOfLoans" => 3,
            ];
            if ($this->system === "mongo") {
                $row["accountNumber"] = "1234567890";
            }
            User::create($row);
        });

        // KORISNIK — čitanje po identifikatoru
        $this->measure("user_find_by_id", 50, $volume, function () use ($randomUserId) {
            $this->system === "mysql"
                ? User::where("userId", $randomUserId)->first()
                : User::find($randomUserId);
        });

        // POZAJMICA — pojedinačan upis
        $this->measure("loan_insert_single", 30, $volume, function () use ($randomBookId, $randomUserId) {
            Loan::create([
                "bookId" => $randomBookId,
                "userId" => $randomUserId,
                "loanDate" => now(),
                "endReturnDate" => now()->addDays(30),
                "status" => "manual_pickup_requested",
                "deliveryType" => "library",
                "remaining" => 5,
                "low_stock" => false,
            ]);
        });

        // POZAJMICA — masovni upis (500 zapisa)
        $this->measure("loan_insert_bulk_500", 10, $volume, function () use ($randomBookId, $randomUserId) {
            $data = [];
            for ($i = 0; $i < 500; $i++) {
                $data[] = [
                    "bookId" => $randomBookId,
                    "userId" => $randomUserId,
                    "loanDate" => now(),
                    "endReturnDate" => now()->addDays(30),
                    "status" => "manual_pickup_requested",
                    "deliveryType" => "library",
                    "remaining" => 5,
                    "low_stock" => false,
                    "created_at" => now(),
                    "updated_at" => now(),
                ];
            }
            Loan::insert($data);
        });

        // POZAJMICA — filtriranje po korisniku
        $this->measure("loan_filter_by_user", 50, $volume, function () use ($randomUserId) {
            Loan::where("userId", $randomUserId)->get();
        });

        // SLOŽENA AGREGACIJA — grana se po sistemu (JOIN naspram dvostepenog upita)
        if ($this->system === "mysql") {
            $this->measure("book_popular_join_aggregate", 20, $volume, function () {
                Book::select("books.*")
                    ->join("uses", "books.bookId", "=", "uses.bookId")
                    ->where("uses.date", ">=", now()->subMonth())
                    ->groupBy("books.bookId")
                    ->selectRaw("SUM(uses.points) as total_points")
                    ->orderByDesc("total_points")
                    ->limit(10)
                    ->get();
            });
        } else {
            $this->measure("book_popular_aggregate_pipeline", 20, $volume, function () {
        $pipeline = [
            [
                '$match' => [
                    'date' => ['$gte' => new \MongoDB\BSON\UTCDateTime(now()->subMonth())],
                ],
            ],
            [
                '$group' => [
                    '_id' => '$bookId',
                    'total_points' => ['$sum' => '$points'],
                ],
            ],
            ['$sort' => ['total_points' => -1]],
            ['$limit' => 10],
        ];

        $topRows = Uses::raw(function ($collection) use ($pipeline) {
            return $collection->aggregate($pipeline)->toArray();
        });

        $ids = array_map(fn($row) => $row["_id"], $topRows);

        Book::whereIn("_id", $ids)->get();
    });
    }}

    protected function measure(string $label, int $repeats, int $volume, callable $fn): void
    {
        $times = [];
        for ($i = 0; $i < $repeats; $i++) {
            $start = microtime(true);
            $fn();
            $times[] = (microtime(true) - $start) * 1000;
        }
        sort($times);
        $avg = array_sum($times) / count($times);
        $median = $times[intdiv(count($times), 2)];

        $result = [
            "system" => $this->system,
            "volume" => $volume,
            "label" => $label,
            "repeats" => $repeats,
            "avg_ms" => round($avg, 3),
            "median_ms" => round($median, 3),
            "min_ms" => round(min($times), 3),
            "max_ms" => round(max($times), 3),
            "throughput_per_sec" => round(1000 / $avg, 2),
        ];

        $this->rows[] = $result;
        $this->line("  ✓ [{$volume}] {$label}: avg {$result["avg_ms"]} ms");
    }

    protected function writeCsv(): void
    {
        $path = storage_path("benchmark_results.csv");
        $isNew = !file_exists($path);
        $handle = fopen($path, "a");

        if ($isNew) {
            fputcsv($handle, ["system", "volume", "label", "repeats", "avg_ms", "median_ms", "min_ms", "max_ms", "throughput_per_sec"]);
        }

        foreach ($this->rows as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);
    }
}
