<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::table('master_files', function (Blueprint $table) {
        $table->string('product_category')->nullable()->after('product');
        });

    }

    public function down()
    {
        Schema::table('master_files', function (Blueprint $table) {
            $table->dropColumn('product_category');
        });
    }

};
