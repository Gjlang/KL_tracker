<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('outdoor_monthly_details', function (Blueprint $table) {
        $table->unsignedBigInteger('outdoor_item_id')->nullable()->after('master_file_id')->index();
    });
}

public function down()
{
    Schema::table('outdoor_monthly_details', function (Blueprint $table) {
        $table->dropColumn('outdoor_item_id');
    });
}

};
