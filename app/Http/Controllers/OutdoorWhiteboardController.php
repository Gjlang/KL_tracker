<?php

namespace App\Http\Controllers;

use App\Models\MasterFile;
use App\Models\OutdoorWhiteboard;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Exports\OutdoorWhiteboardLedgerExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use App\Models\OutdoorItem;
use App\Models\Contractor;
use App\Models\StockInventory;
use App\Models\StockInventoryTransaction;
use App\Models\ClientCompany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;


class OutdoorWhiteboardController extends Controller
{

    public function index(Request $request)
    {
        // --- Inputs ---
        $search = (string) $request->query('q', '');
        $search = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search);

        // Sub Product filter
        $allowedSubs = ['BB', 'TB', 'Newspaper', 'Bunting', 'Flyers', 'Star', 'Signages'];
        $sub = (string) $request->query('sub', '');
        $sub = in_array($sub, $allowedSubs, true) ? $sub : '';

        // Status filter: open | completed | all
        $status = (string) $request->query('status', 'open');
        if (! in_array($status, ['open', 'completed', 'all'], true)) {
            $status = 'open';
        }

        // --- Main list (MasterFile with nested Outdoor Items) ---
        $masterFiles = MasterFile::with([
            'outdoorItems' => function ($q) use ($sub, $status) {
                $q->select(
                    'outdoor_items.id',
                    'outdoor_items.master_file_id',
                    'outdoor_items.site',           // road / place name (from item)
                    'outdoor_items.start_date',
                    'outdoor_items.end_date',
                    'outdoor_items.billboard_id'    // to traverse → billboard
                )
                    ->with([
                        'billboard' => function ($b) {
                            $b->select(
                                'billboards.id',
                                'billboards.location_id',
                                'billboards.site_number'
                            )
                                ->with([
                                    'location' => function ($l) {
                                        $l->select('locations.id', 'locations.name'); // ← Changed: now selecting name instead of district_id
                                    }
                                ]);
                        }
                    ])
                    ->when($sub !== '', fn($qq) => $qq->where('outdoor_items.sub_product', $sub))
                    // Status logic
                    ->when($status === 'open', function ($qq) {
                        $qq->whereNotExists(function ($x) {
                            $x->select(DB::raw(1))
                                ->from('outdoor_whiteboards as ow')
                                ->whereColumn('ow.outdoor_item_id', 'outdoor_items.id')
                                ->whereNotNull('ow.completed_at');
                        });
                    })
                    ->when($status === 'completed', function ($qq) {
                        $qq->whereExists(function ($x) {
                            $x->select(DB::raw(1))
                                ->from('outdoor_whiteboards as ow')
                                ->whereColumn('ow.outdoor_item_id', 'outdoor_items.id')
                                ->whereNotNull('ow.completed_at');
                        });
                    });
            }
        ])
            ->when($search !== '', function ($q) use ($search) {
                $like = "%{$search}%";

                $q->where(function ($qq) use ($like) {
                    // Search on master_files basic text fields
                    $qq->where('company',  'like', $like)
                        ->orWhere('product', 'like', $like)

                        // Search within ITEM fields
                        ->orWhereHas('outdoorItems', function ($qi) use ($like) {
                            $qi->where('outdoor_items.site', 'like', $like) // road / place
                                ->orWhereHas('billboard', function ($qb) use ($like) {
                                    $qb->where('billboards.site_number', 'like', $like) // TB-SEL-...
                                        ->orWhereHas('location.district', function ($qd) use ($like) {
                                            $qd->where('districts.name', 'like', $like); // district name
                                        });
                                });
                        });
                });
            })
            ->orderByDesc('created_at')
            ->get();

        // --- Active item ids actually rendered ---
        $itemIds = $masterFiles->pluck('outdoorItems')->flatten()->pluck('id')->unique()->values();

