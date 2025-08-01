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
        Schema::create('master_file_timelines', function (Blueprint $table) {
        $table->id();
        $table->foreignId('master_file_id')->constrained()->onDelete('cascade');

        $table->date('product')->nullable();
        $table->date('site')->nullable();
        $table->date('client')->nullable();
        $table->date('payment')->nullable();
        $table->date('material_received')->nullable();
        $table->date('artwork')->nullable();
        $table->date('approval')->nullable();
        $table->date('sent_to_printer')->nullable();
        $table->date('installation')->nullable();
        $table->date('dismantle')->nullable();

        $table->text('remarks')->nullable();
        $table->text('next_follow_up')->nullable();

        $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_file_timelines');
    }
};
