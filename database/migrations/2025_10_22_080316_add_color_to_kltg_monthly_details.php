<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
{
    Schema::table('outdoor_whiteboards', function (Blueprint $table) {
        if (!Schema::hasColumn('outdoor_whiteboards', 'outdoor_item_id')) {
            $table->unsignedBigInteger('outdoor_item_id')->nullable()->after('master_file_id');
            // If you plan to add a FK, do it here too (inside the guard):
            // $table->foreign('outdoor_item_id')->references('id')->on('outdoor_items')->nullOnDelete();
        }
    });
}

public function down(): void
{
    Schema::table('outdoor_whiteboards', function (Blueprint $table) {
        if (Schema::hasColumn('outdoor_whiteboards', 'outdoor_item_id')) {
            // If you created a FK above, drop it first (name may vary, adjust if needed):
            // if (Schema::hasColumn('outdoor_whiteboards', 'outdoor_item_id')) {
            //     $table->dropForeign(['outdoor_item_id']);
            // }
            $table->dropColumn('outdoor_item_id');
        }
    });
}

};
