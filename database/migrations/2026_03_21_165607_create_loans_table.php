<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("loans", function (Blueprint $table) {
            $table->id("loanId");
            $table
                ->foreignId("bookId")
                ->constrained("books", "bookId")
                ->cascadeOnDelete();
            $table
                ->foreignId("userId")
                ->constrained("users", "userId")
                ->cascadeOnDelete();
            $table->date("loanDate");
            $table->date("endReturnDate");
            $table->date("returnDate")->nullable();
            $table
                ->enum("status", [
                    "manual_pickup_requested", // rucnoPreuzimanje
                    "manual_picked_up", // rucnoPreuzeta
                    "sending_by_post", // slanjePostom
                    "post_arrived_for_pickup", // postaStiglia ZaUrucanje
                    "postal_pickup", // postanskoPreuzimanje
                    "postal_pickup_cancelled", // otkazanoPostanskoPreuzimanje
                    "returned_unwanted_by_post", // neposeljnoVracenoPostom
                    "return_on_time_started", // pocetakVracanjaNaVreme
                    "return_late_started", // pocetakVracanjaZakasnjenjem
                    "manual_return", // rucnoVracanje
                    "postal_return", // postaVracanje
                    "return_sent_by_post", // vracanjePoslatoPosti
                    "return_arrived_by_post", // vracanjePristaloIzPoste
                    "returned_on_time", // vracenaNaVreme
                    "returned_late", // vracenaSaZakasnjenjem])
                ])
                ->default("manual_pickup_requested");
            $table->string("deliveryType");
            $table->string("returnType")->nullable();
            $table->integer("remaining")->default(0);
            $table->boolean("low_stock")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("loans");
    }
};
