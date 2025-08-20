<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('media_ongoing_jobs', function (Blueprint $table) {
            // letakkan setelah id biar rapi
            $table->foreignId('master_file_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('master_files')
                  ->cascadeOnDelete();

            $table->index('master_file_id');
        });
    }

    public function down(): void
    {
        Schema::table('media_ongoing_jobs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('master_file_id');
        });
    }
};
