<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('outdoor_ongoing_jobs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('master_file_id')->nullable()->constrained('master_files')->nullOnDelete();
            $table->unsignedInteger('year')->nullable();

            $table->date('date')->nullable();
            $table->string('company')->nullable();
            $table->string('product')->nullable();
            $table->string('category')->nullable(); // product_category
            $table->string('platform')->nullable(); // keep for parity w/ Media UI (can be null)
            $table->string('location')->nullable();

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // Aggregated month texts (e.g. "Installed @ 2025-02-12")
            $table->string('jan')->nullable();
            $table->string('feb')->nullable();
            $table->string('mar')->nullable();
            $table->string('apr')->nullable();
            $table->string('may')->nullable();
            $table->string('jun')->nullable();
            $table->string('jul')->nullable();
            $table->string('aug')->nullable();
            $table->string('sep')->nullable();
            $table->string('oct')->nullable();
            $table->string('nov')->nullable();
            $table->string('dec')->nullable();

            $table->string('status')->nullable()->default('ongoing');
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index('date');
            $table->index('company');
            $table->index('product');
            $table->index(['year','company']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outdoor_ongoing_jobs');
    }
};
