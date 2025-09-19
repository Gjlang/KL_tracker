<?php

// database/migrations/2025_09_19_000001_add_completed_to_outdoor_whiteboards.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('outdoor_whiteboards', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable()->after('notes');
            // Optional: quick index to filter active/completed fast
            $table->index('completed_at');
        });
    }
    public function down(): void {
        Schema::table('outdoor_whiteboards', function (Blueprint $table) {
            $table->dropIndex(['completed_at']);
            $table->dropColumn('completed_at');
        });
    }
};

