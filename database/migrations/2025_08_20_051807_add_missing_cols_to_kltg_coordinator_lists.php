<?php

// database/migrations/2025_08_20_000001_add_missing_cols_to_kltg_coordinator_lists.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('kltg_coordinator_lists', function (Blueprint $t) {
            // common text fields
            if (!Schema::hasColumn('kltg_coordinator_lists','client_bp')) $t->string('client_bp')->nullable()->after('company_snapshot');
            if (!Schema::hasColumn('kltg_coordinator_lists','material_reminder_text')) $t->string('material_reminder_text')->nullable()->after('client_bp');
            if (!Schema::hasColumn('kltg_coordinator_lists','post_link')) $t->string('post_link')->nullable();

            // EM fields
            if (!Schema::hasColumn('kltg_coordinator_lists','em_date_write'))   $t->date('em_date_write')->nullable();
            if (!Schema::hasColumn('kltg_coordinator_lists','em_date_to_post')) $t->date('em_date_to_post')->nullable();
            if (!Schema::hasColumn('kltg_coordinator_lists','em_post_date'))    $t->date('em_post_date')->nullable();
            if (!Schema::hasColumn('kltg_coordinator_lists','em_qty'))          $t->unsignedInteger('em_qty')->nullable();
            if (!Schema::hasColumn('kltg_coordinator_lists','blog_link'))       $t->string('blog_link')->nullable();

            // Video / Article / LB date fields
            foreach ([
                'video_done','pending_approval','video_approved','video_scheduled','video_posted',
                'article_done','article_approved','article_scheduled','article_posted',
            ] as $col) {
                if (!Schema::hasColumn('kltg_coordinator_lists',$col)) $t->date($col)->nullable();
            }
        });
    }

    public function down(): void {
        Schema::table('kltg_coordinator_lists', function (Blueprint $t) {
            $t->dropColumn([
                'client_bp','material_reminder_text','post_link',
                'em_date_write','em_date_to_post','em_post_date','em_qty','blog_link',
                'video_done','pending_approval','video_approved','video_scheduled','video_posted',
                'article_done','article_approved','article_scheduled','article_posted',
            ]);
        });
    }
};
