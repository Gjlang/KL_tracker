<?php

// database/migrations/2025_09_16_000003_add_company_to_client_feed_backlogs.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('client_feed_backlogs', function (Blueprint $table) {
            $table->string('company')->nullable()->after('client');
            $table->index('company');
        });
    }
    public function down(): void {
        Schema::table('client_feed_backlogs', function (Blueprint $table) {
            $table->dropIndex(['company']);
            $table->dropColumn('company');
        });
    }
};
