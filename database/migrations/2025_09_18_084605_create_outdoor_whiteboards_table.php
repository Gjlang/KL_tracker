<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('outdoor_whiteboards', function (Blueprint $table) {
            $table->id();

            $table->foreignId('master_file_id')
                  ->constrained('master_files')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            // Client (text + date)
            $table->string('client_text')->nullable();
            $table->date('client_date')->nullable();

            // PO (text + date)
            $table->string('po_text')->nullable();
            $table->date('po_date')->nullable();

            // Supplier (text + date)
            $table->string('supplier_text')->nullable();
            $table->date('supplier_date')->nullable();

            // Storage (text + date)
            $table->string('storage_text')->nullable();
            $table->date('storage_date')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            // Unik biar 1 master_file cuma punya 1 whiteboard (hapus kalau mau banyak)
            $table->unique('master_file_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outdoor_whiteboards');
    }
};
