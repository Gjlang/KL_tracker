<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Drop the old unique index (name may vary). Try the expected name first.
        // If you used a different name previously, see the notes below to adjust.
        try {
            Schema::table('kltg_monthly_details', function (Blueprint $table) {
                $table->dropUnique('kltg_mf_year_month_cat_unique');
            });
        } catch (\Throwable $e) {
            // Ignore if it doesn't exist; we'll handle alternate names below.
        }

        // Some environments auto-name indexes; drop by auto name if needed:
        // Find your actual index name with: SHOW INDEX FROM kltg_monthly_details;
        // Example fallback (uncomment and replace the name if needed):
        // DB::statement('ALTER TABLE kltg_monthly_details DROP INDEX kltg_monthly_details_master_file_id_year_month_category_unique');

        // 2) Create the correct unique index including field_type
        Schema::table('kltg_monthly_details', function (Blueprint $table) {
            $table->unique(
                ['master_file_id', 'year', 'month', 'category', 'field_type'],
                'kltg_mf_year_month_cat_ft_unique'
            );
        });
    }

    public function down(): void
    {
        // Revert: drop the new unique and (optionally) recreate the old one
        Schema::table('kltg_monthly_details', function (Blueprint $table) {
            try {
                $table->dropUnique('kltg_mf_year_month_cat_ft_unique');
            } catch (\Throwable $e) {
                // ignore
            }

            $table->unique(
                ['master_file_id', 'year', 'month', 'category'],
                'kltg_mf_year_month_cat_unique'
            );
        });
    }
};
