<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) Create table if it doesn't exist
        if (!Schema::hasTable('kltg_coordinator_lists')) {
            Schema::create('kltg_coordinator_lists', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('master_file_id');

                // one logical record per master_file_id
                $table->boolean('x')->nullable(); // allow NULL to avoid ''→int issues
                $table->string('edition')->nullable();
                $table->string('publication')->nullable();
                $table->string('artwork_bp_client')->nullable();

                // keep as string for now (you can migrate to DATE later)
                $table->string('artwork_reminder')->nullable();
                $table->string('material_record')->nullable();

                $table->string('send_chop_sign')->nullable();
                $table->string('collection_printer')->nullable();
                $table->string('sent_to_client')->nullable();
                $table->string('approved_client')->nullable();
                $table->string('sent_to_printer')->nullable();
                $table->string('printed')->nullable();
                $table->string('delivered')->nullable();

                $table->text('remarks')->nullable();

                $table->timestamps();

                $table->foreign('master_file_id')
                      ->references('id')->on('master_files')
                      ->cascadeOnDelete();

                $table->unique(['master_file_id'], 'kltg_coord_master_unique');
            });

            return; // done
        }

        // 2) Patch existing table — DBAL-free

        // Ensure columns exist
        Schema::table('kltg_coordinator_lists', function (Blueprint $table) {
            if (!Schema::hasColumn('kltg_coordinator_lists', 'edition')) {
                $table->string('edition')->nullable();
            }
            if (!Schema::hasColumn('kltg_coordinator_lists', 'publication')) {
                $table->string('publication')->nullable();
            }
            if (!Schema::hasColumn('kltg_coordinator_lists', 'artwork_bp_client')) {
                $table->string('artwork_bp_client')->nullable();
            }
            if (!Schema::hasColumn('kltg_coordinator_lists', 'artwork_reminder')) {
                $table->string('artwork_reminder')->nullable();
            }
            if (!Schema::hasColumn('kltg_coordinator_lists', 'material_record')) {
                $table->string('material_record')->nullable();
            }
            if (!Schema::hasColumn('kltg_coordinator_lists', 'send_chop_sign')) {
                $table->string('send_chop_sign')->nullable();
            }
            if (!Schema::hasColumn('kltg_coordinator_lists', 'collection_printer')) {
                $table->string('collection_printer')->nullable();
            }
            if (!Schema::hasColumn('kltg_coordinator_lists', 'sent_to_client')) {
                $table->string('sent_to_client')->nullable();
            }
            if (!Schema::hasColumn('kltg_coordinator_lists', 'approved_client')) {
                $table->string('approved_client')->nullable();
            }
            if (!Schema::hasColumn('kltg_coordinator_lists', 'sent_to_printer')) {
                $table->string('sent_to_printer')->nullable();
            }
            if (!Schema::hasColumn('kltg_coordinator_lists', 'printed')) {
                $table->string('printed')->nullable();
            }
            if (!Schema::hasColumn('kltg_coordinator_lists', 'delivered')) {
                $table->string('delivered')->nullable();
            }
            if (!Schema::hasColumn('kltg_coordinator_lists', 'remarks')) {
                $table->text('remarks')->nullable();
            }
            if (!Schema::hasColumn('kltg_coordinator_lists', 'x')) {
                $table->boolean('x')->nullable();
            }
        });

        // 3) Make sure x is NULLable (without ->change())
        // Use raw SQL to modify column only if needed
        // This is safe on MySQL 5.7/8.0; adjust tinyint length if your server differs
        $col = DB::selectOne("
            SELECT IS_NULLABLE AS is_nullable, COLUMN_TYPE AS coltype
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'kltg_coordinator_lists'
              AND COLUMN_NAME = 'x'
        ");

        if ($col && strtoupper($col->is_nullable) !== 'YES') {
            // force nullable
            DB::statement("ALTER TABLE kltg_coordinator_lists MODIFY x TINYINT(1) NULL");
        }

        // 4) Ensure unique index on master_file_id (without Doctrine)
        $uniqueExists = DB::selectOne("
            SELECT COUNT(*) AS cnt
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'kltg_coordinator_lists'
              AND INDEX_NAME = 'kltg_coord_master_unique'
        ");

        if (!$uniqueExists || (int)$uniqueExists->cnt === 0) {
            // NOTE: MySQL doesn't support IF NOT EXISTS for ADD UNIQUE on all versions
            // so we check first in INFORMATION_SCHEMA, then add
            DB::statement("ALTER TABLE kltg_coordinator_lists ADD UNIQUE INDEX kltg_coord_master_unique (master_file_id)");
        }
    }

    public function down(): void
    {
        // no-op; or drop if this was purely a create migration
        // Schema::dropIfExists('kltg_coordinator_lists');
    }
};
