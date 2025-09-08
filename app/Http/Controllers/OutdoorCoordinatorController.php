<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterFile;
use App\Models\OutdoorCoordinatorTracking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class OutdoorCoordinatorController  extends Controller
{


    private array $rows;
    private string $title;

    public function __construct(array $rows, string $title)
    {
        $this->rows  = $rows;   // plain array of row arrays
        $this->title = $title;  // e.g. OUTDOOR_EXPORT_2025-09-08
    }

    /** Data starts on row 3 (two heading rows) */
    public function array(): array
    {
        return $this->rows;
    }

    /** Two heading rows: big title (row 1), column headers (row 2) */
    public function headings(): array
    {
        return [
            [$this->title],
            [
                'ID', 'Master File ID', 'Client', 'Product', 'Site',
                'Payment', 'Material', 'Artwork', 'Received Approval',
                'Sent to Printer', 'Collection Printer', 'Installation',
                'Dismantle', 'Remarks', 'Next Follow Up', 'Status',
            ],
        ];
    }

     protected $table = 'outdoor_coordinator_trackings';

    protected $fillable = [
        'master_file_id',
        'site','payment','material','artwork',
        'received_approval','sent_to_printer','collection_printer',
        'installation','dismantle','next_follow_up',
        'remarks','status',
    ];

    protected $casts = [
        'received_approval'  => 'date',
        'sent_to_printer'    => 'date',
        'collection_printer' => 'date',
        'installation'       => 'date',
        'dismantle'          => 'date',
        'next_follow_up'     => 'date',
    ];

    public function index(Request $request)
{
    // 1) Normalize + default month/year (always)
    $rawMonth = $request->input('month', $request->input('outdoor_month'));
    $rawYear  = $request->input('year',  $request->input('outdoor_year'));

    $normalize = function($raw): ?int {
        if ($raw === null || $raw === '') return null;
        $m = trim((string)$raw);
        if (ctype_digit($m)) {
            $n = (int)$m;
            return ($n >= 1 && $n <= 12) ? $n : null;
        }
        $map = [
            'jan'=>1,'january'=>1,'feb'=>2,'february'=>2,'mar'=>3,'march'=>3,
            'apr'=>4,'april'=>4,'may'=>5,'jun'=>6,'june'=>6,'jul'=>7,'july'=>7,
            'aug'=>8,'august'=>8,'sep'=>9,'sept'=>9,'september'=>9,'oct'=>10,'october'=>10,
            'nov'=>11,'november'=>11,'dec'=>12,'december'=>12
        ];
        $key = strtolower(preg_replace('/[^a-z]/i', '', $m));
        return $map[$key] ?? null;
    };

    $month = $normalize($rawMonth) ?? (int) now()->month;   // default current month
    $year  = (int) ($rawYear ?: now()->year);               // default current year

    // 2) Which master files are selected for this month/year?
    $selectedMasterIds = DB::table('outdoor_monthly_details')
        ->where('year', $year)
        ->where('month', $month)
        ->where(function ($q) {
            $q->whereNotNull('value_date')
              ->orWhere(function ($w) {
                  $w->whereNotNull('value_text')
                    ->whereRaw('TRIM(value_text) <> ""');
              });
        })
        ->distinct()
        ->pluck('master_file_id')
        ->map(fn($v) => (int)$v)
        ->unique()
        ->values();

    // 3) Base dataset (only show records selected for the month/year)
    $base = MasterFile::query()
        ->from('master_files as mf')
        ->leftJoin('outdoor_coordinator_trackings as t', 't.master_file_id', '=', 'mf.id')
        ->where(function ($q) {
            $q->whereRaw('LOWER(mf.product) LIKE ?', ['%outdoor%'])
              ->orWhereRaw('LOWER(mf.product_category) LIKE ?', ['%outdoor%'])
              ->orWhereIn('mf.product_category', [
                  'TB','BB','NEWSPAPER','BUNTING','FLYERS','STAR','SIGNAGES',
                  'TB - Tempboard','BB - Billboard','Newspaper','Bunting','Flyers','Star','Signages'
              ]);
        });

    if ($selectedMasterIds->isNotEmpty()) {
        $base->whereIn('mf.id', $selectedMasterIds->all());
    } else {
        $base->whereRaw('1=0'); // nothing selected this month → empty table
    }

    $records = $base->select([
            't.id as id',
            'mf.id as master_file_id',
            'mf.company','mf.client','mf.product','mf.product_category','mf.location',

            // Baseline (non-month) tracking values (will be overlaid from monthly_details)
            't.site','t.payment','t.material','t.artwork',
            't.received_approval','t.sent_to_printer','t.collection_printer','t.installation',
            't.dismantle','t.remarks','t.next_follow_up','t.status',
            't.created_at as tracking_created_at',

            // Snapshots for fallback
            'mf.company as company_snapshot',
            'mf.product as product_snapshot',
        ])
        ->orderByRaw('LOWER(mf.company) asc')
        ->paginate(20)
        ->appends($request->query());

    // 4) Overlay month-specific values from outdoor_monthly_details onto the page of results
    $pageMfIds = $records->pluck('master_file_id')->all();

    $monthlyRows = DB::table('outdoor_monthly_details')
        ->select('master_file_id','field_key','value_text','value_date')
        ->where('year', $year)
        ->where('month', $month)
        ->whereIn('master_file_id', $pageMfIds)
        ->get()
        ->groupBy('master_file_id');

    // Map field_key -> column on the row
    $textCols = ['site','payment','material','artwork','remarks','status'];
    $dateCols = ['received_approval','sent_to_printer','collection_printer','installation','dismantle','next_follow_up'];
    $validKeys = array_merge($textCols, $dateCols);

    foreach ($records as $row) {
        // provide masterFile object for Blade compatibility
        $row->masterFile = (object)[
            'id' => $row->master_file_id,
            'company' => $row->company,
            'client' => $row->client,
            'product' => $row->product,
            'product_category' => $row->product_category,
            'location' => $row->location,
        ];

        if (!isset($monthlyRows[$row->master_file_id])) continue;

        foreach ($monthlyRows[$row->master_file_id] as $md) {
            $key = strtolower((string)$md->field_key);
            if (!in_array($key, $validKeys, true)) continue;

            if (in_array($key, $dateCols, true)) {
                if (!empty($md->value_date)) {
                    $row->{$key} = $md->value_date; // YYYY-MM-DD
                }
            } else {
                // text-like
                $val = trim((string)($md->value_text ?? ''));
                if ($val !== '') {
                    $row->{$key} = $val;
                }
            }
        }
    }

    // 5) Month dropdown data
    $months = [];
    for ($i = 1; $i <= 12; $i++) {
        $months[] = ['value' => $i, 'label' => Carbon::create()->month($i)->format('F')];
    }

    return view('coordinators.outdoor', [
        'rows'          => $records,
        'months'        => $months,
        'month'         => $month,
        'year'          => $year,
        'selectedCount' => $selectedMasterIds->count(),
        'hasSelection'  => $selectedMasterIds->isNotEmpty(),
    ]);
}


    /**
     * 🔥 UPDATED: AJAX Update Field for Inline Editing - Enhanced version
     */
    public function updateField(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:outdoor_coordinator_trackings,id',
                'field' => 'required|string',
                'value' => 'nullable|string'
            ]);

            $job = OutdoorCoordinatorTracking::with('masterFile')->findOrFail($validated['id']);
            $field = $validated['field'];
            $value = $validated['value'];

            // Handle month checkboxes that need to go to master_files table
            $masterFields = [
                'check_jan', 'check_feb', 'check_mar', 'check_apr',
                'check_may', 'check_jun', 'check_jul', 'check_aug',
                'check_sep', 'check_oct', 'check_nov', 'check_dec',
            ];

            if (in_array($field, $masterFields)) {
                if ($job->masterFile) {
                    // Convert value to boolean for checkboxes
                    $boolValue = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
                    $job->masterFile->{$field} = $boolValue;
                    $job->masterFile->save();
                    return response()->json(['success' => true, 'value' => $boolValue]);
                } else {
                    return response()->json(['error' => 'No master file found.'], 404);
                }
            }

            // Fields that go to outdoor_coordinator_trackings table
            $allowedFields = [
                'site', 'payment', 'material', 'artwork', 'received_approval',
                'sent_to_printer', 'collection_printer', 'installation',
                'dismantle', 'remarks', 'next_follow_up', 'status'
            ];

            if (!in_array($field, $allowedFields)) {
                return response()->json(['error' => 'Field not allowed for editing.'], 400);
            }

            // Handle date fields
            $dateFields = [
                'received_approval', 'sent_to_printer', 'collection_printer',
                'installation', 'dismantle', 'next_follow_up'
            ];

            if (in_array($field, $dateFields) && !empty($value)) {
                // Validate date format
                $date = \DateTime::createFromFormat('Y-m-d', $value);
                if (!$date || $date->format('Y-m-d') !== $value) {
                    return response()->json(['error' => 'Invalid date format. Use YYYY-MM-DD.'], 400);
                }
            }

            // Handle status field validation
            if ($field === 'status' && !in_array($value, ['pending', 'ongoing', 'completed', null])) {
                return response()->json(['error' => 'Invalid status value.'], 400);
            }

            // Update the field
            $job->{$field} = $value;
            $job->save();

            // Auto-update status based on progress if we're not directly updating status
            if ($field !== 'status') {
                $newStatus = $this->calculateStatus($job);
                if ($newStatus !== $job->status) {
                    $job->status = $newStatus;
                    $job->save();
                }
            }

            return response()->json([
                'success' => true,
                'value' => $value,
                'status' => $job->status // Return updated status
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            Log::error('updateField error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Server error occurred.'], 500);
        }
    }

    /**
     * Calculate status based on progress
     */
    private function calculateStatus($job)
    {
        if (!empty($job->dismantle)) {
            return 'completed';
        } elseif (!empty($job->installation)) {
            return 'ongoing';
        } else {
            return 'pending';
        }
    }

    public function syncWithMasterFiles()
    {
        // Ambil semua master file dengan kategori Outdoor
        $masterFiles = MasterFile::where('product_category', 'Outdoor')->get();

        $synced = 0;

        foreach ($masterFiles as $mf) {
            // Cek kalau belum ada tracking-nya
            $exists = OutdoorCoordinatorTracking::where('master_file_id', $mf->id)->exists();
            if (!$exists) {
                OutdoorCoordinatorTracking::create([
                    'master_file_id' => $mf->id,
                    'status'         => 'pending',
                    // kolom lainnya bisa default null
                ]);
                $synced++;
            }
        }

        return redirect()->back()->with('success', "$synced outdoor data synced successfully.");
    }

    /**
     * 🔥 NEW: Get Dynamic Years for Filter Dropdown
     */
    public function getAvailableYears()
    {
        $years = OutdoorCoordinatorTracking::selectRaw('DISTINCT YEAR(created_at) as year')
            ->whereNotNull('created_at')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // Add current year if not in list
        $currentYear = now()->year;
        if (!in_array($currentYear, $years)) {
            $years[] = $currentYear;
            rsort($years); // Sort descending
        }

        return $years;
    }

    public function syncFromMasterFile()
    {
        $outdoor = MasterFile::where(function($query) {
            $query->whereIn('product_category', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages', 'Newspaper'])
                  ->orWhere('product_category', 'LIKE', '%outdoor%')
                  ->orWhere('product_category', 'Outdoor');
        })->get();

        $createdCount = 0;
        $updatedCount = 0;

        foreach ($outdoor as $item) {
            $tracking = OutdoorCoordinatorTracking::updateOrCreate(
                ['master_file_id' => $item->id],
                [
                    'site' => $item->location,
                    'status' => 'pending',
                ]
            );

            if ($tracking->wasRecentlyCreated) {
                $createdCount++;
            } else {
                $updatedCount++;
            }
        }

        $message = "Sync completed! Created: {$createdCount}, Updated: {$updatedCount} records.";
        return redirect()->route('coordinator.outdoor.index')->with('success', $message);
    }


    public function show($id)
    {
        $tracking = OutdoorCoordinatorTracking::with('masterFile')->findOrFail($id);
        return view('coordinator.outdoor.show', compact('tracking'));
    }

    public function edit($id)
    {
        $tracking = OutdoorCoordinatorTracking::with('masterFile')->findOrFail($id);

        // Get all outdoor master files for the dropdown
        $masterFiles = MasterFile::where(function($query) {
            $query->whereIn('product_category', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages', 'Newspaper'])
                  ->orWhere('product_category', 'LIKE', '%outdoor%')
                  ->orWhere('product_category', 'Outdoor');
        })
        ->orderBy('client')
        ->get();

        return view('coordinator.outdoor.edit', compact('tracking', 'masterFiles'));
    }




    public function destroy($id)
    {
        $tracking = OutdoorCoordinatorTracking::findOrFail($id);
        $tracking->delete();

        return redirect()->route('coordinator.outdoor.index')
                        ->with('success', 'Outdoor tracking record deleted successfully!');
    }

    /**
     * Manually seed/sync tracking records from master files
     */
    public function seedFromMasterFiles()
    {
        $createdCount = 0;

        // Get outdoor master files that don't have tracking records yet
        $outdoorFiles = MasterFile::where(function($query) {
            $query->whereIn('product_category', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages', 'Newspaper'])
                  ->orWhere('product_category', 'LIKE', '%outdoor%')
                  ->orWhere('product_category', 'Outdoor');
        })
        ->whereNotExists(function($query) {
            $query->select(DB::raw(1))
                  ->from('outdoor_coordinator_trackings')
                  ->whereRaw('outdoor_coordinator_trackings.master_file_id = master_files.id');
        })
        ->get();

        foreach ($outdoorFiles as $file) {
            OutdoorCoordinatorTracking::create([
                'master_file_id' => $file->id,
                'status' => 'pending',
                'site' => $file->location ?? null,
                'payment' => null,
                'material' => null,
                'artwork' => null,
                'received_approval' => null,
                'sent_to_printer' => null,
                'collection_printer' => null,
                'installation' => null,
                'dismantle' => null,
                'remarks' => null,
                'next_follow_up' => null,
            ]);
            $createdCount++;
        }

        if ($createdCount > 0) {
            return redirect()->route('coordinator.outdoor.index')
                           ->with('success', "Created {$createdCount} tracking records from Master Files!");
        }

        return redirect()->route('coordinator.outdoor.index')
                       ->with('info', 'No new outdoor master files found to create tracking records.');
    }

    public function getAvailableMasterFiles()
    {
        $masterFiles = MasterFile::where(function($q) {
        $q->where('product_category', 'Outdoor')
          ->orWhere('product_category', 'LIKE', '%outdoor%');
    })
    ->whereNotExists(function($query) {
        $query->select(DB::raw(1))
              ->from('outdoor_coordinator_trackings')
              ->whereRaw('outdoor_coordinator_trackings.master_file_id = master_files.id');
    })
    ->select('id','client','product','product_category','location')
    ->orderBy('client')
    ->get();
        return response()->json($masterFiles);
    }

    public function styles(Worksheet $sheet)
    {
        // Merge the big title across A1:P1 (16 columns)
        $sheet->mergeCells('A1:P1');

        // Title style
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'FFFF00']], // yellow
        ]);

        // Header row style
        $sheet->getStyle('A2:P2')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'D9D9D9']], // light grey
            'borders' => [
                'allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => '000000']],
            ],
        ]);

        // Body borders (optional)
        $highestRow = $sheet->getHighestRow();
        if ($highestRow >= 3) {
            $sheet->getStyle("A3:P{$highestRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'CCCCCC']],
                ],
            ]);
        }

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Freeze header under row 2 (so row 3 is first scroll row)
                $event->sheet->getDelegate()->freezePane('A3');

                // Set date formats for date columns (I,J,K,L,M,O = 9,10,11,12,13,15)
                foreach (['I','J','K','L','M','O'] as $col) {
                    $event->sheet->getDelegate()
                        ->getStyle("{$col}3:{$col}{$event->sheet->getDelegate()->getHighestRow()}")
                        ->getNumberFormat()->setFormatCode('yyyy-mm-dd');
                }
            },
        ];
    }
}
