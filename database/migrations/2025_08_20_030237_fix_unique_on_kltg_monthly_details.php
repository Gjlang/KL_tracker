<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('kltg_monthly_details', function (Blueprint $table) {
            // 1) Ensure a standalone index exists for the FK on master_file_id
            //    (name it anything unique)
            $table->index('master_file_id', 'kltg_monthly_details_master_file_id_idx');
        });

        Schema::table('kltg_monthly_details', function (Blueprint $table) {
            // 2) Now it's safe to drop the old unique (FK requirement is still met)
            $table->dropUnique('kltg_mf_year_month_cat_field_unique');

            // 3) Add the correct unique including "type"
            $table->unique(
                ['master_file_id','year','month','category','type'],
                'kltg_mf_year_month_cat_type_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('kltg_monthly_details', function (Blueprint $table) {
            // Roll back to previous state
            $table->dropUnique('kltg_mf_year_month_cat_type_unique');

            $table->unique(
                ['master_file_id','year','month','category','field_type'],
                'kltg_mf_year_month_cat_field_unique'
            );

            // (optional) You can keep the standalone index; if you want to remove it:
            $table->dropIndex('kltg_monthly_details_master_file_id_idx');
        });
    }
};
