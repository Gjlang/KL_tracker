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
    // Search (escaped for LIKE)
    $search = (string) $request->query('q', '');
    $search = str_replace(['\\','%','_'], ['\\\\','\\%','\\_'], $search);

    // Sub Product filter
    $allowedSubs = ['BB','TB','Newspaper','Bunting','Flyers','Star','Signages'];
    $sub = (string) $request->query('sub', '');
    $sub = in_array($sub, $allowedSubs, true) ? $sub : '';

    // --- Master files + ONLY active outdoor items (no completed whiteboard) ---
    $masterFiles = MasterFile::query()
        ->when($search !== '', function ($q) use ($search) {
            $like = "%{$search}%";
            $q->where(function ($qq) use ($like) {
                $qq->where('company',  'like', $like)
                   ->orWhere('product', 'like', $like)
                   ->orWhere('location','like', $like);
            });
        })
        ->with(['outdoorItems' => function ($q) use ($sub) {
            $q->select(
                'outdoor_items.id',
                'outdoor_items.master_file_id',
                'outdoor_items.site',
                'outdoor_items.start_date',
                'outdoor_items.end_date'
            )
            // Apply Sub Product filter (per-site)
            ->when($sub !== '', fn ($qq) => $qq->where('outdoor_items.sub_product', $sub))
            // Exclude items that already have a completed whiteboard
            ->whereNotExists(function ($qq) {
                $qq->select(DB::raw(1))
                   ->from('outdoor_whiteboards as ow')
                   ->whereColumn('ow.outdoor_item_id', 'outdoor_items.id')
                   ->whereNotNull('ow.completed_at');
            });
        }])
        ->orderByDesc('created_at')
        ->get();

    // (Optional) hide MasterFiles that end up with zero active items:
    // $masterFiles = $masterFiles->filter(fn ($mf) => $mf->outdoorItems->isNotEmpty())->values();

    // --- Active item ids actually rendered ---
    $itemIds = $masterFiles->pluck('outdoorItems')->flatten()->pluck('id')->unique()->values();

    // --- Prefill for active items (so values show after refresh) ---
    $existing = collect();
    if ($itemIds->isNotEmpty()) {
        $existing = OutdoorWhiteboard::query()
            ->whereIn('outdoor_item_id', $itemIds)
            ->whereNull('completed_at')
            ->get()
            ->keyBy('outdoor_item_id');
    }

    // --- Badge count for Completed link (global, not filtered) ---
    $completedCount = OutdoorWhiteboard::whereNotNull('completed_at')->count();

    // --- Helper list: current open whiteboards (kept if your Blade uses it) ---
    $whiteboards = OutdoorWhiteboard::query()
        ->whereNull('completed_at')
        ->leftJoin('outdoor_items', 'outdoor_items.id', '=', 'outdoor_whiteboards.outdoor_item_id')
        ->leftJoin('master_files', 'master_files.id', '=', 'outdoor_items.master_file_id')
        ->when($search !== '', function ($q) use ($search) {
            $like = "%{$search}%";
            $q->where(function ($qq) use ($like) {
                $qq->where('master_files.company',  'like', $like)
                   ->orWhere('master_files.product', 'like', $like)
                   ->orWhere('master_files.location','like', $like)
                   ->orWhere('outdoor_items.site',    'like', $like);
            });
        })
        // Apply Sub Product filter here too for consistency
        ->when($sub !== '', fn ($q) => $q->where('outdoor_items.sub_product', $sub))
        ->select([
            'outdoor_whiteboards.*',
            'outdoor_items.id as oi_id',
            'outdoor_items.site',
            'master_files.company',
            'master_files.product',
            'master_files.location',
        ])
        ->orderByDesc('outdoor_whiteboards.created_at')
        ->get();

    return view('outdoor.whiteboard', compact(
        'masterFiles',
        'existing',
        'search',
        'completedCount',
        'whiteboards',
        'sub' // pass current filter to Blade
    ));
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
        ->leftJoinSub($latestActiveWB, 'wb', function ($j) {
            $j->on('wb.outdoor_item_id', '=', 'oi.id');
        })
        ->whereNull('wb.completed_at') // ACTIVE ONLY
        ->orderBy('mf.product')
        ->orderBy('mf.company')
        ->orderBy('oi.site')
            ->get([
        'oi.id as outdoor_item_id',
        'mf.product',
        'mf.company',
        DB::raw('COALESCE(oi.site, mf.location) as location'),

        // duration sources from master_files
        DB::raw('mf.duration as mf_duration'),
        DB::raw('mf.date as mf_date'),
        DB::raw('mf.date_finish as mf_date_finish'),

        DB::raw('mf.date as installation'),
        DB::raw('mf.date_finish as dismantle'),

        // from outdoor_whiteboards (latest active wb)
        DB::raw('COALESCE(wb.created_at, oi.created_at, mf.created_at) as created'),


        // INV Number => client_text
        DB::raw('wb.client_text as inv_number'),

        // Purchase Order
        DB::raw('wb.po_text as po_text'),
        DB::raw('wb.po_date as po_date'),

        // Supplier note/date
        DB::raw('wb.supplier_text as supplier_text'),
        DB::raw('wb.supplier_date as supplier_date'),

        // Storage note/date
        DB::raw('wb.storage_text as storage_text'),
        DB::raw('wb.storage_date as storage_date'),
    ]);


    // Group by product
    $byProduct = collect($rows)->groupBy(fn ($r) => (string)($r->product ?? '—'));

    // === 2) SHEET SETUP ===
    $headers = [
        'No.',
        'Created',
        'INV Number',
        'Purchase Order',
        'Product',
        'Company',
        'Location',
        'Duration',
        'Installation',
        'Dismantle',
        'Supplier',
        'Storage',
    ];
    $lastCol = 'L'; // 11 cols

    $ss = new Spreadsheet();
    $sheet = $ss->getActiveSheet();
    $sheet->setTitle('Outdoor Whiteboard');
    $ss->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

    // Column widths (plus autosize feel)
    $sheet->getColumnDimension('A')->setWidth(5);
    $sheet->getColumnDimension('B')->setWidth(12);
    $sheet->getColumnDimension('C')->setWidth(18);
    $sheet->getColumnDimension('D')->setWidth(20);
    $sheet->getColumnDimension('E')->setWidth(12);
    $sheet->getColumnDimension('F')->setWidth(22);
    $sheet->getColumnDimension('G')->setWidth(22);
    $sheet->getColumnDimension('H')->setWidth(12);
    $sheet->getColumnDimension('I')->setWidth(12);
    $sheet->getColumnDimension('J')->setWidth(20);
    $sheet->getColumnDimension('K')->setWidth(20);
    $sheet->getColumnDimension('L')->setWidth(20);

    // Wrap Purchase Order + Supplier + Storage
    foreach (['D', 'K', 'L'] as $col) {
        $sheet->getStyle("{$col}:{$col}")->getAlignment()->setWrapText(true);
    }

    // Title row
    $sheet->mergeCells('A1:L1');
    $sheet->setCellValue('A1', 'TITLE (OUTDOOR WHITEBOARD)');
    $sheet->getStyle('A1:L1')->applyFromArray([

        'font' => [
            'bold' => true,
            'size' => 14,
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'FFFF00'], // bright yellow
        ],
    ]);

    $rowIdx = 3; // leave a blank row between title and first section

    // === 3) WRITE SECTIONS ===
    foreach ($byProduct as $product => $items) {
        // Section bar (green)
        $sheet->mergeCells("A{$rowIdx}:{$lastCol}{$rowIdx}");
        $sheet->setCellValue("A{$rowIdx}", "Product: {$product}");
        $sheet->getStyle("A{$rowIdx}:{$lastCol}{$rowIdx}")->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '92D050'], // green
            ],
            'font' => ['bold' => true],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ]);
        $rowIdx++;

        // Table header (light yellow)
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue("{$col}{$rowIdx}", $h);
            $col++;
        }
        $sheet->getStyle("A{$rowIdx}:{$lastCol}{$rowIdx}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFF2CC'], // light yellow
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'EAEAEA'],
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
            $r->mf_date ?? null,
            $r->mf_date_finish ?? null
        );
            $sheet->fromArray([[
                $i,
                $this->fmtDate($r->created ?? null),
                $this->blank($r->inv_number ?? null),
                $this->stack($r->po_text ?? null, $r->po_date ?? null),
                $this->blank($r->product ?? null),
                $this->blank($r->company ?? null),
                $this->blank($r->location ?? null),
                $durationText,
                $this->fmtDate($r->installation ?? null),
                $this->fmtDate($r->dismantle ?? null),
                $this->stack($r->supplier_text ?? null, $r->supplier_date ?? null),
                $this->stack($r->storage_text ?? null,  $r->storage_date ?? null),
            ]], null, "A{$rowIdx}");
            // Borders + vertical top for this row
            $sheet->getStyle("A{$rowIdx}:{$lastCol}{$rowIdx}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'EAEAEA'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_TOP,
                ],
            ]);
            $i++;
            $rowIdx++;
        }

        // Spacer
        $rowIdx++;
    }

    // Optional: freeze top left-ish (not per-section)
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


    public function upsert(Request $request)
{
    $data = $request->validate([
        'outdoor_item_id' => ['required', Rule::exists('outdoor_items','id')],
        'master_file_id'  => ['nullable', Rule::exists('master_files','id')],
        'client_text'     => ['nullable','string','max:255'],
        'client_date'     => ['nullable','date'],
        'po_text'         => ['nullable','string','max:255'],
        'po_date'         => ['nullable','date'],
        'supplier_text'   => ['nullable','string','max:255'],
        'supplier_date'   => ['nullable','date'],
        'storage_text'    => ['nullable','string','max:255'],
        'storage_date'    => ['nullable','date'],
        'notes'           => ['nullable','string'],
    ]);

    $data['outdoor_item_id'] = (int) $data['outdoor_item_id'];

    if (empty($data['master_file_id'])) {
        $data['master_file_id'] = OutdoorItem::where('id', $data['outdoor_item_id'])->value('master_file_id');
    }

    $wb = OutdoorWhiteboard::updateOrCreate(
        ['outdoor_item_id' => $data['outdoor_item_id']],
        $data
    );

    return $request->wantsJson()
        ? response()->json(['ok' => true, 'updated_at' => optional($wb->updated_at)->toDateTimeString()])
        : back()->with('success', 'Whiteboard saved.');
}

