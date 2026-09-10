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
        Schema::create("books", function (Blueprint $table) {
            $table->id("bookId");
            $table->string("preview_image")->nullable();
            $table->string("title");
            $table->string("author");
            $table->string("genre");
            $table->text("description")->nullable();
            $table->integer("numberOfPages");
            $table->string("language");
            $table->date("publicationDate");
            $table->string("publisher");
            $table->integer("remainingForLoan")->default(0);
            $table->integer("remainingForSell")->default(0);
            $table->string("preview")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("books");
    }
};
