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
        Schema::create("subscriptions", function (Blueprint $table) {
            $table->id("subscriptionId");
            $table
                ->foreignId("userId")
                ->constrained("users", "userId")
                ->cascadeOnDelete();
            $table->date("startDate");
            $table->date("endDate");
            $table->integer("price");
            $table->string("accountNumber")->nullable();
            $table->timestamps();
            $table->boolean("active")->default(true);
            $table->boolean("autoRenew")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("subscriptions");
    }
};
