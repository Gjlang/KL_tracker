<?php

// database/migrations/2025_08_21_000000_create_media_coordinator_tabs.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Helper to DRY common columns
        $common = function (Blueprint $t) {
            $t->id();
            $t->foreignId('master_file_id')->constrained('master_files')->cascadeOnDelete();
            $t->smallInteger('year');    // e.g., 2025
            $t->tinyInteger('month');    // 1..12
            $t->timestamps();
            $t->unique(['master_file_id', 'year', 'month']); // one row per (company, year, month)
        };

        Schema::create('content_calendars', function (Blueprint $t) use ($common) {
            $common($t);
            $t->string('total_artwork')->nullable();
            $t->string('pending')->nullable();
            $t->boolean('draft_wa')->default(false);
            $t->boolean('approved')->default(false);
        });

        Schema::create('artwork_editings', function (Blueprint $t) use ($common) {
            $common($t);
            $t->string('total_artwork')->nullable();
            $t->string('pending')->nullable();
            $t->boolean('draft_wa')->default(false);
            $t->boolean('approved')->default(false);
        });

        Schema::create('posting_schedulings', function (Blueprint $t) use ($common) {
            $common($t);
            $t->string('total_artwork')->nullable();
            $t->string('crm')->nullable();
            $t->string('meta_mgr')->nullable();
            $t->boolean('tiktok_ig_draft')->default(false);
        });

        Schema::create('media_reports', function (Blueprint $t) use ($common) {
            $common($t);
            $t->string('pending')->nullable();
            $t->boolean('completed')->default(false);
        });

        Schema::create('media_value_adds', function (Blueprint $t) use ($common) {
            $common($t);
            $t->string('quota')->nullable();
            $t->integer('completed')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_value_adds');
        Schema::dropIfExists('media_reports');
        Schema::dropIfExists('posting_schedulings');
        Schema::dropIfExists('artwork_editings');
        Schema::dropIfExists('content_calendars');
    }
};
