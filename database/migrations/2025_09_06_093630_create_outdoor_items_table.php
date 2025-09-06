<?php

// database/migrations/2025_09_06_000000_create_outdoor_items_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('outdoor_items', function (Blueprint $t) {
      $t->id();
      $t->foreignId('master_file_id')->constrained('master_files')->cascadeOnDelete();
      $t->string('sub_product');              // BB, TB, Bunting, dll
      $t->unsignedInteger('qty')->default(1);
      $t->string('site')->nullable();         // nama lokasi
      $t->string('size')->nullable();
      $t->string('district_council')->nullable();
      $t->string('coordinates')->nullable();
      $t->string('remarks')->nullable();
      $t->timestamps();

      $t->index(['master_file_id']);
    });
  }
  public function down(): void {
    Schema::dropIfExists('outdoor_items');
  }
};
