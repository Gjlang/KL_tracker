<?php

// database/migrations/2025_09_09_000001_fix_outdoor_unique_slot.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('outdoor_monthly_details', function (Blueprint $table) {
            // hapus index lama
            $table->dropUnique('outdoor_unique_slot');
            // buat index baru: termasuk outdoor_item_id
            $table->unique(
                ['master_file_id', 'outdoor_item_id', 'year', 'month', 'field_key'],
                'outdoor_unique_slot'
            );
        });
    }

    public function down(): void
    {
        Schema::table('outdoor_monthly_details', function (Blueprint $table) {
            $table->dropUnique('outdoor_unique_slot');
            $table->unique(
                ['master_file_id', 'year', 'month', 'field_key'],
                'outdoor_unique_slot'
            );
        });
    }
};

