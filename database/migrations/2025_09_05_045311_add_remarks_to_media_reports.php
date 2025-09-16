<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('media_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('media_reports', 'remarks')) {
                $table->text('remarks')->nullable()->after('completed');
            }
        });
    }
    public function down(): void {
        Schema::table('media_reports', function (Blueprint $table) {
            if (Schema::hasColumn('media_reports', 'remarks')) {
                $table->dropColumn('remarks');
            }
        });
    }
};
