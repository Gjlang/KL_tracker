<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('outdoor_monthly_details', function (Blueprint $t) {
            $t->unique(
                ['master_file_id','outdoor_item_id','year','month','field_key'],
                'omd_mf_item_year_month_field_unique'
            );
            $t->index(['outdoor_item_id']);
            $t->index(['year','month']);
        });
    }

    public function down(): void
    {
        Schema::table('outdoor_monthly_details', function (Blueprint $t) {
            $t->dropUnique('omd_mf_item_year_month_field_unique');
        });
    }
};
