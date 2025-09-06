<?php

// database/migrations/****_add_amount_to_master_files_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('master_files', function (Blueprint $table) {
            $table->decimal('amount', 12, 2)->nullable()->after('duration'); // MYR, nullable
        });
    }
    public function down(): void {
        Schema::table('master_files', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
    }
};
