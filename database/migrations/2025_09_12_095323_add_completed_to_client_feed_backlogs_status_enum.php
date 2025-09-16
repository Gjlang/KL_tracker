<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
            ALTER TABLE client_feed_backlogs
            MODIFY status ENUM('pending','in-progress','done','cancelled','completed')
            NOT NULL DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        // Rollback ke list semula (hati-hati: data 'completed' akan error kalau masih ada)
        DB::statement("
            ALTER TABLE client_feed_backlogs
            MODIFY status ENUM('pending','in-progress','done','cancelled')
            NOT NULL DEFAULT 'pending'
        ");
    }
};
