<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('outdoor_track_coordinator', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_file_id')->constrained('master_files')->onDelete('cascade');
            $table->string('company_snapshot')->nullable();
            $table->string('product_snapshot')->nullable();
            $table->string('site')->nullable();
            $table->string('payment')->nullable();
            $table->string('material')->nullable();
            $table->string('artwork')->nullable();
            $table->string('approval')->nullable();
            $table->string('sent')->nullable();
            $table->string('collected')->nullable();
            $table->string('install')->nullable();
            $table->string('dismantle')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

            $table->unique('master_file_id'); // no duplicates
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outdoor_track_coordinator');
    }
};
