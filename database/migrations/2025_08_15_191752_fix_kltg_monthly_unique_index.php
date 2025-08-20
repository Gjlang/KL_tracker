<?php

// php artisan make:migration fix_kltg_monthly_unique_index

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('kltg_monthly_details', function (Blueprint $table) {
            // rename this to match your current unique name if different
            $table->dropUnique('kltg_mf_year_month_cat_unique');

            $table->unique(
                ['master_file_id','year','month','category','field_type'],
                'kltg_mf_year_month_cat_field_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('kltg_monthly_details', function (Blueprint $table) {
            $table->dropUnique('kltg_mf_year_month_cat_field_unique');
            $table->unique(
                ['master_file_id','year','month','category'],
                'kltg_mf_year_month_cat_unique'
            );
        });
    }
};
