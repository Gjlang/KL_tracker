<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('outdoor_monthly_details', function (Blueprint $table) {
            // Drop the old unique constraint
            $table->dropUnique('outdoor_unique_slot');

            // Add the correct unique constraint including outdoor_item_id
            $table->unique(
                ['master_file_id', 'outdoor_item_id', 'year', 'month', 'field_key'],
                'outdoor_unique_slot'
            );
        });
    }

    public function down()
    {
        Schema::table('outdoor_monthly_details', function (Blueprint $table) {
            // Drop the new constraint
            $table->dropUnique('outdoor_unique_slot');

            // Restore the old constraint (without outdoor_item_id)
            $table->unique(
                ['master_file_id', 'year', 'month', 'field_key'],
                'outdoor_unique_slot'
            );
        });
    }
};
