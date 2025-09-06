<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('master_files', function (Blueprint $table) {
        // place near other contact-ish fields; adjust "after" if needed
        $table->string('sales_person', 255)->nullable()->after('client');
    });
}

public function down(): void
{
    Schema::table('master_files', function (Blueprint $table) {
        $table->dropColumn('sales_person');
    });
}

};
