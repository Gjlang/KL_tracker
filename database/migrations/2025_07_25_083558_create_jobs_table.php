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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('client_approval')->nullable();
            $table->string('design');
            $table->string('installation');
            $table->string('printing');
            $table->string('company_name');
            $table->string('product');
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->enum('status', ['pending', 'ongoing', 'completed'])->default('pending');
            $table->string('section')->default('general'); // Default section set to 'general'
            $table->text('remarks')->nullable();
            $table->string('site_name');
            $table->integer('progress')->default(0);
            $table->string('file_path')->nullable();
            $table->unsignedBigInteger('assigned_user_id')->nullable();
            $table->timestamps();
            $table->softDeletes(); // This adds the deleted_at column

            // Foreign key constraint
            $table->foreign('assigned_user_id')->references('id')->on('users')->onDelete('set null');

            // Indexes for better performance
            $table->index(['status', 'section']);
            $table->index(['start_date', 'end_date']);
            $table->index('assigned_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
