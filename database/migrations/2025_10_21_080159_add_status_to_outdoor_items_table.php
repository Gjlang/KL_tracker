<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('outdoor_whiteboards', 'outdoor_item_id')) {
            // Column already exists — do nothing
            return;
        }

        Schema::table('outdoor_whiteboards', function (Blueprint $table) {
            $table->unsignedBigInteger('outdoor_item_id')->nullable()->after('master_file_id');
            $table->unique('outdoor_item_id');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('outdoor_whiteboards', 'outdoor_item_id')) {
            Schema::table('outdoor_whiteboards', function (Blueprint $table) {
                $table->dropUnique(['outdoor_item_id']);
                $table->dropColumn('outdoor_item_id');
            });
        }
    }
};

