<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('media_coordinator_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_file_id')->constrained('master_files')->cascadeOnDelete();

            // optional snapshots from master_files at creation time
            $table->string('date_in_snapshot')->nullable();    // (you can set from master_files.date)
            $table->string('company_snapshot')->nullable();    // (master_files.company)

            // manual fields
            $table->string('title')->nullable();
            $table->string('client_bp')->nullable();
            $table->string('x')->nullable();
            $table->string('material_reminder')->nullable();
            $table->string('material_received')->nullable();
            $table->string('video_done')->nullable();
            $table->string('video_approval')->nullable();
            $table->string('video_approved')->nullable();
            $table->string('video_scheduled')->nullable();
            $table->date('video_posted')->nullable();
            $table->string('post_link')->nullable();

            $table->timestamps();

            $table->unique('master_file_id'); // prevent duplicates
            $table->index(['created_at']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('media_coordinator_trackings');
    }
};
