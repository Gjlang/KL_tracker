<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_files', function (Blueprint $table) {
            if (!Schema::hasColumn('master_files', 'barter')) {
                $table->string('barter')->nullable()->after('sales_person');
            }
        });
    }

    public function down(): void
    {
        Schema::table('master_files', function (Blueprint $table) {
            if (Schema::hasColumn('master_files', 'barter')) {
                $table->dropColumn('barter');
            }
        });
    }
};

