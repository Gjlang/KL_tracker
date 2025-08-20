<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kltg_coordinator_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_file_id')->constrained('master_files')->cascadeOnDelete();

            // required selector for each tab
            $table->enum('subcategory', ['print','video','article','lb','em']);

            // common text fields
            $table->string('title')->nullable();
            $table->string('client_bp')->nullable();
            $table->string('x')->nullable();
            $table->string('material_reminder_text')->nullable();
            $table->string('post_link')->nullable();

            // dates used across variants
            $table->date('material_received_date')->nullable();

            // video fields
            $table->date('video_done_date')->nullable();
            $table->date('pending_approval_date')->nullable();
            $table->date('video_approved_date')->nullable();
            $table->date('video_scheduled_date')->nullable();
            $table->date('video_posted_date')->nullable();

            // article fields
            $table->date('article_done_date')->nullable();
            $table->date('article_approved_date')->nullable();
            $table->date('article_scheduled_date')->nullable();
            $table->date('article_posted_date')->nullable();

            // print artwork fields
            $table->string('edition')->nullable();
            $table->string('publication')->nullable();
            $table->string('artwork_party')->nullable(); // "BP" or "Client"
            $table->date('artwork_reminder_date')->nullable();
            $table->date('artwork_done_date')->nullable();
            $table->date('send_chop_sign_date')->nullable();
            $table->date('chop_sign_approval_date')->nullable();
            $table->date('park_in_server_date')->nullable();

            // EM fields
            $table->date('em_date_write')->nullable();
            $table->date('em_date_to_post')->nullable();
            $table->date('em_post_date')->nullable();
            $table->string('em_qty')->nullable();
            $table->string('blog_link')->nullable();

            $table->timestamps();

            $table->unique(['master_file_id', 'subcategory'], 'uniq_master_subcat');
            $table->index('subcategory');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kltg_coordinator_trackings');
    }
};
