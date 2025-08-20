<?php
// Create this migration file: database/migrations/xxxx_xx_xx_xxxxxx_fix_kltg_coordinator_date_columns.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kltg_coordinator_lists', function (Blueprint $table) {
            // Change date columns from varchar to date
            $table->date('artwork_reminder')->nullable()->change();
            $table->date('material_record')->nullable()->change();
            $table->date('send_chop_sign')->nullable()->change();
            $table->date('chop_sign_approval')->nullable()->change();

            // Also fix the x column to be boolean
            $table->boolean('x')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kltg_coordinator_lists', function (Blueprint $table) {
            // Revert back to varchar if needed
            $table->string('artwork_reminder')->nullable()->change();
            $table->string('material_record')->nullable()->change();
            $table->string('send_chop_sign')->nullable()->change();
            $table->string('chop_sign_approval')->nullable()->change();
            $table->string('x')->nullable()->change();
        });
    }
};