        // --- Prefill for active items (so values show after refresh) ---
        // For OPEN view we keep prefill; for COMPLETED/ALL it's not needed
        $existing = collect();
        if ($itemIds->isNotEmpty() && $status === 'open') {
            $existing = OutdoorWhiteboard::query()
                ->whereIn('outdoor_item_id', $itemIds)
                ->whereNull('completed_at')
                ->get()
                ->keyBy('outdoor_item_id');
        }

        // --- Counts for badges/tabs ---
        $openCount      = OutdoorWhiteboard::whereNull('completed_at')->count();
        $completedCount = OutdoorWhiteboard::whereNotNull('completed_at')->count();

        // --- Helper list: whiteboards respecting status filter ---
        $whiteboards = OutdoorWhiteboard::query()
            ->when($status === 'open', fn($q) => $q->whereNull('completed_at'))
            ->when($status === 'completed', fn($q) => $q->whereNotNull('completed_at'))
            ->leftJoin('outdoor_items', 'outdoor_items.id', '=', 'outdoor_whiteboards.outdoor_item_id')
            ->leftJoin('master_files', 'master_files.id', '=', 'outdoor_items.master_file_id')
            // Optional: join billboards/locations/districts so search here behaves similarly
            ->leftJoin('billboards', 'billboards.id', '=', 'outdoor_items.billboard_id')
            ->leftJoin('locations', 'locations.id', '=', 'billboards.location_id')
            ->leftJoin('districts', 'districts.id', '=', 'locations.district_id')
            ->when($search !== '', function ($q) use ($search) {
                $like = "%{$search}%";
                $q->where(function ($qq) use ($like) {
                    $qq->where('master_files.company',        'like', $like)
                        ->orWhere('master_files.product',      'like', $like)
                        ->orWhere('outdoor_items.site',        'like', $like)
                        ->orWhere('billboards.site_number',    'like', $like)
                        ->orWhere('districts.name',            'like', $like);
                });
            })
            // Apply Sub Product filter here too for consistency
            ->when($sub !== '', fn($q) => $q->where('outdoor_items.sub_product', $sub))
            ->select([
                'outdoor_whiteboards.*',
                'outdoor_items.id as oi_id',
                'outdoor_items.site as oi_site',
                'master_files.company',
                'master_files.product',
                'billboards.site_number as bb_site_number',
                'districts.name as district_name',
            ])
            ->orderByDesc('outdoor_whiteboards.created_at')
            ->get();

        $contractors = Contractor::orderBy('name')->get();

