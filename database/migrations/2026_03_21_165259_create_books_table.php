<?php

use Illuminate\Database\Migrations\Migration;
use MongoDB\Laravel\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection("mongodb")->create("books", function (Blueprint $collection) {
            $collection->index("title");
            $collection->index("author");
            $collection->index("genre");
        });
    }

    public function down(): void
    {
        Schema::connection("mongodb")->dropIfExists("books");
    }
};
