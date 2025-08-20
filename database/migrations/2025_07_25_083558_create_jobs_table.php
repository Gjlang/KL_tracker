<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('jobs')) {
            Schema::create('jobs', function (Blueprint $table) {
                $table->id();
                $table->string('client_approval')->nullable();
                $table->string('design');
                $table->string('installation');
                $table->string('printing');
                $table->string('company_name');
                $table->string('product');
                $table->dateTime('start_date');
                $table->dateTime('end_date');
                $table->enum('status', ['pending','ongoing','completed'])->default('pending');
                $table->string('section')->default('general');
                $table->text('remarks')->nullable();
                $table->string('site_name');
                $table->integer('progress')->default(0);
                $table->string('file_path')->nullable();
                $table->unsignedBigInteger('assigned_user_id')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
        // (Opsional) kalau mau, di sini bisa tambah Schema::table() untuk menambah kolom yang
        // mungkin hilang saat upgrade versi, tapi nggak wajib untuk menyelesaikan error ini.
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
