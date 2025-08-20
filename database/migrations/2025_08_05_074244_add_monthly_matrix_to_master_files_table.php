<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('master_files', function (Blueprint $table) {
            foreach (['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'] as $month) {
                foreach (['kltg', 'video', 'article', 'lb', 'em'] as $type) {
                    $table->boolean("check_{$month}_{$type}")->default(false);
                }
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
