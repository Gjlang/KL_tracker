<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('kltg_monthly_details', function (Blueprint $table) {
            // Hapus index lama (lihat dari error log: kltg_mf_year_month_cat_ft_unique)
            $table->dropUnique('kltg_mf_year_month_cat_ft_unique');

            // Tambah index unik baru yg include TYPE
            $table->unique(
                ['master_file_id','year','month','category','type','field_type'],
                'kltg_mf_year_month_cat_type_ft_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('kltg_monthly_details', function (Blueprint $table) {
            $table->dropUnique('kltg_mf_year_month_cat_type_ft_unique');

            $table->unique(
                ['master_file_id','year','month','category','field_type'],
                'kltg_mf_year_month_cat_ft_unique'
            );
        });
    }
};
