<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('master_files', function (Blueprint $table) {
            $months = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];
            foreach ($months as $m) {
                if (!Schema::hasColumn('master_files', "check_$m")) {
                    $table->string("check_$m")->nullable();
                }
            }

            if (!Schema::hasColumn('master_files', 'remarks')) {
                $table->text('remarks')->nullable();
            }
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_files', function (Blueprint $table) {
            //
        });
    }
};
