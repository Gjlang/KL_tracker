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
        Schema::create('outdoor_coordinator_trackings', function (Blueprint $table) {
        $table->id();
        $table->foreignId('master_file_id')->nullable()->constrained('master_files')->nullOnDelete();

        $table->string('client')->nullable();
        $table->string('product')->nullable();
        $table->string('site')->nullable();
        $table->string('payment')->nullable();
        $table->string('material')->nullable();
        $table->string('artwork')->nullable();

        $table->date('received_approval')->nullable();
        $table->date('sent_to_printer')->nullable();
        $table->date('collection_printer')->nullable();
        $table->date('installation')->nullable();
        $table->date('dismantle')->nullable();

        $table->text('remarks')->nullable();
        $table->date('next_follow_up')->nullable();

        $table->enum('status', ['pending', 'ongoing', 'completed'])->default('pending');

        // ✅ just declare them in order; no AFTER needed
        $table->string('month_jan')->nullable();
        $table->string('month_feb')->nullable();
        $table->string('month_mar')->nullable();
        $table->string('month_apr')->nullable();
        $table->string('month_may')->nullable();
        $table->string('month_jun')->nullable();
        $table->string('month_jul')->nullable();
        $table->string('month_aug')->nullable();
        $table->string('month_sep')->nullable();
        $table->string('month_oct')->nullable();
        $table->string('month_nov')->nullable();
        $table->string('month_dec')->nullable();

        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outdoor_coordinator_trackings', function (Blueprint $table) {
            $table->dropColumn([
                'month_jan', 'month_feb', 'month_mar', 'month_apr',
                'month_may', 'month_jun', 'month_jul', 'month_aug',
                'month_sep', 'month_oct', 'month_nov', 'month_dec'
            ]);

            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['master_file_id']);
        });
    }
};
