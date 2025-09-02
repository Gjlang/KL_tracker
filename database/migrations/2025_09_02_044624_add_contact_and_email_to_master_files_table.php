<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('master_files', function (Blueprint $table) {
        $table->string('contact_number')->nullable();
        $table->string('email')->nullable();
    });
}

public function down()
{
    Schema::table('master_files', function (Blueprint $table) {
        $table->dropColumn(['contact_number', 'email']);
    });
}

};
