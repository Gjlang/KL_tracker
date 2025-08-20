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
       Schema::create('kltg_monthly_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_file_id')->constrained()->onDelete('cascade');
            $table->string('client');
            $table->string('month'); // Jan, Feb, etc.
            $table->string('type');  // KLTG, VIDEO, ARTICLE, LB, EM
            $table->string('status')->nullable(); // 2X, TBD, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kltg_monthly_details');
    }
};
