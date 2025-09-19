<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('outdoor_whiteboards', function (Blueprint $t) {
            // ensure no NULLs exist BEFORE running this (see cleanup step)
            $t->unsignedBigInteger('outdoor_item_id')->nullable(false)->change();
            // you already have UNIQUE + FK; keeping them as-is
        });
    }

    public function down(): void {
        Schema::table('outdoor_whiteboards', function (Blueprint $t) {
            $t->unsignedBigInteger('outdoor_item_id')->nullable()->change();
        });
    }
};
