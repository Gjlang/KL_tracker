<?php

// database/migrations/xxxx_xx_xx_xxxxxx_alter_master_files_make_duration_nullable.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('master_files', function (Blueprint $table) {
            $table->string('duration')->nullable()->change();
        });
    }

    public function down(): void {
        Schema::table('master_files', function (Blueprint $table) {
            $table->string('duration')->nullable(false)->default(''); // or remove default if you prefer
        });
    }
};

