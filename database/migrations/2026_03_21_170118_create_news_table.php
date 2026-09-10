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
        Schema::create("news", function (Blueprint $table) {
            $table->id("newsId");
            $table->longText("logo")->nullable();
            $table->string("type");
            $table->string("title");
            $table->date("startDate");
            $table->date("endDate");
            $table->text("text");
            $table->string("multimedia")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("news");
    }
};
