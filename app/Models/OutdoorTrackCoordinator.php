<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int|null $master_file_id
 * @property int|null $outdoor_item_id
 * @property \Illuminate\Support\Carbon|null $masterfile_created_at
 * @property int|null $year
 * @property int|null $month
 * @property string|null $client
 * @property string|null $product
 * @property string|null $site
 * @property string|null $site_date
 * @property string|null $payment
 * @property string|null $payment_date
 * @property string|null $material
 * @property string|null $material_date
 * @property string|null $artwork
 * @property string|null $artwork_date
 * @property \Illuminate\Support\Carbon|null $received_approval
 * @property string|null $received_approval_note
 * @property \Illuminate\Support\Carbon|null $sent_to_printer
 * @property string|null $sent_to_printer_note
 * @property \Illuminate\Support\Carbon|null $collection_printer
 * @property string|null $collection_printer_note
 * @property \Illuminate\Support\Carbon|null $installation
 * @property string|null $installation_note
 * @property \Illuminate\Support\Carbon|null $dismantle
 * @property string|null $dismantle_note
 * @property string|null $remarks
 * @property \Illuminate\Support\Carbon|null $next_follow_up
 * @property string|null $next_follow_up_note
 * @property string $status
 * @property string|null $month_jan
 * @property string|null $month_feb
 * @property string|null $month_mar
 * @property string|null $month_apr
 * @property string|null $month_may
 * @property string|null $month_jun
 * @property string|null $month_jul
 * @property string|null $month_aug
 * @property string|null $month_sep
 * @property string|null $month_oct
 * @property string|null $month_nov
 * @property string|null $month_dec
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereArtwork($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereArtworkDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereCollectionPrinter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereCollectionPrinterNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereDismantle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereDismantleNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereInstallation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereInstallationNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereMasterFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereMasterfileCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereMaterial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereMaterialDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereMonthApr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereMonthAug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereMonthDec($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereMonthFeb($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereMonthJan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereMonthJul($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereMonthJun($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereMonthMar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereMonthMay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereMonthNov($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereMonthOct($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereMonthSep($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereNextFollowUp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereNextFollowUpNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereOutdoorItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator wherePayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereReceivedApproval($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereReceivedApprovalNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereSentToPrinter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereSentToPrinterNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereSite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereSiteDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorTrackCoordinator whereYear($value)
 * @mixin \Eloquent
 */
class OutdoorTrackCoordinator extends Model
{
    // 👇 pastikan table name benar
    protected $table = 'outdoor_coordinator_trackings';

    // kalau primary key bukan 'id', set disini (seharusnya id)
    protected $primaryKey = 'id';

    public $timestamps = true; // karena tabel ada created_at/updated_at

    protected $guarded = []; // SEMENTARA kosongkan guard agar semua field bisa diisi

    protected $casts = [
        'received_approval'   => 'date',
        'sent_to_printer'     => 'date',
        'collection_printer'  => 'date',
        'installation'        => 'date',
        'dismantle'           => 'date',
        'next_follow_up'      => 'date',
        'masterfile_created_at' => 'datetime',
    ];
}
