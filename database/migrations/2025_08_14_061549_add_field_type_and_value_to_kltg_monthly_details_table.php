<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kltg_monthly_details', function (Blueprint $table) {
            // Add the new columns only if they don't already exist
            if (!Schema::hasColumn('kltg_monthly_details', 'field_type')) {
                $table->enum('field_type', ['text', 'date'])->default('text')->after('category');
            }
            if (!Schema::hasColumn('kltg_monthly_details', 'value')) {
                $table->string('value')->nullable()->after('field_type');
            }

            // Make sure client/type can be null to prevent insert errors
            if (Schema::hasColumn('kltg_monthly_details', 'client')) {
                $table->string('client')->nullable()->change();
            }
            if (Schema::hasColumn('kltg_monthly_details', 'type')) {
                $table->string('type')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('kltg_monthly_details', function (Blueprint $table) {
            if (Schema::hasColumn('kltg_monthly_details', 'field_type')) {
                $table->dropColumn('field_type');
            }
            if (Schema::hasColumn('kltg_monthly_details', 'value')) {
                $table->dropColumn('value');
            }
        });
    }
};
