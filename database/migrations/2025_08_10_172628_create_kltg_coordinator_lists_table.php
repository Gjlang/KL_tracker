<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if table exists and add missing columns
        if (Schema::hasTable('kltg_coordinator_lists')) {
            Schema::table('kltg_coordinator_lists', function (Blueprint $table) {
                // Add columns that are missing from the existing table
                if (!Schema::hasColumn('kltg_coordinator_lists', 'master_file_id')) {
                    $table->foreignId('master_file_id')->nullable()->constrained('master_files')->onDelete('cascade');
                }
                if (!Schema::hasColumn('kltg_coordinator_lists', 'company_snapshot')) {
                    $table->string('company_snapshot')->nullable();
                }
                if (!Schema::hasColumn('kltg_coordinator_lists', 'title_snapshot')) {
                    $table->string('title_snapshot')->nullable();
                }
                if (!Schema::hasColumn('kltg_coordinator_lists', 'x')) {
                    $table->string('x')->nullable();
                }
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
                if (!Schema::hasColumn('kltg_coordinator_lists', 'chop_sign_approval')) {
                    $table->string('chop_sign_approval')->nullable();
                }
                if (!Schema::hasColumn('kltg_coordinator_lists', 'park_in_file_server')) {
                    $table->string('park_in_file_server')->nullable();
                }
            });

            // Add unique constraint using DB facade instead of Doctrine
            if (Schema::hasColumn('kltg_coordinator_lists', 'master_file_id')) {
                // Check if unique constraint already exists by trying to create it
                try {
                    DB::statement('ALTER TABLE `kltg_coordinator_lists` ADD UNIQUE KEY `kltg_coordinator_lists_master_file_id_unique` (`master_file_id`)');
                } catch (\Exception $e) {
                    // Unique constraint might already exist, that's okay
                    // Or master_file_id column might not exist yet
                }
            }
        } else {
            // Create the table from scratch if it doesn't exist
            Schema::create('kltg_coordinator_lists', function (Blueprint $table) {
                $table->id();
                $table->foreignId('master_file_id')->constrained('master_files')->onDelete('cascade');
                $table->string('company_snapshot')->nullable();
                $table->string('title_snapshot')->nullable();
                $table->string('x')->nullable();
                $table->string('edition')->nullable();
                $table->string('publication')->nullable();
                $table->string('artwork_bp_client')->nullable();
                $table->string('artwork_reminder')->nullable();
                $table->string('material_record')->nullable();
                $table->string('send_chop_sign')->nullable();
                $table->string('chop_sign_approval')->nullable();
                $table->string('park_in_file_server')->nullable();
                $table->timestamps();
                $table->unique('master_file_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop columns that we added, don't drop the whole table
        if (Schema::hasTable('kltg_coordinator_lists')) {
            Schema::table('kltg_coordinator_lists', function (Blueprint $table) {
                $columnsToCheck = [
                    'master_file_id', 'company_snapshot', 'title_snapshot', 'x', 'edition',
                    'publication', 'artwork_bp_client', 'artwork_reminder', 'material_record',
                    'send_chop_sign', 'chop_sign_approval', 'park_in_file_server'
                ];

                foreach ($columnsToCheck as $column) {
                    if (Schema::hasColumn('kltg_coordinator_lists', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
