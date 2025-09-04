<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) Content Calendar
        if (!Schema::hasTable('media_content_calendars')) {
            Schema::create('media_content_calendars', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('master_file_id');
                $table->unsignedSmallInteger('year');
                $table->unsignedTinyInteger('month');
                $table->date('total_artwork_date')->nullable();
                $table->date('pending_date')->nullable();
                $table->unsignedSmallInteger('draft_wa')->nullable();
                $table->unsignedSmallInteger('approved')->nullable();
                $table->text('remarks')->nullable();
                $table->timestamps();

                $table->foreign('master_file_id')->references('id')->on('master_files')->onDelete('cascade');
                $table->unique(['master_file_id', 'year', 'month']);
                $table->index(['year', 'month']);
                $table->index('master_file_id');
            });
        }

        // 2) Artwork Editing
        if (!Schema::hasTable('media_artwork_editings')) {
            Schema::create('media_artwork_editings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('master_file_id');
                $table->unsignedSmallInteger('year');
                $table->unsignedTinyInteger('month');
                $table->date('total_artwork_date')->nullable();
                $table->date('pending_date')->nullable();
                $table->unsignedSmallInteger('draft_wa')->nullable();
                $table->unsignedSmallInteger('approved')->nullable();
                $table->text('remarks')->nullable();
                $table->timestamps();

                $table->foreign('master_file_id')->references('id')->on('master_files')->onDelete('cascade');
                $table->unique(['master_file_id', 'year', 'month']);
                $table->index(['year', 'month']);
                $table->index('master_file_id');
            });
        }

        // 3) Posting Scheduling
        if (!Schema::hasTable('media_posting_schedulings')) {
            Schema::create('media_posting_schedulings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('master_file_id');
                $table->unsignedSmallInteger('year');
                $table->unsignedTinyInteger('month');
                $table->date('total_artwork_date')->nullable();
                $table->date('crm_date')->nullable();
                $table->date('meta_ads_manager_date')->nullable();
                $table->unsignedSmallInteger('tiktok_ig_draft')->nullable();
                $table->text('remarks')->nullable();
                $table->timestamps();

                $table->foreign('master_file_id')->references('id')->on('master_files')->onDelete('cascade');
                $table->unique(['master_file_id', 'year', 'month']);
                $table->index(['year', 'month']);
                $table->index('master_file_id');
            });
        }

        // 4) Report
        if (!Schema::hasTable('media_reports')) {
            Schema::create('media_reports', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('master_file_id');
                $table->unsignedSmallInteger('year');
                $table->unsignedTinyInteger('month');
                $table->date('pending_date')->nullable();
                $table->date('completed_date')->nullable();
                $table->text('remarks')->nullable();
                $table->timestamps();

                $table->foreign('master_file_id')->references('id')->on('master_files')->onDelete('cascade');
                $table->unique(['master_file_id', 'year', 'month']);
                $table->index(['year', 'month']);
                $table->index('master_file_id');
            });
        }

        // 5) Value Add (Social Media only)
        if (!Schema::hasTable('media_value_adds')) {
            Schema::create('media_value_adds', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('master_file_id');
                $table->unsignedSmallInteger('year');
                $table->unsignedTinyInteger('month');
                $table->string('quota', 255)->nullable();
                $table->unsignedSmallInteger('completed')->nullable();
                $table->text('remarks')->nullable();
                $table->timestamps();

                $table->foreign('master_file_id')->references('id')->on('master_files')->onDelete('cascade');
                $table->unique(['master_file_id', 'year', 'month']);
                $table->index(['year', 'month']);
                $table->index('master_file_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('media_value_adds');
        Schema::dropIfExists('media_reports');
        Schema::dropIfExists('media_posting_schedulings');
        Schema::dropIfExists('media_artwork_editings');
        Schema::dropIfExists('media_content_calendars');
    }
};
