<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('master_files', function (Blueprint $table) {
            $table->id();
            $table->string('month');
            $table->string('date');
            $table->string('company');
            $table->string('product');
            $table->string('traffic');
            $table->string('duration');
            $table->string('status');
            $table->string('client');
            $table->date('date_finish')->nullable(); // Add date_finish column
            $table->string('job_number')->nullable(); // Add job_number column
            $table->string('artwork')->nullable(); // Add artwork column
            $table->date('invoice_date')->nullable(); // Add invoice_date column
            $table->string('invoice_number')->nullable(); // Add invoice_number column
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('master_files');
    }
};
