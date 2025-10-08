<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private array $datePairs = [
        // textColumn                => [dateColumn, placeAfter]
        'payment'                   => ['payment_date', 'payment'],
        'material'                  => ['material_date', 'material'],
        'artwork'                   => ['artwork_date', 'artwork'],
        'site'                      => ['site_date', 'site'],

        // note columns paired with existing date columns
        'received_approval_note'    => ['received_approval', 'received_approval'],
        'sent_to_printer_note'      => ['sent_to_printer', 'sent_to_printer'],
        'collection_printer_note'   => ['collection_printer', 'collection_printer'],
        'installation_note'         => ['installation', 'installation'],
        'dismantle_note'            => ['dismantle', 'dismantle'],
        'next_follow_up_note'       => ['next_follow_up', 'next_follow_up'],
    ];

    public function up(): void
    {
        Schema::table('outdoor_coordinator_trackings', function (Blueprint $table) {
            // 1) *_date additions for text columns that already exist
            foreach (['payment','material','artwork','site'] as $textCol) {
                [$dateCol, $after] = $this->datePairs[$textCol];
                if (!Schema::hasColumn('outdoor_coordinator_trackings', $dateCol)) {
                    $table->date($dateCol)->nullable()->after($after);
                }
            }

            // 2) *_note additions for date columns that already exist
            foreach ([
                'received_approval_note'  => 'received_approval',
                'sent_to_printer_note'    => 'sent_to_printer',
                'collection_printer_note' => 'collection_printer',
                'installation_note'       => 'installation',
                'dismantle_note'          => 'dismantle',
                'next_follow_up_note'     => 'next_follow_up',
            ] as $noteCol => $after) {
                if (!Schema::hasColumn('outdoor_coordinator_trackings', $noteCol)) {
                    $table->string($noteCol, 255)->nullable()->after($after);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('outdoor_coordinator_trackings', function (Blueprint $table) {
            $drop = [
                'payment_date','material_date','artwork_date','site_date',
                'received_approval_note','sent_to_printer_note','collection_printer_note',
                'installation_note','dismantle_note','next_follow_up_note',
            ];

            foreach ($drop as $col) {
                if (Schema::hasColumn('outdoor_coordinator_trackings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
