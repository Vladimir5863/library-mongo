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
        Schema::create("sells", function (Blueprint $table) {
            $table->id("sellId");
            $table
                ->foreignId("bookId")
                ->constrained("books", "bookId")
                ->cascadeOnDelete();
            $table
                ->foreignId("userId")
                ->constrained("users", "userId")
                ->cascadeOnDelete();
            $table->date("sellDate");
            $table->string("deliveryType");
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
        Schema::dropIfExists("sells");
    }
};
