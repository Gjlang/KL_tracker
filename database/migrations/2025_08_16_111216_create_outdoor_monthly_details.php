<?php

// database/migrations/2025_08_16_000000_create_outdoor_monthly_details.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('outdoor_monthly_details', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('master_file_id');
            $t->integer('year');           // 2000..2100
            $t->tinyInteger('month');      // 1..12
            $t->string('field_key', 64);   // e.g. INSTALLED_ON, REMARK
            $t->enum('field_type', ['text','date']);
            $t->text('value_text')->nullable();
            $t->date('value_date')->nullable();
            $t->timestamps();

            $t->unique(['master_file_id','year','month','field_key'], 'outdoor_unique_slot');
            $t->foreign('master_file_id')->references('id')->on('master_files')->cascadeOnDelete();
        });
    }
    public function down(): void {
        Schema::dropIfExists('outdoor_monthly_details');
    }
};
