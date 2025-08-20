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
        Schema::create('media_ongoing_jobs', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('company');
            $table->string('product');
            $table->string('category')->nullable();
            $table->string('location')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // Monthly progress columns
            $table->string('jan', 50)->nullable();
            $table->string('feb', 50)->nullable();
            $table->string('mar', 50)->nullable();
            $table->string('apr', 50)->nullable();
            $table->string('may', 50)->nullable();
            $table->string('jun', 50)->nullable();
            $table->string('jul', 50)->nullable();
            $table->string('aug', 50)->nullable();
            $table->string('sep', 50)->nullable();
            $table->string('oct', 50)->nullable();
            $table->string('nov', 50)->nullable();
            $table->string('dec', 50)->nullable();

            $table->text('remarks')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('date');
            $table->index('company');
            $table->index('product');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_ongoing_jobs');
    }
};
