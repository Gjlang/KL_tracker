<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('kltg_monthly_details', function (Blueprint $table) {
            // Drop the old unique index
            $table->dropUnique('kltg_mf_year_month_cat_ft_unique');

            // Add new unique index that includes "type"
            $table->unique(
                ['master_file_id', 'year', 'month', 'category', 'field_type', 'type'],
                'kltg_mf_year_month_cat_ft_type_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('kltg_monthly_details', function (Blueprint $table) {
            $table->dropUnique('kltg_mf_year_month_cat_ft_type_unique');

            // Revert back to the old unique index
            $table->unique(
                ['master_file_id', 'year', 'month', 'category', 'field_type'],
                'kltg_mf_year_month_cat_ft_unique'
            );
        });
    }
};
