<?php

namespace App\Http\Controllers;

use App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Billboard;
use App\Models\Client;
use App\Models\ClientCompany;
use App\Models\Contractor;
use App\Models\StockInventory;
use App\Models\StockInventoryTransaction;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;


class StockInventoryController extends Controller
{
    public $user;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
     public function __construct()
    {
        // $this->middleware(function ($request, $next) {
        //     $this->user = Auth::guard('web')->user();
        //     return $next($request);
        // });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // if (is_null($this->user) || !$this->user->can('client.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized to view any client. Contact system admin for access !');
        // }

        // Get clients data
        $clients = Client::leftJoin('client_companies', 'client_companies.id', '=', 'clients.company_id')
        ->select('clients.*', 'client_companies.name as company_name')
        ->where('clients.status', '=', '1')
        ->get();

        // Get user data
        $users = User::where('id', '!=', Auth::id())->get();


        // Get client company data
        $clientcompany = ClientCompany::all();

        // Get contractor data
        $contractors = Contractor::all();

        // Get billboard data
        $billboards = Billboard::leftJoin('locations', 'billboards.location_id', '=', 'locations.id')->get();

        return view('stockInventory.index', compact('clients', 'users', 'clientcompany',  'contractors', 'billboards'));
    }

    protected function getBgocContractorId(): int
{
    // Prefer env/config; fallback to row named "BGOC"; last resort = 1
    $id = (int) (config('app.bgoc_contractor_id') ?? env('BGOC_CONTRACTOR_ID', 0));
    if ($id > 0 && DB::table('contractors')->where('id', $id)->exists()) {
        return $id;
    }

    $named = DB::table('contractors')->where('name', 'like', '%BGOC%')->value('id');
    if ($named) return (int)$named;

    // Create one if still missing
    $newId = DB::table('contractors')->insertGetId([
        'company_name' => 'BGOC', // 👈 REQUIRED FIELD
        'name' => 'BGOC',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    return (int) $newId;
}



public function list(Request $request)
{
    $limit = $request->input('length');
    $start = $request->input('start');
    $search = $request->input('search.value');

    // Subquery for IN transactions aggregated per stock_inventory
    $inSub = DB::table('stock_inventory_transactions as t_in')
        ->select(
            't_in.stock_inventory_id',
            DB::raw("GROUP_CONCAT(t_in.id ORDER BY t_in.id SEPARATOR '|||') as transaction_in_ids"),
            DB::raw("GROUP_CONCAT(quantity ORDER BY t_in.id SEPARATOR '|||') as quantity_in"),
            DB::raw("GROUP_CONCAT(COALESCE(remarks, '') ORDER BY t_in.id SEPARATOR '|||') as remarks_in"),
            DB::raw("GROUP_CONCAT(transaction_date ORDER BY t_in.id SEPARATOR '|||') as date_in"),
            DB::raw("GROUP_CONCAT(COALESCE(client_companies.id, '') ORDER BY t_in.id SEPARATOR '|||') as client_in_ids"),
            DB::raw("GROUP_CONCAT(COALESCE(client_companies.name, '') ORDER BY t_in.id SEPARATOR '|||') as client_in_name"),
            DB::raw("GROUP_CONCAT(COALESCE(CONCAT(billboards.site_number, ' - ', locations.name), '') ORDER BY t_in.id SEPARATOR '|||') as site_in"),
            DB::raw("GROUP_CONCAT(COALESCE(billboards.type, '') ORDER BY t_in.id SEPARATOR '|||') as billboard_type_in"),
            DB::raw("GROUP_CONCAT(COALESCE(billboards.size, '') ORDER BY t_in.id SEPARATOR '|||') as billboard_size_in")
        )
        ->leftJoin('client_companies', 'client_companies.id', '=', 't_in.client_id')
        ->leftJoin('billboards', 'billboards.id', '=', 't_in.billboard_id')
        ->leftJoin('locations', 'locations.id', '=', 'billboards.location_id')
        ->where('t_in.type', 'in')
        ->groupBy('t_in.stock_inventory_id');

    // Subquery for OUT transactions aggregated per stock_inventory
    $outSub = DB::table('stock_inventory_transactions as t_out')
        ->select(
            't_out.stock_inventory_id',
            DB::raw("GROUP_CONCAT(t_out.id ORDER BY t_out.id SEPARATOR '|||') as transaction_out_ids"),
            DB::raw("GROUP_CONCAT(quantity ORDER BY t_out.id SEPARATOR '|||') as quantity_out"),
            DB::raw("GROUP_CONCAT(COALESCE(remarks, '') ORDER BY t_out.id SEPARATOR '|||') as remarks_out"),
            DB::raw("GROUP_CONCAT(transaction_date ORDER BY t_out.id SEPARATOR '|||') as date_out"),
            DB::raw("GROUP_CONCAT(COALESCE(client_companies.id, '') ORDER BY t_out.id SEPARATOR '|||') as client_out_ids"),
            DB::raw("GROUP_CONCAT(COALESCE(client_companies.name, '') ORDER BY t_out.id SEPARATOR '|||') as client_out_name"),
            DB::raw("GROUP_CONCAT(COALESCE(CONCAT(billboards.site_number, ' - ', locations.name), '') ORDER BY t_out.id SEPARATOR '|||') as site_out"),
            DB::raw("GROUP_CONCAT(COALESCE(billboards.type, '') ORDER BY t_out.id SEPARATOR '|||') as billboard_type_out"),
            DB::raw("GROUP_CONCAT(COALESCE(billboards.size, '') ORDER BY t_out.id SEPARATOR '|||') as billboard_size_out")
        )
        ->leftJoin('client_companies', 'client_companies.id', '=', 't_out.client_id')
        ->leftJoin('billboards', 'billboards.id', '=', 't_out.billboard_id')
        ->leftJoin('locations', 'locations.id', '=', 'billboards.location_id')
        ->where('t_out.type', 'out')
        ->groupBy('t_out.stock_inventory_id');

    // Apply date filter INSIDE subqueries
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $startDate = $request->start_date;
        $endDate   = $request->end_date;

        $inSub->whereBetween('t_in.transaction_date', [$startDate, $endDate]);
        $outSub->whereBetween('t_out.transaction_date', [$startDate, $endDate]);
    }

    $query = StockInventory::select(
        'stock_inventories.*',
        'stock_inventories.balance_contractor',
        'stock_inventories.balance_bgoc',
        'contractors.name as contractor_name',
        'contractors.company_name as contractor_company',
        'contractors.phone as contractor_phone',
        'in_agg.transaction_in_ids',
        'in_agg.quantity_in',
        'in_agg.remarks_in',
        'in_agg.date_in',
        'in_agg.client_in_name',
        'in_agg.site_in',
        'in_agg.billboard_type_in',
        'in_agg.billboard_size_in',
        'out_agg.transaction_out_ids',
        'out_agg.quantity_out',
        'out_agg.remarks_out',
        'out_agg.date_out',
        'out_agg.client_out_name',
        'out_agg.site_out',
        'out_agg.billboard_type_out',
        'out_agg.billboard_size_out'
    )
    ->leftJoinSub($inSub, 'in_agg', function($join){
        $join->on('in_agg.stock_inventory_id', '=', 'stock_inventories.id');
    })
    ->leftJoinSub($outSub, 'out_agg', function($join){
        $join->on('out_agg.stock_inventory_id', '=', 'stock_inventories.id');
    })
    ->leftJoin('contractors', 'contractors.id', '=', 'stock_inventories.contractor_id')
    // ❌ Removed filter to show inventories without transactions
    // ->where(function($q) {
    //     $q->whereNotNull('in_agg.transaction_in_ids')
    //       ->orWhereNotNull('out_agg.transaction_out_ids');
    // })
    ->orderBy('stock_inventories.id', 'asc');

    // Apply Filters
    if ($request->filled('contractor_id')) {
        $query->where('stock_inventories.contractor_id', $request->contractor_id);
    }

    if ($request->filled('client_id')) {
        $query->where(function ($q) use ($request) {
            $q->whereRaw('FIND_IN_SET(?, in_agg.client_in_ids)', [$request->client_id])
              ->orWhereRaw('FIND_IN_SET(?, out_agg.client_out_ids)', [$request->client_id]);
        });
    }

    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            $q->where('contractors.name', 'like', "%{$search}%")
              ->orWhere('contractors.company_name', 'like', "%{$search}%")
              ->orWhere('in_agg.client_in_name', 'like', "%{$search}%")
              ->orWhere('out_agg.client_out_name', 'like', "%{$search}%")
              ->orWhere('in_agg.site_in', 'like', "%{$search}%")
              ->orWhere('out_agg.site_out', 'like', "%{$search}%")
              ->orWhere('in_agg.remarks_in', 'like', "%{$search}%")
              ->orWhere('out_agg.remarks_out', 'like', "%{$search}%");
        });
    }

    $totalData = (clone $query)->count('stock_inventories.id');

    $data = $query->skip($start)->take($limit)->get()->flatMap(function($d){
        // IN data
        $inIds     = $d->transaction_in_ids ? explode('|||', $d->transaction_in_ids) : [];
        $inDates   = $d->date_in ? explode('|||', $d->date_in) : [];
        $inRemarks = $d->remarks_in ? explode('|||', $d->remarks_in) : [];
        $inQty     = $d->quantity_in ? explode('|||', $d->quantity_in) : [];
        $inClients = $d->client_in_name ? explode('|||', $d->client_in_name) : [];
        $inSites   = $d->site_in ? explode('|||', $d->site_in) : [];
        $inTypes   = $d->billboard_type_in ? explode('|||', $d->billboard_type_in) : [];
        $inSizes   = $d->billboard_size_in ? explode('|||', $d->billboard_size_in) : [];

        // OUT data
        $outIds     = $d->transaction_out_ids ? explode('|||', $d->transaction_out_ids) : [];
        $outDates   = $d->date_out ? explode('|||', $d->date_out) : [];
        $outRemarks = $d->remarks_out ? explode('|||', $d->remarks_out) : [];
        $outQty     = $d->quantity_out ? explode('|||', $d->quantity_out) : [];
        $outClients = $d->client_out_name ? explode('|||', $d->client_out_name) : [];
        $outSites   = $d->site_out ? explode('|||', $d->site_out) : [];
        $outTypes   = $d->billboard_type_out ? explode('|||', $d->billboard_type_out) : [];
        $outSizes   = $d->billboard_size_out ? explode('|||', $d->billboard_size_out) : [];

        // If NO transactions exist, return one summary row with empty details
        if (empty($inIds) && empty($outIds)) {
            return [[
                'contractor'          => ($d->contractor_company ?? '') . ' (' . ($d->contractor_name ?? '') . ')',
                'balance_contractor'  => $d->balance_contractor ?? 0,
                'balance_bgoc'        => $d->balance_bgoc ?? 0,

                // IN empty
                'transaction_in_id'   => '',
                'date_in'             => '',
                'remarks_in'          => '',
                'quantity_in'         => '',
                'client_in_name'      => '',
                'site_in'             => '',
                'billboard_type_in'   => '',
                'billboard_size_in'   => '',

                // OUT empty
                'transaction_out_id'  => '',
                'date_out'            => '',
                'remarks_out'         => '',
                'quantity_out'        => '',
                'client_out_name'     => '',
                'site_out'            => '',
                'billboard_type_out'  => '',
                'billboard_size_out'  => '',

                'stock_inventory_id'  => $d->id
            ]];
        }

        // Transactions exist → calculate maximum rows needed
        $rowCount = max(count($inIds), count($outIds));

        $rows = [];
        for ($i = 0; $i < $rowCount; $i++) {
            $rows[] = [
                'contractor' => ($d->contractor_company ?? '') . ' (' . ($d->contractor_name ?? '') . ')',
                'balance_contractor' => $d->balance_contractor ?? 0,
                'balance_bgoc' => $d->balance_bgoc ?? 0,

                // IN columns
                'transaction_in_id' => $inIds[$i] ?? '',
                'date_in' => (!empty($inDates[$i]) ? Carbon::parse($inDates[$i])->format('d/m/y') : ''),
                'remarks_in' => $inRemarks[$i] ?? '',
                'quantity_in' => $inQty[$i] ?? '',
                'client_in_name' => $inClients[$i] ?? '',
                'site_in' => $inSites[$i] ?? '',
                'billboard_type_in' => $inTypes[$i] ?? '',
                'billboard_size_in' => $inSizes[$i] ?? '',

                // OUT columns
                'transaction_out_id' => $outIds[$i] ?? '',
                'date_out' => (!empty($outDates[$i]) ? Carbon::parse($outDates[$i])->format('d/m/y') : ''),
                'remarks_out' => $outRemarks[$i] ?? '',
                'quantity_out' => $outQty[$i] ?? '',
                'client_out_name' => $outClients[$i] ?? '',
                'site_out' => $outSites[$i] ?? '',
                'billboard_type_out' => $outTypes[$i] ?? '',
                'billboard_size_out' => $outSizes[$i] ?? '',

                'stock_inventory_id' => $d->id
            ];
        }

        return $rows;
    });

    return response()->json([
        "draw" => intval($request->input('draw')),
        "recordsTotal" => $totalData,
        "recordsFiltered" => $totalData,
        "data" => $data,
    ]);
}

    public function editData($stockInventoryId, Request $request)
    {
        logger('woyyyyyyyyyyyy: ');
        $transactionInId  = $request->get('transaction_in_id');
        $transactionOutId = $request->get('transaction_out_id');

        $transactionIn  = $transactionInId
            ? StockInventoryTransaction::with(['contractor','client','billboard','stockInventory'])
                ->find($transactionInId)
            : null;

        $transactionOut = $transactionOutId
            ? StockInventoryTransaction::with(['contractor','client','billboard','stockInventory'])
                ->find($transactionOutId)
            : null;

        logger('in: ' . $transactionIn);
        logger('out: ' . $transactionOut);

        return response()->json([
            'in'  => $transactionIn ? [
                'id'                => $transactionIn->id,
                'contractor_id'     => $transactionIn->stockInventory->contractor_id ?? null,
                'contractor_name'   => $transactionIn->contractor->name ?? '',
                'balance_contractor'=> $transactionIn->stockInventory->balance_contractor ?? 0,
                'balance_bgoc'      => $transactionIn->stockInventory->balance_bgoc ?? 0,
                'transaction_date'  => $transactionIn->transaction_date ? Carbon::parse($transactionIn->transaction_date)->format('Y-m-d') : null,
                'client_id'         => $transactionIn->client_id,
                'client_name'       => $transactionIn->client->name ?? '',
                'billboard_id'      => $transactionIn->billboard_id,
                'site_number'       => $transactionIn->billboard->site_number ?? '',
                'type'              => $transactionIn->billboard->type,
                'size'              => $transactionIn->billboard->size ?? '',
                'quantity'          => $transactionIn->quantity,
                'remarks'           => $transactionIn->remarks,
            ] : null,

            'out' => $transactionOut ? [
                'id'                => $transactionOut->id,
                'contractor_id'     => $transactionOut->stockInventory->contractor_id ?? null,
                'contractor_name'   => $transactionOut->contractor->name ?? '',
                'balance_contractor'=> $transactionOut->stockInventory->balance_contractor ?? 0,
                'balance_bgoc'      => $transactionOut->stockInventory->balance_bgoc ?? 0,
                'transaction_date'  => $transactionOut->transaction_date ? Carbon::parse($transactionOut->transaction_date)->format('Y-m-d') : null,
                'client_id'         => $transactionOut->client_id,
                'client_name'       => $transactionOut->client->name ?? '',
                'billboard_id'      => $transactionOut->billboard_id,
                'site_number'       => $transactionOut->billboard->site_number ?? '',
                'type'              => $transactionOut->billboard->type,
                'size'              => $transactionOut->billboard->size ?? '',
                'quantity'          => $transactionOut->quantity,
                'remarks'           => $transactionOut->remarks,
            ] : null,
        ]);
    }


    // MODIFIED CODE FOR CONTRACTOR - CONTRACTOR TRANSFER 2.0
   public function create(Request $request)
{
    $validator = Validator::make($request->all(), [
        // Top-level contractor is the SOURCE in transfer mode
        'contractor_id'         => 'required|exists:contractors,id',
        'from_contractor_id'    => 'nullable|exists:contractors,id',

        'remarks_in'            => 'nullable|string',
        'remarks_out'           => 'nullable|string',
        'balance_contractor'    => 'nullable|integer',
        'balance_bgoc'          => 'nullable|integer',

        'date_in'               => 'nullable|date',
        'date_out'              => 'nullable|date',

        'sites_in'                      => 'nullable|array',
        'sites_in.*.id'                 => 'nullable|exists:billboards,id',
        'sites_in.*.qty'                => 'nullable|integer|min:0',
        'sites_in.*.client_id'          => 'nullable|integer',
        'sites_in.*.client_type'        => 'nullable|string|in:contractor,client',

        'sites_out'                     => 'nullable|array',
        'sites_out.*.id'                => 'nullable|exists:billboards,id',
        'sites_out.*.qty'               => 'nullable|integer|min:0',
        'sites_out.*.client_id'         => 'nullable|integer',
        'sites_out.*.client_type'       => 'nullable|string|in:contractor,client',
    ]);

    // Optional: conditional existence checks depending on client_type
    $validator->after(function ($v) use ($request) {
        foreach (($request->input('sites_in') ?? []) as $i => $row) {
            if (($row['client_type'] ?? null) === 'contractor') {
                if (!Contractor::whereKey($row['client_id'] ?? null)->exists()) {
                    $v->errors()->add("sites_in.$i.client_id", 'Selected contractor does not exist.');
                }
            } elseif (($row['client_type'] ?? null) === 'client') {
                if (!ClientCompany::whereKey($row['client_id'] ?? null)->exists()) {
                    $v->errors()->add("sites_in.$i.client_id", 'Selected client does not exist.');
                }
            }
        }
        foreach (($request->input('sites_out') ?? []) as $i => $row) {
            if (($row['client_type'] ?? null) === 'contractor') {
                if (!Contractor::whereKey($row['client_id'] ?? null)->exists()) {
                    $v->errors()->add("sites_out.$i.client_id", 'Selected contractor does not exist.');
                }
            } elseif (($row['client_type'] ?? null) === 'client') {
                if (!ClientCompany::whereKey($row['client_id'] ?? null)->exists()) {
                    $v->errors()->add("sites_out.$i.client_id", 'Selected client does not exist.');
                }
            }
        }
    });

    $validated = $validator->validate();

    try {
        $userId = Auth::id() ?? 1;

        $inventory = DB::transaction(function () use ($validated, $userId) {
            $sourceId = (int)$validated['contractor_id'];

            // Detect contractor-to-contractor transfer in either list
            $isContractorTransfer =
                collect($validated['sites_in'] ?? [])->contains(fn ($r) => ($r['client_type'] ?? null) === 'contractor' && !empty($r['client_id'])) ||
                collect($validated['sites_out'] ?? [])->contains(fn ($r) => ($r['client_type'] ?? null) === 'contractor' && !empty($r['client_id']));

            $returnInventory = null;

            if ($isContractorTransfer) {
                // Source contractor inventory (e.g., Waqas)
                $from = StockInventory::firstOrNew(['contractor_id' => $sourceId]);
                $from->balance_contractor = $from->balance_contractor ?? 0;
                $from->balance_bgoc       = $from->balance_bgoc ?? 0;
                $from->save();

                // Helper to move qty from source to a target contractor
                $transferToContractor = function (array $row) use (&$from, $validated, $userId, $sourceId, &$returnInventory) {
                    $qty = (int)($row['qty'] ?? 0);
                    if ($qty <= 0) return;

                    $targetId = (int)($row['client_id'] ?? 0);
                    if ($targetId <= 0) return;

                    if ($from->balance_contractor < $qty) {
                        $contractorName = Contractor::find($sourceId)?->name ?? "Unknown Contractor";
                        throw new \Exception("Error: Insufficient stock balance for contractor {$contractorName}.");
                    }

                    // Target contractor inventory (e.g., Arun)
                    $to = StockInventory::firstOrNew(['contractor_id' => $targetId]);
                    $to->balance_contractor = $to->balance_contractor ?? 0;
                    $to->balance_bgoc       = $to->balance_bgoc ?? 0;
                    $to->save();

                    // 1) Debit source - Write OUT transaction
                    $from->balance_contractor -= $qty;
                    $from->save();
                    $from->transactions()->create([
                        'billboard_id'       => $row['id'] ?? null,
                        'client_id'          => null, // contractor transfer has no client
                        'from_contractor_id' => $sourceId,
                        'to_contractor_id'   => $targetId,
                        'type'               => 'out',
                        'quantity'           => $qty,
                        'transaction_date'   => !empty($validated['date_out'])
                                                ? Carbon::parse($validated['date_out'])->format('Y-m-d H:i:s')
                                                : now(),
                        'remarks'            => $validated['remarks_out'] ?? ($validated['remarks_in'] ?? null),
                        'created_by'         => $userId,
                    ]);

                    // 2) Credit target - Write IN transaction
                    $to->balance_contractor += $qty;
                    $to->save();
                    $to->transactions()->create([
                        'billboard_id'       => $row['id'] ?? null,
                        'client_id'          => null, // contractor transfer has no client
                        'from_contractor_id' => $sourceId,
                        'to_contractor_id'   => $targetId,
                        'type'               => 'in',
                        'quantity'           => $qty,
                        'transaction_date'   => !empty($validated['date_in'])
                                                ? Carbon::parse($validated['date_in'])->format('Y-m-d H:i:s')
                                                : now(),
                        'remarks'            => $validated['remarks_in'] ?? ($validated['remarks_out'] ?? null),
                        'created_by'         => $userId,
                    ]);

                    // so the response can include the latest target inventory updated
                    $returnInventory = $to;
                };

                // Process any contractor-target rows in sites_in (your sample uses this)
                foreach ($validated['sites_in'] ?? [] as $row) {
                    if (($row['client_type'] ?? null) === 'contractor' && !empty($row['client_id'])) {
                        $transferToContractor($row);
                    }
                }

                // Also support contractor-target rows in sites_out (mirrored behavior)
                foreach ($validated['sites_out'] ?? [] as $row) {
                    if (($row['client_type'] ?? null) === 'contractor' && !empty($row['client_id'])) {
                        $transferToContractor($row);
                    }
                }

                // Return the last updated target inventory so UI can show recipient's IN record
                return $returnInventory ?? $from;
            }

            // -------------------------
            // Default (BGOC) mode below
            // -------------------------
            $inventory = StockInventory::firstOrNew(['contractor_id' => $sourceId]);
            $inventory->balance_contractor = $inventory->balance_contractor ?? 0;
            $inventory->balance_bgoc       = $inventory->balance_bgoc ?? 0;
            $inventory->save();

            // IN transactions (from BGOC/client to contractor)
            foreach ($validated['sites_in'] ?? [] as $site) {
                if (($site['client_type'] ?? 'client') === 'contractor') {
                    // already handled in transfer-block
                    continue;
                }

                $qty = (int)($site['qty'] ?? 0);
                if ($qty <= 0) continue; // Skip if qty is 0

                // Update balances
                $inventory->balance_contractor += $qty;

                if (($inventory->balance_bgoc ?? 0) > 0) {
                    $inventory->balance_bgoc -= $qty;
                    if ($inventory->balance_bgoc < 0) $inventory->balance_bgoc = 0;
                }
                $inventory->save();

                // ✅ Write IN transaction to stock_inventory_transactions
                $inventory->transactions()->create([
                    'billboard_id'       => $site['id'] ?? null,
                    'client_id'          => $site['client_id'] ?? null,
                    'from_contractor_id' => null,
                    'to_contractor_id'   => null,
                    'type'               => 'in', // ✅ Exact ENUM value
                    'quantity'           => $qty,
                    'transaction_date'   => !empty($validated['date_in'])
                                            ? Carbon::parse($validated['date_in'])->format('Y-m-d H:i:s')
                                            : now(),
                    'remarks'            => $validated['remarks_in'] ?? null,
                    'created_by'         => $userId,
                ]);
            }

            // OUT transactions (from contractor to BGOC/client)
            foreach ($validated['sites_out'] ?? [] as $site) {
                if (($site['client_type'] ?? 'client') === 'contractor') {
                    // already handled in transfer-block
                    continue;
                }

                $qty = (int)($site['qty'] ?? 0);
                if ($qty <= 0) continue; // Skip if qty is 0

                // Update balances
                $inventory->balance_bgoc += $qty;
                $inventory->balance_contractor = max(0, ($inventory->balance_contractor ?? 0) - $qty);
                $inventory->save();

                // ✅ Write OUT transaction to stock_inventory_transactions
                $inventory->transactions()->create([
                    'billboard_id'       => $site['id'] ?? null,
                    'client_id'          => $site['client_id'] ?? null,
                    'from_contractor_id' => $sourceId,
                    'to_contractor_id'   => null,
                    'type'               => 'out', // ✅ Exact ENUM value
                    'quantity'           => $qty,
                    'transaction_date'   => !empty($validated['date_out'])
                                            ? Carbon::parse($validated['date_out'])->format('Y-m-d H:i:s')
                                            : now(),
                    'remarks'            => $validated['remarks_out'] ?? null,
                    'created_by'         => $userId,
                ]);

                // Update BGOC contractor (id = 1) stock if that is your convention
                $bgocId = $this->getBgocContractorId();
                $bgocInventory = StockInventory::firstOrNew(['contractor_id' => $bgocId]);
                $bgocInventory->balance_contractor = $bgocInventory->balance_contractor ?? 0;
                $bgocInventory->balance_contractor += $qty;
                $bgocInventory->save();

                // ✅ Write corresponding IN transaction for BGOC
                $bgocInventory->transactions()->create([
                    'billboard_id'       => $site['id'] ?? null,
                    'client_id'          => $site['client_id'] ?? null,
                    'from_contractor_id' => $sourceId,
                    'to_contractor_id'   => null,
                    'type'               => 'in', // ✅ BGOC receives IN
                    'quantity'           => $qty,
                    'transaction_date'   => !empty($validated['date_out'])
                                            ? Carbon::parse($validated['date_out'])->format('Y-m-d H:i:s')
                                            : now(),
                    'remarks'            => $validated['remarks_out'] ?? null,
                    'created_by'         => $userId,
                ]);
            }

            return $inventory;
        });

        return response()->json([
            'success' => true,
            'message' => 'Inventory saved successfully.',
            'data'    => $inventory->load('transactions'),
        ]);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 422);
    }
}

    public function edit(Request $request)
    {
        logger('edit request:', $request->all());

        $validated = Validator::make($request->all(), [
            'stock_inventory_id' => 'required|integer|exists:stock_inventories,id',

            'transaction_in_id'  => 'nullable|integer|exists:stock_inventory_transactions,id',
            'transaction_out_id' => 'nullable|integer|exists:stock_inventory_transactions,id',

            'contractor_id'      => 'nullable|exists:contractors,id',
            'from_contractor_id' => 'nullable|exists:contractors,id', // 🔹 NEW FIELD

            'remarks_in'         => 'nullable|string|max:255',
            'remarks_out'        => 'nullable|string|max:255',

            'client_in'          => 'nullable|integer|exists:client_companies,id',
            'site_in'            => 'nullable|integer|exists:billboards,id',
            'qty_in'             => 'nullable|integer|min:0',
            'date_in'            => 'nullable|date',

            'client_out'         => 'nullable|integer|exists:client_companies,id',
            'site_out'           => 'nullable|integer|exists:billboards,id',
            'qty_out'            => 'nullable|integer|min:0',
            'date_out'           => 'nullable|date',
        ])->validate();

        $inventory = StockInventory::findOrFail($validated['stock_inventory_id']);
        $userId    = Auth::id() ?? 1;

        logger('validated:');

        try {
            DB::transaction(function () use ($validated, $inventory, $userId) {
                logger('masuk sini:');
                // --- Update IN transaction ---
                if (!empty($validated['site_in'])) {
                    $inTransaction = !empty($validated['transaction_in_id'])
                        ? StockInventoryTransaction::findOrFail($validated['transaction_in_id'])
                        : new StockInventoryTransaction();

                    $qtyIn = (int) ($validated['qty_in'] ?? 0);

                    // --- Calculate difference if editing ---
                    $oldQty = $inTransaction->exists ? (int) $inTransaction->quantity : 0;
                    $diff   = $qtyIn - $oldQty; // 🔹 positive = increase, negative = reduce

                    // --- Save transaction ---
                    $inTransaction->stock_inventory_id = $inventory->id;
                    $inTransaction->type               = 'in';
                    $inTransaction->billboard_id       = $validated['site_in'];
                    $inTransaction->client_id          = $validated['client_in'] ?? null;
                    $inTransaction->quantity           = $qtyIn;
                    $inTransaction->transaction_date   = !empty($validated['date_in'])
                                                            ? Carbon::parse($validated['date_in'])->format('Y-m-d H:i:s')
                                                            : now();
                    $inTransaction->remarks    = $validated['remarks_in'] ?? null;
                    $inTransaction->created_by = $userId;
                    $inTransaction->save();

                    // --- Update balances based on difference ---
                    $inventory->balance_contractor += $diff;   // 🔹 only adjust by diff

                    if (($inventory->balance_bgoc ?? 0) > 0) {
                        $inventory->balance_bgoc -= $diff;     // 🔹 reverse adjust BGOC
                        if ($inventory->balance_bgoc < 0) {
                            $inventory->balance_bgoc = 0;
                        }
                    }

                    $inventory->save();
                }


                // --- Update OUT transaction ---
                if (!empty($validated['site_out'])) {
                    $outTransaction = !empty($validated['transaction_out_id'])
                        ? StockInventoryTransaction::findOrFail($validated['transaction_out_id'])
                        : new StockInventoryTransaction();

                    $qtyOut = (int) ($validated['qty_out'] ?? 0);

                    // --- Calculate difference if editing ---
                    $oldQty = $outTransaction->exists ? (int) $outTransaction->quantity : 0;
                    $diff   = $qtyOut - $oldQty;   // 🔹 positive = more taken out, negative = return/reduce

                    // --- Save transaction ---
                    $outTransaction->stock_inventory_id = $inventory->id;
                    $outTransaction->type               = 'out';
                    $outTransaction->billboard_id       = $validated['site_out'];
                    $outTransaction->client_id          = $validated['client_out'] ?? null;
                    $outTransaction->quantity           = $qtyOut;
                    $outTransaction->transaction_date   = !empty($validated['date_out'])
                                                            ? Carbon::parse($validated['date_out'])->format('Y-m-d H:i:s')
                                                            : now();
                    $outTransaction->remarks    = $validated['remarks_out'] ?? null;
                    $outTransaction->created_by = $userId;
                    $outTransaction->save();

                    // --- Deduct from selected contractor stock ---
                    if (!empty($validated['from_contractor_id'])) {
                        $contractorInventory = StockInventory::firstOrNew([
                            'contractor_id' => $validated['from_contractor_id'],
                        ]);

                        $contractorInventory->balance_contractor = $contractorInventory->balance_contractor ?? 0;

                        // 🔹 Adjust based on diff (instead of always deducting new qty)
                        $contractorInventory->balance_contractor -= $diff;

                        if ($contractorInventory->balance_contractor < 0) {
                            $contractorInventory->balance_contractor = 0;
                        }
                        $contractorInventory->save();
                    }

                    // --- Adjust BGOC contractor (id=1) ---
                    $bgocInventory = StockInventory::where('contractor_id', 1)->first();
                    if ($bgocInventory) {
                        $bgocInventory->balance_contractor = $bgocInventory->balance_contractor ?? 0;

                        // 🔹 When stock goes OUT from contractor → BGOC stock goes UP
                        $bgocInventory->balance_contractor += $diff;
                        if ($bgocInventory->balance_contractor < 0) {
                            $bgocInventory->balance_contractor = 0;
                        }
                        $bgocInventory->save();
                    }

                    // 🔹 Sync/create BGOC transaction (if needed)
                    if (!empty($validated['transaction_out_id']) && $bgocInventory) {
                        $bgocTransaction = StockInventoryTransaction::where('stock_inventory_id', $bgocInventory->id)
                            ->where('id', $validated['transaction_out_id'])
                            ->first();

                        if ($bgocTransaction) {
                            $bgocTransaction->update([
                                'billboard_id'     => $validated['site_out'],
                                'client_id'        => $validated['client_out'] ?? null,
                                'transaction_date' => !empty($validated['date_out'])
                                                        ? Carbon::parse($validated['date_out'])->format('Y-m-d H:i:s')
                                                        : now(),
                                'remarks'          => $validated['remarks_out'] ?? null,
                                'created_by'       => $userId,
                            ]);
                        } else {
                            $bgocInventory->transactions()->create([
                                'billboard_id'     => $validated['site_out'],
                                'client_id'        => $validated['client_out'] ?? null,
                                'type'             => 'out',
                                'quantity'         => $qtyOut,
                                'transaction_date' => !empty($validated['date_out'])
                                                        ? Carbon::parse($validated['date_out'])->format('Y-m-d H:i:s')
                                                        : now(),
                                'remarks'          => $validated['remarks_out'] ?? null,
                                'created_by'       => $userId,
                            ]);
                        }
                    }
                }


            });

            return response()->json([
                'success' => true,
                'message' => 'Stock inventory updated successfully.',
                'data'    => $inventory->load('transactions'),
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Delete stock inventory.
     */
    public function delete(Request $request)
    {
        $id = $request->id;

        $validator = Validator::make(
            $request->all(),
            [
                'id' => 'required|integer|exists:stock_inventory_transactions,id',
            ],
            [
                'id.exists' => 'The stock inventory transaction cannot be found.',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        try {
            DB::beginTransaction();

            $transaction = StockInventoryTransaction::findOrFail($id);
            $isTransfer = $transaction->from_contractor_id && $transaction->to_contractor_id;

            if ($isTransfer) {
                // find paired transaction
                $paired = StockInventoryTransaction::where('from_contractor_id', $transaction->from_contractor_id)
                    ->where('to_contractor_id', $transaction->to_contractor_id)
                    ->where('quantity', $transaction->quantity)
                    ->where('transaction_date', $transaction->transaction_date)
                    ->where('id', '!=', $transaction->id)
                    ->where('type', $transaction->type === 'in' ? 'out' : 'in')
                    ->first();

                // rollback balances for current
                $stockInventory = StockInventory::find($transaction->stock_inventory_id);
                if ($transaction->type === 'in') {
                    $stockInventory->balance_contractor -= $transaction->quantity;
                } elseif ($transaction->type === 'out') {
                    $stockInventory->balance_contractor += $transaction->quantity;
                }
                $stockInventory->balance_contractor = max(0, $stockInventory->balance_contractor);
                $transaction->delete();

                // check if no more transactions → delete inventory
                if ($stockInventory->transactions()->count() === 0) {
                    $stockInventory->delete();
                } else {
                    $stockInventory->save();
                }

                // rollback balances for paired
                if ($paired) {
                    $pairedInventory = StockInventory::find($paired->stock_inventory_id);
                    if ($paired->type === 'in') {
                        $pairedInventory->balance_contractor -= $paired->quantity;
                    } elseif ($paired->type === 'out') {
                        $pairedInventory->balance_contractor += $paired->quantity;
                    }
                    $pairedInventory->balance_contractor = max(0, $pairedInventory->balance_contractor);
                    $paired->delete();

                    if ($pairedInventory->transactions()->count() === 0) {
                        $pairedInventory->delete();
                    } else {
                        $pairedInventory->save();
                    }
                }
            } else {
                // normal (BGOC/client) transaction
                $stockInventory = StockInventory::find($transaction->stock_inventory_id);

                if ($transaction->type === 'in') {
                    $stockInventory->balance_contractor -= $transaction->quantity;
                } elseif ($transaction->type === 'out') {
                    $stockInventory->balance_bgoc -= $transaction->quantity;
                }

                $stockInventory->balance_contractor = max(0, $stockInventory->balance_contractor);
                $stockInventory->balance_bgoc       = max(0, $stockInventory->balance_bgoc);

                $transaction->delete();

                if ($stockInventory->transactions()->count() === 0) {
                    $stockInventory->delete();
                } else {
                    $stockInventory->save();
                }
            }

            DB::commit();

            return response()->json([
                "success" => "Transaction (and paired transfer if any) deleted successfully.",
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }


}
