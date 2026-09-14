<?php

use Illuminate\Database\Migrations\Migration;
use MongoDB\Laravel\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("users", function (Blueprint $collection) {
            $collection->unique("email");
            $collection->index("userType");
        });

        Schema::create("password_reset_tokens", function (Blueprint $collection) {
            $collection->unique("email");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("users");
        Schema::dropIfExists("password_reset_tokens");
    }
};