        return view('outdoor.whiteboard', compact(
            'masterFiles',
            'existing',
            'search',
            'status',
            'openCount',
            'completedCount',
            'whiteboards',
            'contractors',
            'sub' // pass current filter to Blade
        ));
    }


    public function complete($outdoorItemId)
    {
        // ensure one OW row per item (your schema has unique(outdoor_item_id))
        $ow = OutdoorWhiteboard::firstOrCreate(
            ['outdoor_item_id' => $outdoorItemId],
            [] // fill other default fields if you want
        );

        if (is_null($ow->completed_at)) {
            $ow->completed_at = now();
            $ow->save();
        }

        return back()->with('success', 'Marked as completed.');
    }


    public function exportLedgerXlsx(): StreamedResponse
    {
        // === 1) DATA: active only, latest per outdoor_item ===
        $latestActiveWB = DB::table('outdoor_whiteboards as w')
            ->select('w.*')
            ->join(DB::raw('(
            SELECT outdoor_item_id, MAX(updated_at) AS maxu
            FROM outdoor_whiteboards
            WHERE completed_at IS NULL
            GROUP BY outdoor_item_id
        ) as x'), function ($j) {
                $j->on('x.outdoor_item_id', '=', 'w.outdoor_item_id')
                    ->on('x.maxu', '=', 'w.updated_at');
            });

        $rows = DB::table('outdoor_items as oi')
            ->join('master_files as mf', 'mf.id', '=', 'oi.master_file_id')
            ->leftJoin('billboards as bb', 'bb.id', '=', 'oi.billboard_id')
            ->leftJoin('locations as loc', 'loc.id', '=', 'bb.location_id')
            ->leftJoinSub($latestActiveWB, 'wb', function ($j) {
                $j->on('wb.outdoor_item_id', '=', 'oi.id');
            })
            ->whereNull('wb.completed_at') // ACTIVE ONLY
            ->orderBy('mf.product')
            ->orderBy('mf.company')
            ->orderBy('loc.name')
            ->get([
                'oi.id as outdoor_item_id',
                'mf.product',
                'mf.company',

                // Location from locations table
                'loc.name as location',

                // Duration sources from master_files
                DB::raw('mf.duration as mf_duration'),

                // START DATE and END DATE from master_files
                DB::raw('mf.date as start_date'),
                DB::raw('mf.date_finish as end_date'),

                // INSTALLATION and DISMANTLE from outdoor_whiteboards (user input)
                DB::raw('wb.install_date as install_date'),
                DB::raw('wb.dismantle_date as dismantle_date'),

                // from outdoor_whiteboards (latest active wb)
                DB::raw('COALESCE(wb.created_at, oi.created_at, mf.created_at) as created'),

                // INV Number => client_text
                DB::raw('wb.client_text as inv_number'),

                // Purchase Order (note/date)
                DB::raw('wb.po_text as po_text'),
                DB::raw('wb.po_date as po_date'),

                // Supplier (contractor + date)
                DB::raw('wb.contractor_id as contractor_id'),
                DB::raw('wb.supplier_date as supplier_date'),

                // Storage (note/date)
                DB::raw('wb.storage_text as storage_text'),
                DB::raw('wb.storage_date as storage_date'),
            ]);

        // Group by product
        $byProduct = collect($rows)->groupBy(fn($r) => (string)($r->product ?? '—'));

        // === 2) SHEET SETUP ===
        $headers = [
            'No.',
            'Created',
            'INV Number',
            'Purchase Order',      // SINGLE kolom (note + date ditumpuk)
            'Product',
            'Company',
            'Location',
            'Duration',
            'Start Date',          // NEW: from master_files.date
            'End Date',            // NEW: from master_files.date_finish
            'Installation',        // from outdoor_whiteboards.install_date (user input)
            'Dismantle',          // from outdoor_whiteboards.dismantle_date (user input)
            'Supplier',           // SINGLE kolom (note + date ditumpuk)
            'Storage',            // SINGLE kolom (note + date ditumpuk)
        ];
        $lastCol = 'N'; // 14 cols (A..N)

        $ss = new Spreadsheet();
        $sheet = $ss->getActiveSheet();
        $sheet->setTitle('Outdoor Whiteboard');
        $ss->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(5);   // No.
        $sheet->getColumnDimension('B')->setWidth(12);  // Created
        $sheet->getColumnDimension('C')->setWidth(18);  // INV Number
        $sheet->getColumnDimension('D')->setWidth(20);  // Purchase Order (stacked)
        $sheet->getColumnDimension('E')->setWidth(12);  // Product
        $sheet->getColumnDimension('F')->setWidth(22);  // Company
        $sheet->getColumnDimension('G')->setWidth(22);  // Location
        $sheet->getColumnDimension('H')->setWidth(12);  // Duration
        $sheet->getColumnDimension('I')->setWidth(12);  // Start Date
        $sheet->getColumnDimension('J')->setWidth(12);  // End Date
        $sheet->getColumnDimension('K')->setWidth(12);  // Installation
        $sheet->getColumnDimension('L')->setWidth(12);  // Dismantle
        $sheet->getColumnDimension('M')->setWidth(20);  // Supplier (stacked)
        $sheet->getColumnDimension('N')->setWidth(20);  // Storage (stacked)

        // Wrap untuk kolom yang ditumpuk (NOTE + DATE)
        foreach (['D', 'M', 'N'] as $col) {
            $sheet->getStyle("{$col}:{$col}")->getAlignment()->setWrapText(true);
        }

        // Title row
        $sheet->mergeCells('A1:N1');
        $sheet->setCellValue('A1', 'TITLE (OUTDOOR WHITEBOARD)');
        $sheet->getStyle('A1:N1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFFF00'],
            ],
        ]);

        $rowIdx = 3; // leave a blank row between title and first section

        // === 3) WRITE SECTIONS ===
        foreach ($byProduct as $product => $items) {
            // Section bar
            $sheet->mergeCells("A{$rowIdx}:{$lastCol}{$rowIdx}");
            $sheet->setCellValue("A{$rowIdx}", "Product: {$product}");
            $sheet->getStyle("A{$rowIdx}:{$lastCol}{$rowIdx}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '92D050'],
                ],
                'font' => ['bold' => true],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ]);
            $rowIdx++;

            // Table header
            $col = 'A';
            foreach ($headers as $h) {
                $sheet->setCellValue("{$col}{$rowIdx}", $h);
                $col++;
            }
            $sheet->getStyle("A{$rowIdx}:{$lastCol}{$rowIdx}")->applyFromArray([
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFF2CC'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ]);
            $rowIdx++;

            // Data rows
            $i = 1;
            foreach ($items as $r) {
                $durationText = $this->durationText(
                    $r->mf_duration ?? null,
                    $r->start_date ?? null,
                    $r->end_date ?? null
                );

                $sheet->fromArray([[
                    $i,                                           // A: No.
                    $this->fmtDate($r->created ?? null),         // B: Created
                    $this->blank($r->inv_number ?? null),        // C: INV Number

                    // D: Purchase Order (NOTE + DATE ditumpuk dalam 1 sel)
                    ($r->po_text || $r->po_date)
                        ? trim(($r->po_text ?? '')
                                . ($r->po_date ? "\n" . $this->fmtDate($r->po_date) : '')
                        )
                        : '',

                    $this->blank($r->product ?? null),           // E: Product
                    $this->blank($r->company ?? null),           // F: Company
                    $this->blank($r->location ?? null),          // G: Location (from locations.name)
                    $durationText,                               // H: Duration
                    $this->fmtDate($r->start_date ?? null),      // I: Start Date (from master_files.date)
                    $this->fmtDate($r->end_date ?? null),        // J: End Date (from master_files.date_finish)
                    $this->fmtDate($r->install_date ?? null),    // K: Installation (from outdoor_whiteboards.install_date)
                    $this->fmtDate($r->dismantle_date ?? null),  // L: Dismantle (from outdoor_whiteboards.dismantle_date)

                    // M: Supplier (NOTE + DATE ditumpuk dalam 1 sel)
                    ($r->contractor_id || $r->supplier_date)
                        ? trim(($r->contractor_id ?? '')
                                . ($r->supplier_date ? "\n" . $this->fmtDate($r->supplier_date) : '')
                        )
                        : '',

                    // N: Storage (NOTE + DATE ditumpuk dalam 1 sel)
                    ($r->storage_text || $r->storage_date)
                        ? trim(($r->storage_text ?? '')
                                . ($r->storage_date ? "\n" . $this->fmtDate($r->storage_date) : '')
                        )
                        : '',
                ]], null, "A{$rowIdx}");

                // Borders yang lebih tegas + vertical top
                $sheet->getStyle("A{$rowIdx}:{$lastCol}{$rowIdx}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                    'alignment' => ['vertical' => Alignment::VERTICAL_TOP],
                ]);

                $i++;
                $rowIdx++;
            }

            // Spacer
            $rowIdx++;
        }

        // Freeze header
        $sheet->freezePane('A3');

        // === 4) Stream XLSX download ===
        $fileName = 'outdoor_whiteboard_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($ss) {
            $writer = new Xlsx($ss);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
    // put this below fmtDate() / stack(), but still inside the class
    private function durationText($raw, $startDate, $endDate): string
    {
        // If master_files.duration exists and has a value
        if (isset($raw) && $raw !== '') {
            return is_numeric($raw) ? ($raw . ' days') : (string)$raw;
        }

        // Else compute from master_files.date → date_finish
        if (!empty($startDate) && !empty($endDate)) {
            try {
                $start = Carbon::parse($startDate);
                $end   = Carbon::parse($endDate);
                if ($end->greaterThanOrEqualTo($start)) {
                    $days = $start->diffInDays($end) + 1; // inclusive
                    return $days . ' days';
                }
            } catch (\Throwable $e) {
                // fall through
            }
        }
        return '';
    }


    // ===== helpers =====
    private function blank($v): string
    {
        return isset($v) && $v !== '0' ? (string)$v : '';
    }

    private function fmtDate($v): string
    {
        if (empty($v)) return '';
        try {
            return Carbon::parse($v)->format('d/m/Y'); // mm/dd/yyyy
        } catch (\Throwable) {
            return '';
        }
    }

    // Stack "note" + "date" in one cell with a line break, like your mock
    private function stack(?string $text, $date): string
    {
        $t = trim((string)($text ?? ''));
        $d = $this->fmtDate($date);
        if ($t !== '' && $d !== '') return $t . "\n" . $d;
        if ($t !== '') return $t;
        if ($d !== '') return $d;
        return '';
    }


    //     public function upsert(Request $request)
    // {
    //     $data = $request->validate([
    //         'outdoor_item_id' => ['required', Rule::exists('outdoor_items','id')],
    //         'master_file_id'  => ['nullable', Rule::exists('master_files','id')],
    //         'client_text'     => ['nullable','string','max:255'],
    //         'client_date'     => ['nullable','date'],
    //         'po_text'         => ['nullable','string','max:255'],
    //         'po_date'         => ['nullable','date'],
    //         'contractor_id'   => ['nullable','string','max:255'],
    //         'supplier_date'   => ['nullable','date'],
    //         'storage_text'    => ['nullable','string','max:255'],
    //         'storage_date'    => ['nullable','date'],
    //         'notes'           => ['nullable','string'],
    //     ]);

    //     $data['outdoor_item_id'] = (int) $data['outdoor_item_id'];

    //     if (empty($data['master_file_id'])) {
    //         $data['master_file_id'] = OutdoorItem::where('id', $data['outdoor_item_id'])->value('master_file_id');
    //     }

    //     $wb = OutdoorWhiteboard::updateOrCreate(
    //         ['outdoor_item_id' => $data['outdoor_item_id']],
    //         $data
    //     );

    //     return $request->wantsJson()
    //         ? response()->json(['ok' => true, 'updated_at' => optional($wb->updated_at)->toDateTimeString()])
    //         : back()->with('success', 'Whiteboard saved.');
    // }








    public function upsert(Request $request)
    {
        $data = $request->validate([
            'outdoor_item_id' => ['required', Rule::exists('outdoor_items', 'id')],
            'master_file_id'  => ['nullable', Rule::exists('master_files', 'id')],
            'company_id'      => ['nullable', Rule::exists('client_companies', 'id')],
            'job_number'     => ['nullable', 'string', 'max:255'],
            'client_text'     => ['nullable', 'string', 'max:255'],
            'client_date'     => ['nullable', 'date'],
            'po_text'         => ['nullable', 'string', 'max:255'],
            'po_date'         => ['nullable', 'date'],
            'install_date'   => ['nullable', 'date'],
            'dismantle_date'  => ['nullable', 'date'],
            'contractor_id'   => ['nullable', Rule::exists('contractors', 'id')],
            'supplier_date'   => ['nullable', 'date'],
            'storage_text'    => ['nullable', 'string', 'max:255'],
            'storage_date'    => ['nullable', 'date'],
            'notes'           => ['nullable', 'string'],
        ]);

        $data['outdoor_item_id'] = (int) $data['outdoor_item_id'];

        if (empty($data['master_file_id'])) {
            $data['master_file_id'] = OutdoorItem::where('id', $data['outdoor_item_id'])->value('master_file_id');
        }

        // Get the existing whiteboard record to check for contractor changes
        $existingWhiteboard = OutdoorWhiteboard::where('outdoor_item_id', $data['outdoor_item_id'])->first();
        $oldContractorId = $existingWhiteboard ? $existingWhiteboard->contractor_id : null;

        $wb = OutdoorWhiteboard::updateOrCreate(
            ['outdoor_item_id' => $data['outdoor_item_id']],
            $data
        );

        // Check if supplier data was provided to create new vendor stock inventory
        if (!empty($data['contractor_id']) || !empty($data['supplier_date'])) {
            // Get the outdoor item to get related information for the stock inventory
            $outdoorItem = OutdoorItem::find($data['outdoor_item_id']);

            // Handle contractor change - delete old contractor data if contractor changed
            if ($oldContractorId && $oldContractorId != $data['contractor_id']) {
                $this->deleteOldContractorData($oldContractorId, $data['outdoor_item_id'], $data['master_file_id']);
            }

            // Create or update stock inventory for new contractor
            if (!empty($data['contractor_id'])) {

                // Check if stock inventory already exists for this contractor
                $stockInventory = StockInventory::where('contractor_id', $data['contractor_id'])->first();

                if (!$stockInventory) {
                    // Create new stock inventory record
                    $stockInventory = StockInventory::create([
                        'contractor_id' => $data['contractor_id'],
                        'balance_contractor' => 0, // Initial balance
                        'balance_bgoc' => 0,       // Initial balance
                        'description' => $outdoorItem->name ?? 'Outdoor Item Stock',
                    ]);
                }

                // Create IN transaction for the stock inventory
                $inTransactionData = [
                    'stock_inventory_id' => $stockInventory->id,
                    'outdoor_item_id' => $data['outdoor_item_id'],
                    'master_file_id' => $data['master_file_id'],
                    'client_id' => $data['company_id'] ?? null,
                    'type' => 'in',
                    'quantity' => 1, // Default quantity, adjust as needed
                    'transaction_date' => $data['supplier_date'] ?? now(),
                    'remarks' => 'Contractor dismantle for Job No: ' . $data['job_number'],
                ];

                // Add client_id if client information is available
                if (!empty($data['client_text'])) {
                    $clientCompany = ClientCompany::where('name', $data['client_text'])->first();
                    if ($clientCompany) {
                        $inTransactionData['client_id'] = $clientCompany->id;
                    }
                }

                // Add billboard_id if outdoor item has a related billboard
                if ($outdoorItem && $outdoorItem->billboard_id) {
                    $inTransactionData['billboard_id'] = $outdoorItem->billboard_id;
                }

                // Check if a record exists with the same outdoor_item_id and master_file_id
                $existingTransaction = StockInventoryTransaction::where([
                    ['outdoor_item_id', $data['outdoor_item_id']],
                    ['master_file_id', $data['master_file_id']],
                    ['stock_inventory_id', $stockInventory->id],
                    ['type', 'in']
                ])->first();

                if ($existingTransaction) {
                    // Update existing record
                    $existingTransaction->update($inTransactionData);
                    $transaction = $existingTransaction;
                } else {
                    // Create new record
                    $transaction = StockInventoryTransaction::create($inTransactionData);
                }

                // Update the stock inventory balances after the transaction
                $this->updateStockInventoryBalances($stockInventory->id);
            }
        }

        return $request->wantsJson()
            ? response()->json(['ok' => true, 'updated_at' => optional($wb->updated_at)->toDateTimeString()])
            : back()->with('success', 'Whiteboard saved.');
    }

    /**
     * Helper method to update stock inventory balances
     */
    private function updateStockInventoryBalances($stockInventoryId)
    {
        $inTotal = StockInventoryTransaction::where('stock_inventory_id', $stockInventoryId)
            ->where('type', 'in')
            ->sum('quantity');

        $outTotal = StockInventoryTransaction::where('stock_inventory_id', $stockInventoryId)
            ->where('type', 'out')
            ->sum('quantity');

        $balance = $inTotal - $outTotal;

        StockInventory::where('id', $stockInventoryId)->update([
            'balance_contractor' => $balance,
            // 'balance_bgoc' => $balance,
        ]);
    }

    private function deleteOldContractorData($oldContractorId, $outdoorItemId, $masterFileId)
    {
        // Find the old stock inventory for the old contractor
        $oldStockInventory = StockInventory::where('contractor_id', $oldContractorId)->first();

        if ($oldStockInventory) {
            // Delete the specific transaction related to this outdoor item and master file
            StockInventoryTransaction::where([
                ['stock_inventory_id', $oldStockInventory->id],
                ['outdoor_item_id', $outdoorItemId],
                ['master_file_id', $masterFileId],
                ['type', 'in']
            ])->delete();

            // Update the balance of the old stock inventory after deletion
            $this->updateStockInventoryBalances($oldStockInventory->id);

            // Optional: Delete the stock inventory if it has no transactions left
            $transactionCount = StockInventoryTransaction::where('stock_inventory_id', $oldStockInventory->id)->count();
            if ($transactionCount == 0) {
                $oldStockInventory->delete();
            }
        }
    }

    public function completed(Request $request)
    {
        $searchRaw = (string) $request->query('q', '');
        $search = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $searchRaw);

        $whiteboards = OutdoorWhiteboard::query()
            ->whereNotNull('outdoor_whiteboards.completed_at')
            ->leftJoin('outdoor_items', 'outdoor_items.id', '=', 'outdoor_whiteboards.outdoor_item_id')
            ->leftJoin('master_files', 'master_files.id', '=', 'outdoor_items.master_file_id')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $like = "%{$search}%";
                    $qq->where('master_files.company', 'like', $like)
                        ->orWhere('master_files.product', 'like', $like)
                        ->orWhere('master_files.location', 'like', $like)
                        ->orWhere('outdoor_items.site', 'like', $like);
                });
            })
            ->orderByDesc('outdoor_whiteboards.completed_at')
            ->select([
                'outdoor_whiteboards.*',          // model fields: client_text, po_text, contractor_id, storage_text, completed_at, etc.
                'outdoor_items.start_date as installation_date',
                'outdoor_items.end_date as dismantle_date',
                'master_files.company',
                'master_files.product',
                'master_files.location',
                // 'master_files.inv_number as inv_number',
            ])
            ->paginate(30)
            ->withQueryString();

        return view('outdoor.whiteboard-completed', compact('whiteboards', 'searchRaw'));
    }


    public function markCompleted(Request $request)
    {
        $validated = $request->validate([
            'outdoor_item_id' => ['required', Rule::exists('outdoor_items', 'id')],
        ]);

        $itemId = (int) $validated['outdoor_item_id'];

        // Create if missing, then mark as completed
        $wb = OutdoorWhiteboard::firstOrNew(['outdoor_item_id' => $itemId]);

        if (empty($wb->master_file_id)) {
            $wb->master_file_id = OutdoorItem::where('id', $itemId)->value('master_file_id');
        }

        $wb->completed_at = now();
        $wb->save();

        return $request->wantsJson()
            ? response()->json(['ok' => true, 'completed_at' => $wb->completed_at->toDateTimeString()])
            : back()->with('success', 'Marked as completed.');
    }

    public function restore(Request $request)
    {
        $validated = $request->validate([
            'outdoor_item_id' => ['required', Rule::exists('outdoor_whiteboards', 'outdoor_item_id')],
        ]);

        $wb = OutdoorWhiteboard::where('outdoor_item_id', (int) $validated['outdoor_item_id'])
            ->whereNotNull('completed_at') // only restore completed ones
            ->firstOrFail();

        $wb->completed_at = null;
        $wb->save();

        return $request->wantsJson()
            ? response()->json(['ok' => true])
            : back()->with('success', 'Restored to Active.');
    }
}
