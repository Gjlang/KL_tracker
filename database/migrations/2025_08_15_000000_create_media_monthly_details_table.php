<?php

// database/migrations/2025_08_15_000000_create_media_monthly_details_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('media_monthly_details', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('master_file_id')->index();
            $t->integer('year')->index();
            $t->tinyInteger('month')->index(); // 1..12
            $t->string('subcategory'); // KLTG, Video, Article, LB, EM
            $t->text('value_text')->nullable();
            $t->date('value_date')->nullable();
            $t->timestamps();

            $t->unique(['master_file_id','year','month','subcategory'], 'uniq_media_detail');
            $t->foreign('master_file_id')->references('id')->on('master_files')->cascadeOnDelete();
        });
    }
    public function down(): void { Schema::dropIfExists('media_monthly_details'); }
};
