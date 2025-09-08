<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('client_feed_backlogs', function (Blueprint $table) {
            $table->id();

            // Optional link to an existing company/file if you want it
            $table->unsignedBigInteger('master_file_id')->nullable()->index();

            $table->date('date')->index();                       // Date
            $table->string('servicing', 255)->nullable();        // Servicing
            $table->string('product', 255)->nullable();          // Product
            $table->string('location', 255)->nullable();         // Location
            $table->string('client', 255)->index();              // Client
            $table->enum('status', ['pending','in-progress','done','cancelled'])
                  ->default('pending')->index();                 // Status
            $table->string('attended_by', 255)->nullable();      // Attend by
            $table->text('reasons')->nullable();                 // Reasons / notes

            $table->timestamps();

            // FK is optional; uncomment if you want cascade behavior
            // $table->foreign('master_file_id')->references('id')->on('master_files')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_feed_backlogs');
    }
};

