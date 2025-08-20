<?php

// database/migrations/2025_08_17_000000_alter_x_to_string_on_kltg_coordinator_lists.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('kltg_coordinator_lists', function (Blueprint $table) {
            // Change to string; adjust length if needed
            $table->string('x', 500)->nullable()->change();
        });
    }
    public function down(): void {
        Schema::table('kltg_coordinator_lists', function (Blueprint $table) {
            $table->tinyInteger('x')->nullable()->change();
        });
    }
};
