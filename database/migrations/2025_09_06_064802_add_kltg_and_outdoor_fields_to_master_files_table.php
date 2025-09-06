<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('master_files', function (Blueprint $table) {
            // KLTG-only
            $table->string('kltg_industry')->nullable();
            $table->string('kltg_x')->nullable();                // “X”
            $table->string('kltg_edition')->nullable();
            $table->string('kltg_material_cbp')->nullable();     // “Material C/BP”
            $table->string('kltg_print')->nullable();
            $table->string('kltg_article')->nullable();
            $table->string('kltg_video')->nullable();
            $table->string('kltg_leaderboard')->nullable();
            $table->string('kltg_qr_code')->nullable();
            $table->string('kltg_blog')->nullable();
            $table->string('kltg_em')->nullable();
            $table->string('kltg_remarks')->nullable();          // keep separate from global remarks

            // Outdoor-only
            // location already exists
            $table->string('outdoor_size')->nullable();
            $table->string('outdoor_district_council')->nullable();
            $table->string('outdoor_coordinates')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('master_files', function (Blueprint $table) {
            $table->dropColumn([
                'kltg_industry','kltg_x','kltg_edition','kltg_material_cbp','kltg_print',
                'kltg_article','kltg_video','kltg_leaderboard','kltg_qr_code','kltg_blog',
                'kltg_em','kltg_remarks',
                'outdoor_size','outdoor_district_council','outdoor_coordinates',
            ]);
        });
    }
};
