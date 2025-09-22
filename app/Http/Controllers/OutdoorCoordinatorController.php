<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterFile;
use App\Models\OutdoorCoordinatorTracking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse; // add at top
use Illuminate\Foundation\Configuration\Exceptions;
use Throwable;
use App\Http\Requests\OutdoorCoordinatorRequest;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;


class OutdoorCoordinatorController extends Controller
{

public function index(Request $request)
{
    // -------- Inputs + normalization --------
    $rawMonth = $request->input('month', $request->input('outdoor_month')); // support both
    $rawYear  = $request->input('year',  $request->input('outdoor_year'));

    $normalizeMonth = function ($raw): ?int {
        if ($raw === null || $raw === '') return null; // All Months
        $m = strtolower(trim((string)$raw));
        if (ctype_digit($m)) { $n=(int)$m; return ($n>=1 && $n<=12) ? $n : null; }
        $map = [
            'jan'=>1,'january'=>1,'feb'=>2,'february'=>2,'mar'=>3,'march'=>3,
            'apr'=>4,'april'=>4,'may'=>5,'jun'=>6,'june'=>6,'jul'=>7,'july'=>7,
            'aug'=>8,'august'=>8,'sep'=>9,'sept'=>9,'september'=>9,'oct'=>10,'october'=>10,
            'nov'=>11,'november'=>11,'dec'=>12,'december'=>12,
        ];
        return $map[$m] ?? null;
    };

    $month = $normalizeMonth($rawMonth); // 1..12 or null (All Months)

    // Year coercion: treat '', '0', null as "use current year"
    $year = is_numeric($rawYear) ? (int)$rawYear : (int) now()->year;
    if ($year <= 1970) { // guard against 0 or nonsense values
        $year = (int) now()->year;
    }

    $search     = trim((string) $request->get('search', ''));
    $activeOnly = $month !== null && $request->boolean('active'); // ignore toggle when All Months

    // -------- Base set: all Outdoor sites (mf JOIN oi) --------
    $q = DB::table('master_files as mf')
        ->leftJoin('outdoor_items as oi', 'oi.master_file_id', '=', 'mf.id')
        ->where(function ($w) {
            $w->whereRaw('LOWER(mf.product_category) REGEXP ?', ['(^|[^a-z])(outdoor|billboard)([^a-z]|$)'])
              ->orWhereRaw('LOWER(mf.product) REGEXP ?',          ['(^|[^a-z])(outdoor|billboard)([^a-z]|$)']);
        });

    // -------- Search --------
    if ($search !== '') {
        $like = '%' . strtolower($search) . '%';
        $q->where(function ($w) use ($like) {
            $w->whereRaw('LOWER(mf.company) LIKE ?', [$like])
              ->orWhereRaw('LOWER(mf.product) LIKE ?', [$like])
              ->orWhereRaw('LOWER(oi.site) LIKE ?', [$like])
              ->orWhereRaw('LOWER(oi.district_council) LIKE ?', [$like])
              ->orWhereRaw('LOWER(oi.coordinates) LIKE ?', [$like]);
        });
    }

    // -------- Month-aware LEFT JOIN to OMD (pivoted per item) --------
    if ($month !== null) { // month=0 means "All Months" in UI → treat as null
        // Subquery: pivot OMD ke kolom2 per item untuk year+month yang dipilih
        $omd = DB::table('outdoor_monthly_details as md')
            ->select([
                'md.outdoor_item_id',
                DB::raw("MAX(CASE WHEN md.field_key='status'        AND md.field_type='text' THEN md.value_text END)         AS status"),
                DB::raw("MAX(CASE WHEN md.field_key='remarks'       AND md.field_type='text' THEN md.value_text END)         AS remarks"),
                DB::raw("MAX(CASE WHEN md.field_key='payment'       AND md.field_type='text' THEN md.value_text END)         AS payment"),
                DB::raw("MAX(CASE WHEN md.field_key='material'      AND md.field_type='text' THEN md.value_text END)         AS material"),
                DB::raw("MAX(CASE WHEN md.field_key='artwork'       AND md.field_type='text' THEN md.value_text END)         AS artwork"),
                DB::raw("MAX(CASE WHEN md.field_key='site'          AND md.field_type='text' THEN md.value_text END)         AS site_text"),
                DB::raw("MAX(CASE WHEN md.field_key='site_date'     AND md.field_type='date' THEN md.value_date END)         AS site_date"),
                DB::raw("MAX(CASE WHEN md.field_key='payment_date'  AND md.field_type='date' THEN md.value_date END)         AS payment_date"),
                DB::raw("MAX(CASE WHEN md.field_key='material_date' AND md.field_type='date' THEN md.value_date END)         AS material_date"),
                DB::raw("MAX(CASE WHEN md.field_key='artwork_date'  AND md.field_type='date' THEN md.value_date END)         AS artwork_date"),
                DB::raw("MAX(CASE WHEN md.field_key='received_approval' AND md.field_type='date' THEN md.value_date END)     AS received_approval"),
                DB::raw("MAX(CASE WHEN md.field_key='sent_to_printer'  AND md.field_type='date' THEN md.value_date END)      AS sent_to_printer"),
                DB::raw("MAX(CASE WHEN md.field_key='collection_printer' AND md.field_type='date' THEN md.value_date END)    AS collection_printer"),
                DB::raw("MAX(CASE WHEN md.field_key='installation'  AND md.field_type='date' THEN md.value_date END)         AS installation"),
                DB::raw("MAX(CASE WHEN md.field_key='dismantle'     AND md.field_type='date' THEN md.value_date END)         AS dismantle"),
                DB::raw("MAX(CASE WHEN md.field_key='next_follow_up'AND md.field_type='date' THEN md.value_date END)         AS next_follow_up"),
                DB::raw("MAX(CASE WHEN md.field_key='received_approval_note'  AND md.field_type='text' THEN md.value_text END) AS received_approval_note"),
                DB::raw("MAX(CASE WHEN md.field_key='sent_to_printer_note'    AND md.field_type='text' THEN md.value_text END) AS sent_to_printer_note"),
                DB::raw("MAX(CASE WHEN md.field_key='collection_printer_note' AND md.field_type='text' THEN md.value_text END) AS collection_printer_note"),
                DB::raw("MAX(CASE WHEN md.field_key='installation_note'       AND md.field_type='text' THEN md.value_text END) AS installation_note"),
                DB::raw("MAX(CASE WHEN md.field_key='dismantle_note'          AND md.field_type='text' THEN md.value_text END) AS dismantle_note"),
                DB::raw("MAX(CASE WHEN md.field_key='next_follow_up_note'     AND md.field_type='text' THEN md.value_text END) AS next_follow_up_note"),
                // simpan 1 id md sebagai penanda ada data (untuk Active/UPDATE path)
                DB::raw("MAX(md.id) AS tracking_id")
            ])
            ->where('md.year', $year)
            ->where('md.month', $month)
            ->groupBy('md.outdoor_item_id');

        $q->leftJoinSub($omd, 'md', 'md.outdoor_item_id', '=', 'oi.id');

        // STRICT Active: hanya yang punya record OMD bulan tsb
        if ($activeOnly) {
            $q->whereNotNull('md.tracking_id');
        }
    }
    else {
        // ALL MONTHS: join ke OCT (ambil 1 row terbaru per (mf, oi))
        $octLatest = DB::table('outdoor_coordinator_trackings as oct')
            ->select([
                'oct.master_file_id',
                'oct.outdoor_item_id',
                DB::raw('MAX(oct.id) AS oct_id')
            ])
            ->groupBy('oct.master_file_id', 'oct.outdoor_item_id');

        $q->leftJoinSub($octLatest, 'ox', function ($j) {
            $j->on('ox.master_file_id', '=', 'mf.id')
              ->on('ox.outdoor_item_id', '=', 'oi.id');
        });
        $q->leftJoin('outdoor_coordinator_trackings as oct', 'oct.id', '=', 'ox.oct_id');
    }

    // -------- Selects --------
    $q->select([
        'mf.id as master_file_id',
        'mf.company as company',
        'mf.client as client',
        'mf.product as product',
        'mf.product_category as product_category',
        DB::raw('oi.id as outdoor_item_id'),
        DB::raw('oi.site as site'),
        DB::raw('oi.district_council as district_council'),
        DB::raw('oi.coordinates as coordinates'),
        DB::raw('oi.size as size'),

        // kolom-kolom bulan (NULL kalau tidak ada OMD di bulan tsb, atau OCT data saat All Months)
        DB::raw(($month !== null) ? 'md.status'              : 'oct.status as status'),
        DB::raw(($month !== null) ? 'md.remarks'             : 'oct.remarks as remarks'),
        DB::raw(($month !== null) ? 'md.payment'             : 'oct.payment as payment'),
        DB::raw(($month !== null) ? 'md.material'            : 'oct.material as material'),
        DB::raw(($month !== null) ? 'md.artwork'             : 'oct.artwork as artwork'),
        DB::raw(($month !== null) ? 'md.site_text'           : 'oi.site as site_text'),
        DB::raw(($month !== null) ? 'md.site_date'           : 'oct.site_date as site_date'),
        DB::raw(($month !== null) ? 'md.payment'             : 'oct.payment as payment'),
        DB::raw(($month !== null) ? 'md.payment_date'        : 'oct.payment_date as payment_date'),
        DB::raw(($month !== null) ? 'md.material'            : 'oct.material as material'),
        DB::raw(($month !== null) ? 'md.material_date'       : 'oct.material_date as material_date'),
        DB::raw(($month !== null) ? 'md.artwork'             : 'oct.artwork as artwork'),
        DB::raw(($month !== null) ? 'md.artwork_date'        : 'oct.artwork_date as artwork_date'),
        DB::raw(($month !== null) ? 'md.received_approval'   : 'oct.received_approval as received_approval'),
        DB::raw(($month !== null) ? 'md.received_approval_note' : 'oct.received_approval_note as received_approval_note'),
        DB::raw(($month !== null) ? 'md.sent_to_printer'     : 'oct.sent_to_printer as sent_to_printer'),
        DB::raw(($month !== null) ? 'md.sent_to_printer_note'   : 'oct.sent_to_printer_note as sent_to_printer_note'),
        DB::raw(($month !== null) ? 'md.collection_printer'  : 'oct.collection_printer as collection_printer'),
        DB::raw(($month !== null) ? 'md.collection_printer_note' : 'oct.collection_printer_note as collection_printer_note'),
        DB::raw(($month !== null) ? 'md.installation'        : 'oct.installation as installation'),
        DB::raw(($month !== null) ? 'md.installation_note'   : 'oct.installation_note as installation_note'),
        DB::raw(($month !== null) ? 'md.dismantle'           : 'oct.dismantle as dismantle'),
        DB::raw(($month !== null) ? 'md.dismantle_note'      : 'oct.dismantle_note as dismantle_note'),
        DB::raw(($month !== null) ? 'md.next_follow_up'      : 'oct.next_follow_up as next_follow_up'),
        DB::raw(($month !== null) ? 'md.next_follow_up_note' : 'oct.next_follow_up_note as next_follow_up_note'),
        DB::raw(($month !== null) ? 'md.tracking_id'         : 'oct.id as tracking_id'),
    ]);

    $q->orderBy('mf.company')->orderBy('oi.site');

    // -------- Paginate + page correction --------
    $rows = $q->paginate(50)->withQueryString(); // even if empty, paginator is safe
    if ($rows->isEmpty() && $rows->currentPage() > 1) {
        return redirect()->to(url()->current() . '?' . http_build_query($request->except('page') + ['page' => 1]));
    }

    // -------- Month list for the dropdown --------
    $monthLabels = [1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',
                    7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'];
    $months = collect(range(1,12))->map(fn($m)=>['value'=>$m,'label'=>$monthLabels[$m]]);

    return view('coordinators.outdoor', [
        'rows'   => $rows,
        'year'   => $year,
        'month'  => $month,       // null = All Months
        'active' => $activeOnly,
        'search' => $search,
        'months' => $months,
    ]);
}
public function upsert(Request $request)
{
    // Always JSON for fetch() clients
    $request->headers->set('Accept', 'application/json');

    $data = $request->validate([
        // Two modes:
        // A) OCT direct-by-id
        'id'              => 'nullable|integer',

        // B) Month-scope (OMD + OCT ensure)
        'master_file_id'  => 'required_without:id|integer|exists:master_files,id',
        'outdoor_item_id' => 'nullable|integer|exists:outdoor_items,id',
        'year'            => 'nullable|integer|min:2000|max:2100',
        'month'           => 'nullable|integer|min:1|max:12',

        // Shared
        'field'           => 'required|string',
        'value'           => 'nullable',
    ]);

    $field = $data['field'];
    $value = $data['value'] ?? null;

    // Allowed fields (extend if needed)
    $allowed = [
        // base texts
        'site','payment','material','artwork','remarks','status',
        // new paired dates for base texts
        'site_date','payment_date','material_date','artwork_date',
        // date-core fields
        'received_approval','sent_to_printer','collection_printer','installation','dismantle','next_follow_up',
        // notes for date-core fields
        'received_approval_note','sent_to_printer_note','collection_printer_note','installation_note','dismantle_note','next_follow_up_note',
    ];
    if (!in_array($field, $allowed, true)) {
        return response()->json(['success' => false, 'error' => 'Invalid field'], 422);
    }

    // Date normalization when needed
    $dateFields = [
        'site_date','payment_date','material_date','artwork_date',
        'received_approval','sent_to_printer','collection_printer','installation','dismantle','next_follow_up',
    ];

    // Helper: check once which columns exist on outdoor_coordinator_trackings (OCT)
    $octHas = function (string $col): bool {
        static $cache = null;
        if ($cache === null) {
            $cols = Schema::getColumnListing('outdoor_coordinator_trackings');
            $cache = array_fill_keys($cols, true);
        }
        return isset($cache[$col]);
    };

    if (in_array($field, $dateFields, true) && $value !== null && $value !== '') {
        $d = \DateTime::createFromFormat('Y-m-d', (string)$value);
        if (!$d || $d->format('Y-m-d') !== (string)$value) {
            return response()->json(['success' => false, 'error' => 'Invalid date format (Y-m-d expected)'], 422);
        }
    }

    // Decide scope: month-scope requires all three
    $isMonthScope = !empty($data['year']) && !empty($data['month']) && !empty($data['outdoor_item_id']);

    try {
        if ($isMonthScope) {
            // ---------- MONTH-SCOPE PATH (OMD + ensure OCT) ----------

            $mfId = (int)($data['master_file_id'] ?? 0);
            $oiId = (int)$data['outdoor_item_id'];
            $year = (int)$data['year'];
            $month = (int)$data['month'];

            // Derive/verify master_file_id vs outdoor_item_id relationship
            if ($mfId <= 0) {
                // Derive from outdoor_items if not provided (should be provided in your payload)
                $mfId = (int) DB::table('outdoor_items')->where('id', $oiId)->value('master_file_id');
            }
            if (!$mfId) {
                return response()->json(['success'=>false,'error'=>'master_file_id could not be derived'], 422);
            }
            $oiMf = (int) DB::table('outdoor_items')->where('id', $oiId)->value('master_file_id');
            if ($oiMf !== $mfId) {
                return response()->json(['success'=>false,'error'=>'outdoor_item_id does not belong to master_file_id'], 422);
            }

            $isDate = in_array($field, $dateFields, true);
            $isNote = str_ends_with($field, '_note');

            // 1) Upsert to outdoor_monthly_details (SOURCE OF TRUTH) — include master_file_id!
            $omdKey = [
                'master_file_id'  => $mfId,
                'outdoor_item_id' => $oiId,
                'year'            => $year,
                'month'           => $month,
                'field_key'       => $field,
            ];
            $omdVals = [
                'field_type' => $isDate ? 'date' : 'text',
                'value_text' => $isDate ? null : (string)($value ?? ''),
                'value_date' => $isDate ? ($value ?: null) : null,
                'updated_at' => now(),
            ];
            DB::table('outdoor_monthly_details')->updateOrInsert($omdKey, $omdVals + ['created_at' => now()]);

            // 2) Find existing OCT record by the unique constraint (master_file_id, outdoor_item_id)
            // 2) Ensure the OCT record exists for THIS (mf, oi, year, month)
            $octKey = [
                'master_file_id'  => $mfId,
                'outdoor_item_id' => $oiId,
                'year'            => $year,
                'month'           => $month,
            ];

            // Safe defaults on first create:
            $octVals = [
                'status'                => 'pending',
                'masterfile_created_at' => DB::raw("COALESCE(masterfile_created_at, NOW())"),
                'created_at'            => DB::raw("COALESCE(created_at, NOW())"),
                'updated_at'            => now(),
            ];

            // Make it idempotent and race-safe:
            DB::table('outdoor_coordinator_trackings')->updateOrInsert($octKey, $octVals);

            // Get the id for later field update:
            $octId = DB::table('outdoor_coordinator_trackings')->where($octKey)->value('id');

            // 3) Apply the same field change to the OCT row (so the list reflects it)
            if ($octId && $octHas($field)) {
                $update = $isDate ? [$field => ($value ?: null)] : [$field => $value];
                DB::table('outdoor_coordinator_trackings')
                    ->where('id', $octId)
                    ->update($update + ['updated_at' => now()]);
            }

            return response()->json([
                'success' => true,
                'data'    => ['tracking_id' => $octId, 'field' => $field, 'value' => $value],
            ]);
        }

        // ---------- OCT BASELINE PATH (by id OR by master_file_id[/outdoor_item_id]) ----------
        $octId = $data['id'] ?? null;

        if ($octId) {
            $exists = DB::table('outdoor_coordinator_trackings')->where('id', $octId)->exists();
            if (!$exists) {
                return response()->json(['success' => false, 'error' => 'Invalid OCT id for baseline mode'], 422);
            }
            if ($octHas($field)) {
                $update = in_array($field, $dateFields, true) ? [$field => ($value ?: null)] : [$field => $value];
                DB::table('outdoor_coordinator_trackings')->where('id', $octId)->update($update + ['updated_at' => now()]);
            } // else: ignore silently (still saved in OMD when in month mode)
            return response()->json(['success' => true, 'data' => ['tracking_id' => $octId, 'field' => $field, 'value' => $value]]);
        }

        // Create/find baseline row for master_file_id (optionally per-site)
        $mfId = (int)$data['master_file_id'];
        $query = DB::table('outdoor_coordinator_trackings')->where('master_file_id', $mfId);
        if (!empty($data['outdoor_item_id'])) {
            $query->where('outdoor_item_id', (int)$data['outdoor_item_id']);
        }
        $row = $query->first();

        if ($row) {
            if ($octHas($field)) {
                $update = in_array($field, $dateFields, true) ? [$field => ($value ?: null)] : [$field => $value];
                DB::table('outdoor_coordinator_trackings')->where('id', $row->id)->update($update + ['updated_at' => now()]);
            }
            return response()->json(['success' => true, 'data' => ['tracking_id' => $row->id, 'field' => $field, 'value' => $value]]);
        } else {
            $insert = [
                'master_file_id' => $mfId,
                'outdoor_item_id'=> $data['outdoor_item_id'] ?? null,
                'status'         => 'pending',
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
            if ($octHas($field)) {
                $insert[$field] = in_array($field, $dateFields, true) ? ($value ?: null) : $value;
            }
            $newId = DB::table('outdoor_coordinator_trackings')->insertGetId($insert);
            return response()->json(['success' => true, 'data' => ['tracking_id' => $newId, 'field' => $field, 'value' => $value]]);
        }
    } catch (Throwable $e) {
        Log::error('Outdoor upsert failed', [
            'payload' => $data,
            'error'   => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
        ]);
        return response()->json([
            'success' => false,
            'error'   => 'Server error: '.$e->getMessage(),
        ], 500);
    }
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
                // texts
                'site','payment','material','artwork','remarks','status',
                // new date pairs for texts
                'site_date','payment_date','material_date','artwork_date',
                // core dates
                'received_approval','sent_to_printer','collection_printer','installation','dismantle','next_follow_up',
                // notes for core dates
                'received_approval_note','sent_to_printer_note','collection_printer_note','installation_note','dismantle_note','next_follow_up_note',
            ];

            if (!in_array($field, $allowedFields)) {
                return response()->json(['error' => 'Field not allowed for editing.'], 400);
            }

            // Handle date fields
            $dateFields = [
                'site_date','payment_date','material_date','artwork_date',
                'received_approval','sent_to_printer','collection_printer','installation','dismantle','next_follow_up',
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

/**
 * EXPORT: 2 rows per record (Row1 dates, Row2 notes)
 * Headers: NO | Company | Person In Charge | Product | Site | Payment | Material Received | Artwork Done | Received Approval | Sent to Printer | Collection Printer | Installation | Dismantle | Next Follow Up | Status
 */
public function export(Request $request): StreamedResponse
{
    Log::info('Export started', [
        'month_requested' => $request->integer('month'),
        'all_params'      => $request->all(),
    ]);

    $month = $request->integer('month'); // 1..12

    // Base query (complete select)
    $q = DB::table('outdoor_coordinator_trackings as oct')
        ->join('master_files as mf', 'oct.master_file_id', '=', 'mf.id')
        ->select([
            'oct.id',
            DB::raw('COALESCE(mf.company, oct.client) as company'),
            DB::raw('oct.client as client'),
            'oct.product',
            'oct.site',

            // NOTE + DATE pairs
            'oct.payment',                 'oct.payment_date',
            'oct.material',                'oct.material_date',
            'oct.artwork',                 'oct.artwork_date',
            'oct.received_approval_note',  'oct.received_approval',
            'oct.sent_to_printer_note',    'oct.sent_to_printer',
            'oct.collection_printer_note', 'oct.collection_printer',
            'oct.installation_note',       'oct.installation',
            'oct.dismantle_note',          'oct.dismantle',
            'oct.next_follow_up_note',     'oct.next_follow_up',

            'oct.status',
        ]);

    if ($month) {
        $q->where(function ($w) use ($month) {
            $w->orWhereRaw("MONTH(oct.payment_date)=?", [$month])
              ->orWhereRaw("MONTH(oct.material_date)=?", [$month])
              ->orWhereRaw("MONTH(oct.artwork_date)=?", [$month])
              ->orWhereRaw("MONTH(oct.received_approval)=?", [$month])
              ->orWhereRaw("MONTH(oct.sent_to_printer)=?", [$month])
              ->orWhereRaw("MONTH(oct.collection_printer)=?", [$month])
              ->orWhereRaw("MONTH(oct.installation)=?", [$month])
              ->orWhereRaw("MONTH(oct.dismantle)=?", [$month])
              ->orWhereRaw("MONTH(oct.next_follow_up)=?", [$month]);
        });
    }

    $rows = $q->orderBy('oct.id')->get();

    if ($rows->isEmpty()) {
        // fallback to all
        $rows = DB::table('outdoor_coordinator_trackings as oct')
            ->join('master_files as mf', 'oct.master_file_id', '=', 'mf.id')
            ->select([
                'oct.id',
                DB::raw('COALESCE(mf.company, oct.client) as company'),
                DB::raw('oct.person_in_charge as person_in_charge'),
                'oct.product',
                'oct.site',
                'oct.payment',                 'oct.payment_date',
                'oct.material',                'oct.material_date',
                'oct.artwork',                 'oct.artwork_date',
                'oct.received_approval_note',  'oct.received_approval',
                'oct.sent_to_printer_note',    'oct.sent_to_printer',
                'oct.collection_printer_note', 'oct.collection_printer',
                'oct.installation_note',       'oct.installation',
                'oct.dismantle_note',          'oct.dismantle',
                'oct.next_follow_up_note',     'oct.next_follow_up',
                'oct.status',
            ])
            ->orderBy('oct.id')
            ->get();
    }

    // Bucket by product class
    $classifiedData = ['BB'=>[], 'TB'=>[], 'Buting'=>[], 'Other'=>[]];
    foreach ($rows as $r) {
        $p = strtoupper((string)($r->product ?? ''));
        if (str_contains($p, 'BB'))         $classifiedData['BB'][] = $r;
        elseif (str_contains($p, 'TB'))     $classifiedData['TB'][] = $r;
        elseif (str_contains($p, 'BUTING')) $classifiedData['Buting'][] = $r;
        else                                 $classifiedData['Other'][] = $r;
    }

    $filename   = 'outdoor-coordinator-'.($month ? "month-{$month}" : 'all').'.xlsx';
    $monthLabel = $month ? Carbon::createFromDate(now()->year, $month, 1)->format('F Y') : null;

    return response()->streamDownload(function () use ($classifiedData, $monthLabel) {
        $this->generateOutdoorCoordinatorXlsx($classifiedData, $monthLabel);
    }, $filename, [
        'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        'Cache-Control'       => 'no-cache, no-store, max-age=0',
    ]);
}

/**
 * Build XLSX using only setCellValue("A1", ...) — no setCellValueByColumnAndRow
 */
private function generateOutdoorCoordinatorXlsx(array $classifiedData, ?string $monthLabel): void
{
    $headers = [
        'NO','Company','Person In Charge','Product','Site',
        'Payment','Material Received','Artwork Done','Received Approval',
        'Sent to Printer','Collection Printer','Installation','Dismantle',
        'Next Follow Up','Status',
    ];
    $dateColLetters = ['F','G','H','I','J','K','L','M']; // F..M
    $lastCol = 'O'; // 15 columns (A..O)

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // ===== Top info (yellow) =====
    $r = 1;
    $sheet->setCellValue("A{$r}", 'OUTDOOR COORDINATOR TRACKING REPORT'); $sheet->mergeCells("A{$r}:{$lastCol}{$r}"); $r++;
    $sheet->setCellValue("A{$r}", 'Generated: '.now()->format('Y-m-d H:i:s')); $sheet->mergeCells("A{$r}:{$lastCol}{$r}"); $r++;
    $sheet->setCellValue("A{$r}", 'Month Filter: '.($monthLabel ?: 'All Data')); $sheet->mergeCells("A{$r}:{$lastCol}{$r}"); $r++;
    $total = array_sum(array_map('count', $classifiedData));
    $sheet->setCellValue("A{$r}", 'Total Records: '.$total); $sheet->mergeCells("A{$r}:{$lastCol}{$r}"); $r++;
    $topYellowRange = "A1:{$lastCol}{$r}";
    $r++; // spacer

    $titleRanges  = [];
    $headerRanges = [];
    $lastDataRow  = $r;
    $no = 1;

    foreach ($classifiedData as $cat => $rows) {
        if (empty($rows)) continue;

        // Sub-title
        $sheet->setCellValue("A{$r}", strtoupper($cat).' - '.count($rows).' Records');
        $sheet->mergeCells("A{$r}:{$lastCol}{$r}");
        $titleRanges[] = "A{$r}:{$lastCol}{$r}";
        $r++;

        // Header row 1
        $c = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue("{$c}{$r}", $h);
            $c++;
        }
        $headerRanges[] = "A{$r}:{$lastCol}{$r}";
        $r++;

        // Header row 2 (blank to keep the yellow double-row look)
        $headerRanges[] = "A{$r}:{$lastCol}{$r}";
        $r++;

        // Data (two rows each)
        foreach ($rows as $row) {
            // Row 1: dates
            $sheet->setCellValue("A{$r}", $no);
            $sheet->setCellValue("B{$r}", $row->company ?? '');
            $sheet->setCellValue("C{$r}", $row->person_in_charge ?? '');
            $sheet->setCellValue("D{$r}", $row->product ?? '');
            $sheet->setCellValue("E{$r}", $row->site ?? '');

            $sheet->setCellValue("F{$r}", $this->formatDate($row->payment_date));
            $sheet->setCellValue("G{$r}", $this->formatDate($row->material_date));
            $sheet->setCellValue("H{$r}", $this->formatDate($row->artwork_date));
            $sheet->setCellValue("I{$r}", $this->formatDate($row->received_approval));
            $sheet->setCellValue("J{$r}", $this->formatDate($row->sent_to_printer));
            $sheet->setCellValue("K{$r}", $this->formatDate($row->collection_printer));
            $sheet->setCellValue("L{$r}", $this->formatDate($row->installation));
            $sheet->setCellValue("M{$r}", $this->formatDate($row->dismantle));
            $sheet->setCellValue("N{$r}", $this->formatDate($row->next_follow_up));
            $sheet->setCellValue("O{$r}", $row->status ?? '');
            $r++;

            // Row 2: notes
            $sheet->setCellValue("A{$r}", '');
            $sheet->setCellValue("B{$r}", '');
            $sheet->setCellValue("C{$r}", '');
            $sheet->setCellValue("D{$r}", '');
            $sheet->setCellValue("E{$r}", '');

            $sheet->setCellValue("F{$r}", $row->payment ?? '');
            $sheet->setCellValue("G{$r}", $row->material ?? '');
            $sheet->setCellValue("H{$r}", $row->artwork ?? '');
            $sheet->setCellValue("I{$r}", $row->received_approval_note ?? '');
            $sheet->setCellValue("J{$r}", $row->sent_to_printer_note ?? '');
            $sheet->setCellValue("K{$r}", $row->collection_printer_note ?? '');
            $sheet->setCellValue("L{$r}", $row->installation_note ?? '');
            $sheet->setCellValue("M{$r}", $row->dismantle_note ?? '');
            $sheet->setCellValue("N{$r}", $row->next_follow_up_note ?? '');
            $sheet->setCellValue("O{$r}", '');
            $r++;

            $no++;
        }

        $r++; // spacer between categories
        $lastDataRow = $r - 1;
    }

    // ===== Styling =====
    // Yellow for top info
    $sheet->getStyle($topYellowRange)->applyFromArray([
        'fill' => ['fillType'=>Fill::FILL_SOLID, 'startColor'=>['argb'=>'FFFFFF00']],
        'font' => ['bold'=>true],
        'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER],
        'borders' => ['allBorders'=>['borderStyle'=>Border::BORDER_THIN]],
    ]);

    // Yellow for category titles and headers
    foreach ($titleRanges as $rg) {
        $sheet->getStyle($rg)->applyFromArray([
            'fill' => ['fillType'=>Fill::FILL_SOLID, 'startColor'=>['argb'=>'FFFFFF00']],
            'font' => ['bold'=>true],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders'=>['borderStyle'=>Border::BORDER_THIN]],
        ]);
    }
    foreach ($headerRanges as $rg) {
        $sheet->getStyle($rg)->applyFromArray([
            'fill' => ['fillType'=>Fill::FILL_SOLID, 'startColor'=>['argb'=>'FFFFFF00']],
            'font' => ['bold'=>true],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle'=>Border::BORDER_THIN]],
        ]);
    }

    // Borders for all cells
    $sheet->getStyle("A1:{$lastCol}{$lastDataRow}")->applyFromArray([
        'borders' => ['allBorders'=>['borderStyle'=>Border::BORDER_THIN]],
    ]);

    // Date number format mm/dd/yyyy for F..M
    foreach ($dateColLetters as $letter) {
        $sheet->getStyle("{$letter}1:{$letter}{$lastDataRow}")
              ->getNumberFormat()->setFormatCode('mm/dd/yyyy');
    }

    // Autosize
    foreach (range('A', $lastCol) as $letter) {
        $sheet->getColumnDimension($letter)->setAutoSize(true);
    }

    // Freeze after top info (first header section starts at row 6)
    $sheet->freezePane('A6');

    // Output
    (new Xlsx($spreadsheet))->save('php://output');
    $spreadsheet->disconnectWorksheets();
    unset($spreadsheet);
}

/** Format helper */
private function formatDate($date): string
{
    if ($date && !in_array($date, ['0000-00-00','0000-00-00 00:00:00','',null], true)) {
        try { return date('m/d/Y', strtotime((string)$date)); }
        catch (Throwable $e) { /* ignore */ }
    }
    return '';
}

}
