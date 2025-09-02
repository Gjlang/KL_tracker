<?php

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
        if (!Schema::hasColumn('kltg_coordinator_lists', 'artwork_done')) {
            $table->date('artwork_done')->nullable()->after('material_record');
        }
    });
}

public function down(): void
{
    Schema::table('kltg_coordinator_lists', function (Blueprint $table) {
        if (Schema::hasColumn('kltg_coordinator_lists', 'artwork_done')) {
            $table->dropColumn('artwork_done');
        }
    });
}

};
