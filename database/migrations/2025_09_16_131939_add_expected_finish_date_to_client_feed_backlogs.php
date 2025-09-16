<?php

// database/migrations/2025_09_16_000001_add_expected_finish_date_to_client_feed_backlogs.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('client_feed_backlogs', function (Blueprint $table) {
            $table->date('expected_finish_date')->nullable()->after('date');
            $table->index('expected_finish_date');
        });
    }
    public function down(): void {
        Schema::table('client_feed_backlogs', function (Blueprint $table) {
            $table->dropIndex(['expected_finish_date']);
            $table->dropColumn('expected_finish_date');
        });
    }
};

