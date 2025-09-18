<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('outdoor_whiteboards', function (Blueprint $table) {
            $table->id();

            // Kunci ke master_files (data tampilan diambil dari sini)
            $table->foreignId('master_file_id')
                  ->constrained('master_files')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            // Client – kamu minta “text box + date”
            $table->string('client_text')->nullable();
            $table->date('client_date')->nullable();

            // PO – “text + date”
            $table->string('po_text')->nullable();
            $table->date('po_date')->nullable();

            // Supplier – “text + date”
            $table->string('supplier_text')->nullable();
            $table->date('supplier_date')->nullable();

            // Storage – “text + date”
            $table->string('storage_text')->nullable();
            $table->date('storage_date')->nullable();

            // Optional catatan
            $table->text('notes')->nullable();

            // Opsional: kunci unik supaya 1 master file hanya 1 entri whiteboard (biar gak overlap)
            $table->unique(['master_file_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outdoor_whiteboards');
    }
};
