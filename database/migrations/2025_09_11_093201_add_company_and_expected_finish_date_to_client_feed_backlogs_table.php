<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('client_feed_backlogs', function (Blueprint $table) {
            // Put after existing fields for clarity
            $table->date('expected_finish_date')->nullable()->after('date'); // new
            $table->string('company')->nullable()->after('client');          // new

            // optional indexes if you’ll filter by these
            $table->index('expected_finish_date', 'cfb_expected_finish_date_idx');
            $table->index('company', 'cfb_company_idx');
        });
    }

    public function down(): void
    {
        Schema::table('client_feed_backlogs', function (Blueprint $table) {
            $table->dropIndex('cfb_expected_finish_date_idx');
            $table->dropIndex('cfb_company_idx');
            $table->dropColumn(['expected_finish_date', 'company']);
        });
    }
};
