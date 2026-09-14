<?php

use Illuminate\Database\Migrations\Migration;
use MongoDB\Laravel\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection("mongodb")->create("sells", function (Blueprint $collection) {
            $collection->index("bookId");
            $collection->index("userId");
        });
    }

    public function down(): void
    {
        Schema::connection("mongodb")->dropIfExists("sells");
    }
};
