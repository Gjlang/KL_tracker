<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('outdoor_coordinator_trackings', function (Blueprint $table) {
            $table->timestamp('masterfile_created_at')->nullable()->after('master_file_id');
        });
    }

    public function down(): void {
        Schema::table('outdoor_coordinator_trackings', function (Blueprint $table) {
            $table->dropColumn('masterfile_created_at');
        });
    }
};
