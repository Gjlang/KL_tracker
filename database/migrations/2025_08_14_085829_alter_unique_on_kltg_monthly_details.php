<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $table = 'kltg_monthly_details';
        $new   = 'kltg_mf_year_month_cat_ft_unique'; // sudah ada di DB-mu (sesuai screenshot)
        $oldCandidates = [
            // kemungkinan nama lama kalau pernah dibuat tanpa field_type
            'kltg_monthly_details_master_file_id_year_month_category_unique',
            'kltg_monthly_details_master_year_month_category_unique',
        ];

        // Jika unique baru SUDAH ada, tidak perlu apa-apa
        $hasNew = DB::table('information_schema.statistics')
            ->whereRaw('table_schema = DATABASE()')
            ->where('table_name', $table)
            ->where('index_name', $new)
            ->exists();

        if ($hasNew) {
            return; // no-op, supaya migrate tidak error
        }

        // Jika ada unique lama, drop dulu
        $oldExisting = DB::table('information_schema.statistics')
            ->select('index_name')
            ->whereRaw('table_schema = DATABASE()')
            ->where('table_name', $table)
            ->whereIn('index_name', $oldCandidates)
            ->distinct()
            ->pluck('index_name')
            ->all();

        if (!empty($oldExisting)) {
            Schema::table($table, function (Blueprint $table) use ($oldExisting) {
                foreach ($oldExisting as $idx) {
                    $table->dropUnique($idx);
                }
            });
        }

        // Tambah unique baru (kalau belum ada)
        Schema::table($table, function (Blueprint $table) use ($new) {
            $table->unique(
                ['master_file_id','year','month','category','field_type'],
                $new
            );
        });
    }

    public function down(): void
    {
        $table = 'kltg_monthly_details';
        $new   = 'kltg_mf_year_month_cat_ft_unique';

        Schema::table($table, function (Blueprint $table) use ($new) {
            // hapus unique yang pakai field_type
            $table->dropUnique($new);
            // opsional: hidupkan lagi unique lama tanpa field_type
            $table->unique(
                ['master_file_id','year','month','category'],
                'kltg_monthly_details_master_file_id_year_month_category_unique'
            );
        });
    }
};

