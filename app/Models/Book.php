<?php
namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\EmbedsMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Book extends Model
{
    use HasFactory;

    protected $connection = "mongodb";
    protected $table = "books";

    protected $fillable = [
        "preview_image",
        "title",
        "author",
        "genre",
        "description",
        "numberOfPages",
        "language",
        "publicationDate",
        "publisher",
        "remainingForLoan",
        "remainingForSell",
        "preview",
    ];

    public function prices(): EmbedsMany
    {
        return $this->embedsMany(Price::class);
    }

    public function currentPrice(): ?Price
    {
        return $this->prices()
            ->get()
            ->first(
                fn($p) => $p->startDate <= Carbon::today() &&
                    $p->endDate >= Carbon::today(),
            );
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, "bookId", "_id");
    }

    public function sells()
    {
        return $this->hasMany(Sell::class, "bookId", "_id");
    }

    public function uses()
    {
        return $this->hasMany(Uses::class, "bookId", "_id");
    }
}