public function completed(Request $request)
{
    $searchRaw = (string) $request->query('q', '');
    $search = str_replace(['\\','%','_'], ['\\\\','\\%','\\_'], $searchRaw);

    $whiteboards = OutdoorWhiteboard::query()
        ->whereNotNull('outdoor_whiteboards.completed_at')
        ->leftJoin('outdoor_items', 'outdoor_items.id', '=', 'outdoor_whiteboards.outdoor_item_id')
        ->leftJoin('master_files', 'master_files.id', '=', 'outdoor_items.master_file_id')
        ->when($search !== '', function ($q) use ($search) {
            $q->where(function ($qq) use ($search) {
                $like = "%{$search}%";
                $qq->where('master_files.company','like',$like)
                  ->orWhere('master_files.product','like',$like)
                  ->orWhere('master_files.location','like',$like)
                  ->orWhere('outdoor_items.site','like',$like);
            });
        })
        ->orderByDesc('outdoor_whiteboards.completed_at')
        ->select([
            'outdoor_whiteboards.*',          // model fields: client_text, po_text, supplier_text, storage_text, completed_at, etc.
            'outdoor_items.start_date as installation_date',
            'outdoor_items.end_date as dismantle_date',
            'master_files.company',
            'master_files.product',
            'master_files.location',
            // 'master_files.inv_number as inv_number',
        ])
        ->paginate(30)
        ->withQueryString();

    return view('outdoor.whiteboard-completed', compact('whiteboards','searchRaw'));
}


public function markCompleted(Request $request)
{
    $validated = $request->validate([
        'outdoor_item_id' => ['required', Rule::exists('outdoor_items','id')],
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
        : back()->with('success','Marked as completed.');
}

public function restore(Request $request)
{
    $validated = $request->validate([
        'outdoor_item_id' => ['required', Rule::exists('outdoor_whiteboards','outdoor_item_id')],
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
