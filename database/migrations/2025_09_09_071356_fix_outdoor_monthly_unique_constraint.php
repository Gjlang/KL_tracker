<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Step 1: Add outdoor_item_id column kalau belum ada
        Schema::table('outdoor_monthly_details', function (Blueprint $table) {
            if (!Schema::hasColumn('outdoor_monthly_details', 'outdoor_item_id')) {
                $table->unsignedBigInteger('outdoor_item_id')->nullable()->after('master_file_id');
            }
        });

        // Step 2: Drop foreign key pada master_file_id dulu
        Schema::table('outdoor_monthly_details', function (Blueprint $table) {
            $table->dropForeign(['master_file_id']);
        });

        // Step 3: Drop old unique constraint
        DB::statement('ALTER TABLE outdoor_monthly_details DROP INDEX outdoor_unique_slot');

        // Step 4: Add new unique constraint with outdoor_item_id
        Schema::table('outdoor_monthly_details', function (Blueprint $table) {
            $table->unique(
                ['master_file_id', 'outdoor_item_id', 'year', 'month', 'field_key'],
                'outdoor_unique_slot'
            );
        });

        // Step 5: Recreate foreign key
        Schema::table('outdoor_monthly_details', function (Blueprint $table) {
            $table->foreign('master_file_id')
                  ->references('id')
                  ->on('master_files')
                  ->cascadeOnDelete();

            // Add foreign key untuk outdoor_item_id juga kalau perlu
            $table->foreign('outdoor_item_id')
                  ->references('id')
                  ->on('outdoor_items')
                  ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('outdoor_monthly_details', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['master_file_id']);
            $table->dropForeign(['outdoor_item_id']);
        });

        DB::statement('ALTER TABLE outdoor_monthly_details DROP INDEX outdoor_unique_slot');

        // Restore old constraint
        Schema::table('outdoor_monthly_details', function (Blueprint $table) {
            $table->unique(
                ['master_file_id', 'year', 'month', 'field_key'],
                'outdoor_unique_slot'
            );

            // Recreate master_file_id foreign key
            $table->foreign('master_file_id')
                  ->references('id')
                  ->on('master_files')
                  ->cascadeOnDelete();
        });

        // Remove outdoor_item_id column
        Schema::table('outdoor_monthly_details', function (Blueprint $table) {
            $table->dropColumn('outdoor_item_id');
        });
    }
};
