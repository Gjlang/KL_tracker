<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('kltg_monthly_details')) {
            Schema::create('kltg_monthly_details', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('master_file_id')->index();
                $t->integer('year')->index();
                $t->unsignedTinyInteger('month')->nullable()->index();
                $t->string('category')->nullable()->index();
                $t->string('value_text')->nullable();
                $t->date('value_date')->nullable();
                $t->boolean('is_date')->default(false);
                $t->timestamps();

                $t->unique(['master_file_id','year','month','category'], 'kltg_mf_year_month_cat_unique');
            });
        } else {
            Schema::table('kltg_monthly_details', function (Blueprint $t) {
                if (!Schema::hasColumn('kltg_monthly_details','year')) $t->integer('year')->after('master_file_id')->index();
                if (!Schema::hasColumn('kltg_monthly_details','month')) $t->unsignedTinyInteger('month')->nullable()->after('year')->index();
                if (!Schema::hasColumn('kltg_monthly_details','category')) $t->string('category')->nullable()->after('month')->index();
                if (!Schema::hasColumn('kltg_monthly_details','value_text')) $t->string('value_text')->nullable()->after('category');
                if (!Schema::hasColumn('kltg_monthly_details','value_date')) $t->date('value_date')->nullable()->after('value_text');
                if (!Schema::hasColumn('kltg_monthly_details','is_date')) $t->boolean('is_date')->default(false)->after('value_date');
            });

            $indexes = collect(DB::select("SHOW INDEX FROM kltg_monthly_details"))->pluck('Key_name');
            if (!$indexes->contains('kltg_mf_year_month_cat_unique')) {
                Schema::table('kltg_monthly_details', function (Blueprint $t) {
                    $t->unique(['master_file_id','year','month','category'], 'kltg_mf_year_month_cat_unique');
                });
            }
        }
    }

    public function down(): void
    {
        Schema::table('kltg_monthly_details', function (Blueprint $t) {
            if (Schema::hasColumn('kltg_monthly_details','year') &&
                Schema::hasColumn('kltg_monthly_details','month') &&
                Schema::hasColumn('kltg_monthly_details','category')) {
                $t->dropUnique('kltg_mf_year_month_cat_unique');
            }
        });
    }
};
