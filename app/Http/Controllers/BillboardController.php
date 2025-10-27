<?php

namespace App\Http\Controllers;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\ClientCompany;
use App\Models\User;
use App\Models\Billboard;
// use App\Models\BillboardImage;
use App\Models\Contractor;
use App\Models\State;
use App\Models\District;
use App\Models\Council;
use App\Models\Location;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PushNotificationController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use App\Exports\BillboardExport;
use Maatwebsite\Excel\Facades\Excel;

class BillboardController extends Controller
{
    public $user;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware(function ($request, $next) {
    //         $this->user = Auth::guard('web')->user();
    //         return $next($request);
    //     });
    // }

    /**
     * Show the projects page.
     */
    public function index()
    {
        // $user = Auth::guard('web')->user();
        // if (is_null($user) || !$user->can('billboard.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized to view any project. Contact system admin for access !');
        // }
        // $type = Project::distinct()->get(['type']);
        // return view('projects.index', compact('type'));

        $states = State::orderBy('name', 'ASC')->get();
        $districts = District::orderBy('name', 'ASC')->get();
        $locations = Location::orderBy('name', 'ASC')->get();
        $billboardTypes = Billboard::select('type', 'prefix')->distinct()->pluck('type', 'prefix');
        $billboardStatus = Billboard::distinct()->pluck('status');
        $billboardSize = Billboard::distinct()->pluck('size');
        $billboardLighting = Billboard::distinct()->pluck('lighting');
        $billboards = Billboard::leftJoin('locations', 'billboards.location_id', '=', 'locations.id')->get();

        $contractors = Contractor::all();
        $clientcompany = ClientCompany::all();

        // return view('workOrder.index', compact('clientcompany', 'projects', 'supervisors', 'technicians'));
        return view('billboard.index', compact(
            'states',
            'districts',
            'locations',
            'billboardTypes',
            'billboardStatus',
            'billboardSize',
            'billboardLighting',
            'contractors',
            'clientcompany',
            'billboards'
        ));
    }

    /**
     * Show the on going work order list.
     */
    public function list(Request $request)
    {
        $user = Auth::user();

        // Get user roles
        // $role = $user->roles->pluck('name')[0];

        // $userID = $this->user->id;

        $status = $request->input('status');
        $state = $request->input('state');
        $district = $request->input('district');
        $type     = $request->input('type');
        $site_type     = $request->input('site_type');
        $size     = $request->input('size');
        $searchValue    = trim(strtolower($request->input('search.value')));
        $start     = $request->input('start', 0);
        $limit     = $request->input('length', 25);

        $columns = [
            0 => 'billboards.site_number',
            1 => 'billboards.type',
            2 => 'billboards.size',
            3 => 'billboards.lighting',
            4 => 'locations.name',
            5 => 'districts.name',
            6 => 'billboards.created_at',
            7 => 'billboards.status',
            8 => 'billboards.id',
            9 => 'billboards.site_type',
        ];

        $orderColumnIndex   = $request->input('order.0.column');
        $orderColumnName    = $columns[$orderColumnIndex];
        $orderDirection     = $request->input('order.0.dir');

        $query = Billboard::select('billboards.*', 'locations.id as location_id', 'locations.council_id as council_id', 'locations.name as location_name', 'districts.id as district_id', 'districts.name as district_name', 'states.id as state_id', 'states.name as state_name')
            ->leftJoin('locations', 'billboards.location_id', '=', 'locations.id')
            ->leftJoin('districts', 'locations.district_id', '=', 'districts.id')
            ->leftJoin('councils', 'councils.id', '=', 'locations.council_id')
            ->leftJoin('states', 'districts.state_id', '=', 'states.id')
            ->orderBy($orderColumnName, $orderDirection)
            ->orderBy('billboards.id', 'desc');

        if ($status != "all") {
            $query->where('billboards.status', $status);
        }

        if ($state != "all") {
            $query->where('states.id', $state);
        }

        if ($district != "all") {
            $query->where('districts.id', $district);
        }

        if ($type != "all") {
            $query->where('billboards.type', $type);
        }

        if ($site_type != "all") {
            $query->where('billboards.site_type', $site_type);
        }

        if ($size != "all") {
            $query->where('billboards.size', $size);
        }

        // Get total records count
        $totalData = $query->count();

        if (!empty($searchValue)) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('billboards.site_number', 'LIKE', "%{$searchValue}%")
                    ->orWhere('locations.name', 'LIKE', "%{$searchValue}%")
                    ->orWhere('districts.name', 'LIKE', "%{$searchValue}%")
                    ->orWhere('states.name', 'LIKE', "%{$searchValue}%");
            });
        }

        // Get total filtered records count
        $totalFiltered = $query->count();

        if ($limit == -1) {
            // Export: get all filtered data (no pagination)
            $filteredData = $query->get();
        } else {
            // Normal request: paginate
            $filteredData = $query->skip($start)->take($limit)->get();
        }

        $data = array();

        foreach ($filteredData as $d) {
            $created_at = Carbon::parse($d->start_date)->format('d/m/y');

            $nestedData = array(
                'site_number'           => $d->site_number,
                'site_type'             => $d->site_type,
                'type'                  => $d->type, // display name
                'type_prefix'           => $d->prefix,
                'size'                  => $d->size,
                'lighting'              => $d->lighting,
                'location_id'           => $d->location_id,
                'state_id'              => $d->state_id,
                'district_id'           => $d->district_id,
                'council_id'            => $d->council_id,
                'location_name'         => $d->location_name,
                'region'                => $d->district_name . ', ' . $d->state_name,
                'gps_latitude'          => $d->gps_latitude,
                'gps_longitude'         => $d->gps_longitude,
                'gps_url'               => $d->gps_url,
                'traffic_volume'        => $d->traffic_volume,
                'status'                => $d->status,
                'created_at'            => $created_at,
                'status'                => $d->status,
                'id'                    => $d->id,
            );

            $data[] = $nestedData;
        }



        $json_data = array(
            "draw"              => intval($request->input('draw')),
            "recordsTotal"      => intval($totalData),
            "recordsFiltered"   => intval($totalFiltered),
            "data"              => $data,
        );

        return response()->json($json_data);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $userID = $user->id;

        // ✅ Validation
        $validated = Validator::make($request->all(), [
            'type'          => 'required|string',
            'size'          => 'required|string|max:50',
            'lighting'      => 'required|string', // adjust based on your allowed values
            'state'         => 'required|exists:states,id',
            'district'      => 'nullable|string|max:255',
            'council'       => 'required|exists:councils,id',
            'location'      => 'required|string|max:255',
            'land'          => 'required|string|max:10', // adjust if you only allow values like "PRIV" / "GOV"
            'gps_coordinate' => [
                'required',
                'regex:/^-?([0-8]?\d(\.\d+)?|90(\.0+)?),\s*-?(1[0-7]\d(\.\d+)?|180(\.0+)?)$/'
            ],
            'gps_url' => [
                'nullable',
                'regex:/^https:\/\/maps\.app\.goo\.gl\/[A-Za-z0-9]+$/'
            ],
            'trafficvolume' => 'nullable|integer|min:0',
            'siteType' => 'nullable|string|max:10',
        ]);

        if ($validated->fails()) {
            return response()->json(['error' => $validated->errors()->first()], 422);
        }

        DB::beginTransaction();

        try {
            $type           = $request->type;
            $size           = $request->size;
            $lighting       = $request->lighting;
            $state          = $request->state;
            $district       = $request->district;
            $council        = $request->council;
            $locationName   = $request->location;
            $land           = $request->land;
            $trafficvolume  = $request->trafficvolume;
            $siteType       = $request->siteType;
            $gpsUrl         = $request->gps_url;

            $coords = explode(',', $request->gps_coordinate);
            $gpslatitude = trim($coords[0]);
            $gpslongitude = trim($coords[1]);

            $districtId = null;

            if (!empty($district)) {
                if (is_numeric($district)) {
                    // existing district id
                    $districtId = (int)$district;
                } else {
                    // district typed by user → create new
                    $district = District::firstOrCreate(
                        ['name' => $district, 'state_id' => $request->state]
                    );
                    $districtId = $district->id;
                }
            }


            // Step 1: Ensure location exists (or create new)
            $location = Location::firstOrCreate([
                'name'        => $locationName,
                'district_id' => $districtId,
                'council_id'  => $council,
            ]);

            // Step 2: Fetch state code
            $stateCode = State::select('prefix')->where('id', $state)->firstOrFail();

            // Step 3: Running number
            $lastNumber = Billboard::whereHas('location.district.state', function ($query) use ($state) {
                $query->where('id', $state);
            })
                ->selectRaw("MAX(CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(site_number,'-',3),'-',-1) AS UNSIGNED)) as max_number")
                ->lockForUpdate() // ensures safe increment under concurrency
                ->value('max_number');

            $runningNumber = $lastNumber ? $lastNumber + 1 : 1;
            $formattedNumber = str_pad($runningNumber, 4, '0', STR_PAD_LEFT);

            $councilAbbv = Council::findOrFail($council)->abbreviation;

            // Step 4: Generate site_number
            $siteNumber = "{$type}-{$stateCode->prefix}-{$formattedNumber}-{$councilAbbv}-{$land}";

            $typeMap = [
                'BB' => 'Billboard',
                'TB' => 'Tempboard',
                'BU' => 'Bunting',
                'BN' => 'Banner',
            ];

            $prefix = $request->type;                // e.g. "TB"
            $type   = $typeMap[$prefix] ?? $prefix;  // e.g. "Tempboard"

            // Step 5: Create billboard
            $billboard = Billboard::create([
                'site_number'       => $siteNumber,
                'status'            => 1,
                'type'              => $type,
                'prefix'            => $prefix,
                'size'              => $size,
                'lighting'          => $lighting,
                'state'             => $state,
                'district'          => $districtId,
                'location_id'       => $location->id,
                'gps_longitude'     => $gpslongitude,
                'gps_latitude'      => $gpslatitude,
                'gps_url'           => $gpsUrl,
                'traffic_volume'    => $trafficvolume,
                'site_type'         => $siteType,
                'created_by'        => $userID,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Billboard created successfully.',
                'billboard_id' => $billboard->id,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }


    /**
     * Update status of billboard
     */
    public function update(Request $request)
    {
        $billboard = Billboard::find($request->id);

        if (!$billboard) {
            return response()->json([
                'success' => false,
                'message' => 'Billboard not found.'
            ], 404);
        }

        // validate (you can adjust rules)
        $request->validate([
            'id' => 'required|integer|exists:billboards,id',
            'type' => 'required|string|in:BB,TB,BU,BN', // Accept only these prefix values
            'size' => 'nullable|string|max:255',
            'lighting' => 'nullable|string|max:255',
            'state_id' => 'nullable|integer',
            'district_id' => 'nullable|string|max:255',
            'location_name' => 'nullable|string|max:255',
            'gps_coordinate' => [
                'required',
                'regex:/^-?([0-8]?\d(\.\d+)?|90(\.0+)?),\s*-?(1[0-7]\d(\.\d+)?|180(\.0+)?)$/'
            ],
            'gps_url' => [
                'nullable',
                'regex:/^https:\/\/maps\.app\.goo\.gl\/[A-Za-z0-9]+$/'
            ],
            'traffic_volume' => 'nullable|integer',
            'status' => 'nullable|integer',
            'site_type' => 'nullable|string|max:255',
        ]);

        try {
            // Ensure all queries successfully executed
            DB::beginTransaction();

            $billboard = Billboard::findOrFail($request->id);
            $location = Location::find($billboard->location_id);

            $coords = explode(',', $request->gps_coordinate);
            $gpslatitude = trim($coords[0]);
            $gpslongitude = trim($coords[1]);

            // Update location if it exists
            if ($location && $request->filled('location_name')) {
                $location->update([
                    'name' => $request->location_name,
                ]);
            }

            // ✅ handle district (id or new text) - only update if provided
            $districtId = null;

            if ($request->filled('district_id')) {
                if (is_numeric($request->district_id)) {
                    // existing district
                    $districtId = (int) $request->district_id;
                } else {
                    // new district name
                    $district = \App\Models\District::create([
                        'name' => $request->district_id,
                        'state_id' => $request->state_id,
                    ]);
                    $districtId = $district->id;
                }

                // Update location with new district if location exists
                if ($location) {
                    $location->update([
                        'district_id' => $districtId,
                    ]);
                }
            }

            // Map prefix to full type name
            $prefixMap = [
                'BB' => 'Billboard',
                'TB' => 'Tempboard',
                'BU' => 'Bunting',
                'BN' => 'Banner',
            ];

            $prefix = $request->type; // sent from hidden field, e.g., "BB"
            $fullType = $prefixMap[$prefix] ?? $prefix; // map to full name or use prefix as fallback

            // Prepare update data - only include fields that are present in the request
            $updateData = [
                'prefix'         => $prefix,    // always update type
                'type'           => $fullType,  // always update type
                'gps_latitude'   => $gpslatitude,
                'gps_longitude'  => $gpslongitude,
            ];

            // Only add fields to update if they are present in the request
            if ($request->filled('size')) {
                $updateData['size'] = $request->size;
            }

            if ($request->filled('lighting')) {
                $updateData['lighting'] = $request->lighting;
            }

            if ($request->filled('state_id')) {
                $updateData['state_id'] = $request->state_id;
            }

            if (array_key_exists('gps_url', $request->all())) {
                $updateData['gps_url'] = $request->gps_url !== '' ? $request->gps_url : null;
            }

            if (array_key_exists('traffic_volume', $request->all())) {
                $updateData['traffic_volume'] = $request->traffic_volume !== '' ? $request->traffic_volume : null;
            }

            if ($request->filled('status')) {
                $updateData['status'] = (int)$request->status;
            }

            if ($request->filled('site_type')) {
                $updateData['site_type'] = $request->site_type;
            }

            $billboard->update($updateData);

            // Ensure all queries successfully executed, commit the db changes
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Billboard created successfully.',
            ], 200);
        } catch (\Exception $e) {
            // If any queries fail, undo all changes
            DB::rollback();

            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Delete billboard + all associated images
     */
    public function delete(Request $request)
    {
        $id = $request->id;

        try {
            DB::beginTransaction();

            // Get billboard
            $billboard = Billboard::findOrFail($id);
            $siteNumber = $billboard->site_number;

            // Delete billboard record
            $billboard->delete();

            // Delete ALL associated images (dynamic cleanup)
            $directory = 'public/billboards';
            $files = Storage::files($directory);

            foreach ($files as $file) {
                if (str_starts_with(basename($file), $siteNumber . '_')) {
                    Storage::delete($file);
                }
            }

            DB::commit();

            return response()->json([
                "success" => "Billboard and all related images deleted successfully",
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }


    /**
     * View billboard details
     */
    public function redirectNewTab(Request $request)
    {

        $filter = $request->input('filter');
        $id = $request->input('id');

        $billboard_detail = Billboard::leftJoin('locations', 'locations.id', 'billboards.location_id')
            ->leftJoin('districts', 'districts.id', '=', 'locations.district_id')
            ->leftJoin('councils', 'councils.id', '=', 'locations.council_id')
            ->leftJoin('states', 'states.id', '=', 'districts.state_id')
            // ->leftJoin('billboard_images', 'billboard_images.billboard_id', 'billboards.id')
            ->select(
                'billboards.*',
                'locations.name as location_name',
                'locations.id as location_id',
                'districts.name as district_name',
                'districts.id as district_id',
                'councils.name as council_name',
                'councils.id as council_id',
                'councils.abbreviation as council_abbrv',
                'states.name as state_name',
                'states.id as state_id',
                // 'billboard_images.image_path as billboard_image'
            )
            ->where('billboards.id', $request->id)
            ->first();

        // $billboard_images = BillboardImage::where('billboard_id', $request->id)->get();

        $states = State::orderBy('name', 'ASC')->get();
        $districts = District::orderBy('name', 'ASC')->get();
        $councils = Council::orderBy('name', 'ASC')->get();
        $locations = Location::orderBy('name', 'ASC')->get();

        return view('billboard.detail', compact(
            'billboard_detail',
            // 'billboard_images',
            'states',
            'districts',
            'councils',
            'locations'
        ));
    }

    public function viewMap(Request $request)
    {

        $filter = $request->input('filter');
        $id = $request->input('id');

        $billboard_detail = Billboard::leftJoin('locations', 'locations.id', 'billboards.location_id')
            ->leftJoin('districts', 'districts.id', '=', 'locations.district_id')
            ->leftJoin('states', 'states.id', '=', 'districts.state_id')
            ->leftJoin('billboard_images', 'billboard_images.billboard_id', 'billboards.id')
            ->select(
                'billboards.*',
                'locations.name as location_name',
                'districts.name as district_name',
                'states.name as state_name',
                'billboard_images.image_path as billboard_image'
            )
            ->where('billboards.id', $request->id)
            ->first();

        // $billboard_images = BillboardImage::where('billboard_id', $request->id)->get();


        // Convert to Dubai time
        // $dubaiTime = Carbon::parse($open_WO_DetailId->created_dt);

        // Add new formatted date, month, and year fields to the object
        // $open_WO_DetailId->created_dt = $dubaiTime->format('F j, Y \a\t g:i A');

        // if ($open_WO_DetailId !== null) {

        //     $woActivities = WorkOrderActivity::select(
        //         'work_order_activity.id as comment_id',
        //         'comments',
        //         'comment_by',
        //         'work_order_activity.created_at as created_at',
        //         'name',
        //     )
        //     ->leftJoin('users', 'users.id', 'work_order_activity.comment_by')
        //     ->where('work_order_id', '=', $request->id);

        // if($filter){
        //     if ($filter == 'new') {
        //         $woActivities->orderBy('created_at', 'desc');
        //     } elseif ($filter == 'old'){
        //         $woActivities->orderBy('created_at', 'asc');
        //     }
        // }
        // // ->get();

        // $woActivities = $woActivities->get();

        // $woActivities->transform(function ($woActivity) {
        //     // Convert to Dubai time
        //     $created_dt = Carbon::parse($woActivity->created_at);

        //     // Add new formatted date, month, and year fields to the object
        //     $woActivity->created_dt = $created_dt->format('F j, Y \a\t g:i A');

        //     // Fetch related attachments
        //     $attachments = WorkOrderActivityAttachment::select('id', 'url')
        //     ->where('wo_activity_id', '=', $woActivity->comment_id)
        //     ->get();

        //     // Add attachments to the activity
        //     $woActivity->attachments = $attachments;

        //     return $woActivity;
        // });

        // $gg = $woActivities->get();

        // $WoOrHistory = WorkOrderHistory::select(
        //     'work_order_history.id',
        //     'work_order_history.status',
        //     'work_order_history.status_changed_by',
        //     'work_order_history.assigned_teamleader',
        //     'work_order_history.assign_to_technician',
        //     'users.id as user_id',
        //     'users.name as user_name',
        // )
        // ->leftJoin('users', 'users.id', '=', DB::raw('CASE 
        //         WHEN work_order_history.status = "NEW" THEN work_order_history.status_changed_by 
        //         WHEN work_order_history.status = "ACCEPTED" THEN work_order_history.status_changed_by 
        //         WHEN work_order_history.status = "ASSIGNED_SP" THEN work_order_history.status_changed_by                     
        //         WHEN work_order_history.status = "ACCEPTED_TECHNICIAN" THEN work_order_history.assign_to_technician
        //         WHEN work_order_history.status = "STARTED" THEN work_order_history.assign_to_technician
        //         WHEN work_order_history.status = "COMPLETED" THEN work_order_history.assign_to_technician
        //         ELSE NULL 
        //     END'))
        // ->where('work_order_history.work_order_id', '=', $request->id)
        // ->get();

        // return view('workOrderProfile.index', compact('open_WO_DetailId', 'imageData', 'WoOrObImageBefore', 'WoOrObImageAfter', 'WoOrHistory'));
        return view('billboard.detail', compact('billboard_detail', 'billboard_images'));

        // } else {
        //     // Handle the case when no record is found
        //     // You can return an error message or redirect the user
        //     return response()->json(['error' => 'No record found with the provided ID'], 404);
        // }
    }

    public function downloadPdf($id)
    {
        // $billboard = Billboard::with(['location.district.state', 'images'])->findOrFail($id);

        // $billboard = Billboard::with([
        //     'location' => function ($query) {
        //         $query->with([
        //             'district' => function ($query) {
        //                 $query->with('state');
        //             }
        //         ]);
        //     },
        //     'images'
        // ])->findOrFail($id);

        $billboard = Billboard::with([
            'location' => function ($query) {
                $query->with([
                    'district' => function ($query) {
                        $query->with('state');
                    }
                ]);
            }
        ])->findOrFail($id);

        // Hardcode images for testing
        $billboard->images = [
            'storage/billboards/' . $billboard->site_number . '_1.png',
            'storage/billboards/' . $billboard->site_number . '_2.png',
        ];

        $pdf = PDF::loadView('billboard.export', compact('billboard'))
            ->setPaper('A4', 'landscape'); // 👈 Set orientation here

        return $pdf->download('billboard-detail-' . $billboard->site_number . '.pdf');
    }

    public function downloadPdfClient($id)
    {
        // $billboard = Billboard::with([
        //     'location' => function ($query) {
        //         $query->with([
        //             'district' => function ($query) {
        //                 $query->with('state');
        //             }
        //         ]);
        //     }
        // ])->select(
        //     'billboards.*',
        //     DB::raw("CONCAT(
        //                     CASE 
        //                         WHEN states.name = 'Kuala Lumpur' THEN 'KL'
        //                         WHEN states.name = 'Selangor' THEN 'SEL'
        //                         WHEN states.name = 'Negeri Sembilan' THEN 'N9'
        //                         WHEN states.name = 'Melaka' THEN 'MLK'
        //                         WHEN states.name = 'Johor' THEN 'JHR'
        //                         WHEN states.name = 'Perak' THEN 'PRK'
        //                         WHEN states.name = 'Pahang' THEN 'PHG'
        //                         WHEN states.name = 'Terengganu' THEN 'TRG'
        //                         WHEN states.name = 'Kelantan' THEN 'KTN'
        //                         WHEN states.name = 'Perlis' THEN 'PLS'
        //                         WHEN states.name = 'Kedah' THEN 'KDH'
        //                         WHEN states.name = 'Penang' THEN 'PNG'
        //                         WHEN states.name = 'Sarawak' THEN 'SWK'
        //                         WHEN states.name = 'Sabah' THEN 'SBH'
        //                         WHEN states.name = 'Labuan' THEN 'LBN'
        //                         WHEN states.name = 'Putrajaya' THEN 'PJY'
        //                         ELSE states.name
        //                     END, ' - ', districts.name
        //                 ) as area")
        // )->findOrFail($id);

        $billboard = Billboard::with(['location.district.state'])
            ->leftJoin('locations', 'billboards.location_id', '=', 'locations.id')
            ->leftJoin('districts', 'locations.district_id', '=', 'districts.id')
            ->leftJoin('states', 'districts.state_id', '=', 'states.id')
            ->select(
                'billboards.*',
                DB::raw("CONCAT(
                            CASE 
                                WHEN states.name = 'Kuala Lumpur' THEN 'KL'
                                WHEN states.name = 'Selangor' THEN 'SEL'
                                WHEN states.name = 'Negeri Sembilan' THEN 'N9'
                                WHEN states.name = 'Melaka' THEN 'MLK'
                                WHEN states.name = 'Johor' THEN 'JHR'
                                WHEN states.name = 'Perak' THEN 'PRK'
                                WHEN states.name = 'Pahang' THEN 'PHG'
                                WHEN states.name = 'Terengganu' THEN 'TRG'
                                WHEN states.name = 'Kelantan' THEN 'KTN'
                                WHEN states.name = 'Perlis' THEN 'PLS'
                                WHEN states.name = 'Kedah' THEN 'KDH'
                                WHEN states.name = 'Penang' THEN 'PNG'
                                WHEN states.name = 'Sarawak' THEN 'SWK'
                                WHEN states.name = 'Sabah' THEN 'SBH'
                                WHEN states.name = 'Labuan' THEN 'LBN'
                                WHEN states.name = 'Putrajaya' THEN 'PJY'
                                ELSE states.name
                            END, ' - ', districts.name
                        ) as area")
            )
            ->orderByRaw("area ASC")
            ->findOrFail($id);

        // Hardcode images for testing
        $billboard->images = [
            'storage/billboards/' . $billboard->site_number . '_1.png',
            'storage/billboards/' . $billboard->site_number . '_2.png',
        ];

        $pdf = PDF::loadView('billboard.export_client', compact('billboard'))
            ->setPaper('A4', 'landscape'); // 👈 Set orientation here

        return $pdf->download('billboard-detail-' . $billboard->site_number . '.pdf');
    }

    public function exportListPdf(Request $request)
    {
        // ↑ Increase PHP memory limit right at the start
        ini_set('memory_limit', '1024M'); // 1GB
        ini_set('max_execution_time', 300); // 5 minutes
        set_time_limit(300);

        $query = Billboard::with(['location.district.state']);

        // ✅ Apply selected IDs first (like Excel export)
        if ($request->filled('billboard_ids')) {
            $ids = explode(',', $request->billboard_ids);
            $ids = array_map('intval', $ids);
            $query->whereIn('id', $ids);
        } else {
            // Apply filters only if no specific selection
            if ($request->filled('state_id') && $request->state_id !== 'all') {
                $query->whereHas('location.district.state', fn($q) => $q->where('id', $request->state_id));
            }

            if ($request->filled('district_id') && $request->district_id !== 'all') {
                $query->whereHas('location.district', fn($q) => $q->where('id', $request->district_id));
            }

            if ($request->filled('type') && $request->type !== 'all') {
                $query->where('type', $request->type);
            }

            if ($request->filled('site_type') && $request->site_type !== 'all') {
                $query->where('site_type', $request->site_type);
            }

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->filled('size') && $request->size !== 'all') {
                $query->where('size', $request->size);
            }
        }

        $billboards = $query->get();

        // ✅ Create image manager (GD driver is default in most servers)
        $manager = new ImageManager(new Driver());

        foreach ($billboards as $billboard) {
            $resizedImages = [];

            $imagePaths = [
                public_path('storage/billboards/' . $billboard->site_number . '_1.png'),
                public_path('storage/billboards/' . $billboard->site_number . '_2.png'),
            ];

            foreach ($imagePaths as $fullPath) {
                if (file_exists($fullPath)) {
                    // Resize and compress
                    $resized = $manager->read($fullPath)
                        ->scale(width: 600)   // auto keeps aspect ratio
                        ->toJpeg(70);         // compress quality

                    $resizedImages[] = 'data:image/jpeg;base64,' . base64_encode($resized->toString());
                }
            }

            $billboard->images = $resizedImages;
        }

        // 📂 Filename
        $filename = 'billboards-master';
        $date = now()->format('Y-m-d');

        if ($request->filled('district_id') && $request->district_id !== 'all') {
            $district = District::find($request->district_id);
            if ($district) {
                $filename = 'billboards-' . Str::slug($district->name) . '-' . $date;
            }
        } elseif ($request->filled('state_id') && $request->state_id !== 'all') {
            $state = State::find($request->state_id);
            if ($state) {
                $filename = 'billboards-' . Str::slug($state->name) . '-' . $date;
            }
        } else {
            $filename .= '-' . $date;
        }

        $pdf = PDF::loadView('billboard.exportlist', compact('billboards'))
            ->setPaper('A4', 'landscape'); // 👈 Set orientation here

        return $pdf->download($filename . '.pdf');
    }

    public function exportListPdfClient(Request $request)
    {
        // ↑ Increase PHP memory limit right at the start
        ini_set('memory_limit', '1024M'); // 1GB
        ini_set('max_execution_time', 300); // 5 minutes
        set_time_limit(300);

        $query = Billboard::with(['location.district.state'])
            ->leftJoin('locations', 'billboards.location_id', '=', 'locations.id')
            ->leftJoin('districts', 'locations.district_id', '=', 'districts.id')
            ->leftJoin('states', 'districts.state_id', '=', 'states.id')
            ->select(
                'billboards.*',
                DB::raw("CONCAT(
                            CASE 
                                WHEN states.name = 'Kuala Lumpur' THEN 'KL'
                                WHEN states.name = 'Selangor' THEN 'SEL'
                                WHEN states.name = 'Negeri Sembilan' THEN 'N9'
                                WHEN states.name = 'Melaka' THEN 'MLK'
                                WHEN states.name = 'Johor' THEN 'JHR'
                                WHEN states.name = 'Perak' THEN 'PRK'
                                WHEN states.name = 'Pahang' THEN 'PHG'
                                WHEN states.name = 'Terengganu' THEN 'TRG'
                                WHEN states.name = 'Kelantan' THEN 'KTN'
                                WHEN states.name = 'Perlis' THEN 'PLS'
                                WHEN states.name = 'Kedah' THEN 'KDH'
                                WHEN states.name = 'Penang' THEN 'PNG'
                                WHEN states.name = 'Sarawak' THEN 'SWK'
                                WHEN states.name = 'Sabah' THEN 'SBH'
                                WHEN states.name = 'Labuan' THEN 'LBN'
                                WHEN states.name = 'Putrajaya' THEN 'PJY'
                                ELSE states.name
                            END, ' - ', districts.name
                        ) as area")
            )
            ->orderByRaw("area ASC");

        // ✅ Apply selected IDs first (like Excel export)
        if ($request->filled('billboard_ids')) {
            $ids = explode(',', $request->billboard_ids);
            $ids = array_map('intval', $ids);
            $query->whereIn('billboards.id', $ids);
        } else {
            // Apply filters only if no specific selection
            if ($request->filled('state_id') && $request->state_id !== 'all') {
                $query->whereHas('location.district.state', fn($q) => $q->where('id', $request->state_id));
            }

            if ($request->filled('district_id') && $request->district_id !== 'all') {
                $query->whereHas('location.district', fn($q) => $q->where('id', $request->district_id));
            }

            if ($request->filled('type') && $request->type !== 'all') {
                $query->where('type', $request->type);
            }

            if ($request->filled('site_type') && $request->site_type !== 'all') {
                $query->where('site_type', $request->site_type);
            }

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->filled('size') && $request->size !== 'all') {
                $query->where('size', $request->size);
            }
        }

        $billboards = $query->get();

        // ✅ Create image manager (GD driver is default in most servers)
        $manager = new ImageManager(new Driver());

        foreach ($billboards as $billboard) {
            $resizedImages = [];

            $imagePaths = [
                public_path('storage/billboards/' . $billboard->site_number . '_1.png'),
                public_path('storage/billboards/' . $billboard->site_number . '_2.png'),
            ];

            foreach ($imagePaths as $fullPath) {
                if (file_exists($fullPath)) {
                    // Resize and compress
                    $resized = $manager->read($fullPath)
                        ->scale(width: 600)   // auto keeps aspect ratio
                        ->toJpeg(70);         // compress quality

                    $resizedImages[] = 'data:image/jpeg;base64,' . base64_encode($resized->toString());
                }
            }

            $billboard->images = $resizedImages;
        }

        // 📂 Filename
        $filename = 'billboards-master';
        $date = now()->format('Y-m-d');

        if ($request->filled('district_id') && $request->district_id !== 'all') {
            $district = District::find($request->district_id);
            if ($district) {
                $filename = 'billboards-' . Str::slug($district->name) . '-' . $date;
            }
        } elseif ($request->filled('state_id') && $request->state_id !== 'all') {
            $state = State::find($request->state_id);
            if ($state) {
                $filename = 'billboards-' . Str::slug($state->name) . '-' . $date;
            }
        } else {
            $filename .= '-' . $date;
        }

        $pdf = PDF::loadView('billboard.exportlist_client', compact('billboards'))
            ->setPaper('A4', 'landscape'); // 👈 Set orientation here

        return $pdf->download($filename . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->only(['status', 'state', 'district', 'type', 'site_type', 'size']);
        $selectedIds = $request->input('billboard_ids');

        // ✅ Base name logic (match title rules in BillboardExport)
        $baseName = "Billboard_List";
        if (!empty($filters['site_type']) && $filters['site_type'] !== "all") {
            $baseName = ucfirst($filters['site_type']) . "_Stock_Inventory_List";
        } elseif (!empty($filters['type']) && $filters['type'] !== "all") {
            $baseName = ucfirst($filters['type']) . "_Stock_Inventory_List";
        }

        // ✅ Final filename
        $fileName = $baseName . "_" . now()->format('dmY') . ".xlsx";

        return Excel::download(new BillboardExport($filters, $selectedIds), $fileName);
    }











    public function uploadImage(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $siteNumber = $request->input('site_number');
            $extension = 'png';

            $directory = 'billboards';

            // ✅ Ensure directory exists on "public" disk
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            // ✅ Get existing files from "public" disk
            $existingFiles = Storage::disk('public')->files($directory);
            $siteFiles = array_filter($existingFiles, fn($f) => str_starts_with(basename($f), $siteNumber . '_'));

            if (count($siteFiles) >= 2) {
                return response()->json([
                    'message' => 'Maximum of 2 images already uploaded for this site.'
                ], 400);
            }

            // Sequence number
            $usedNumbers = [];
            foreach ($siteFiles as $f) {
                if (preg_match('/_(\d+)\.png$/', $f, $m)) {
                    $usedNumbers[] = (int)$m[1];
                }
            }

            $sequence = null;
            for ($i = 1; $i <= 2; $i++) {
                if (!in_array($i, $usedNumbers)) {
                    $sequence = $i;
                    break;
                }
            }

            if (!$sequence) {
                return response()->json([
                    'message' => 'Maximum of 2 images already uploaded for this site.'
                ], 400);
            }

            $filename = $siteNumber . '_' . $sequence . '.' . $extension;

            // Check file size
            $fileSize = $file->getSize();
            $imageData = null;

            if ($fileSize > 1024 * 1024) {
                // compress/resize if >1MB
                $imageData = (string) Image::read($file)
                    ->scale(width: 400)
                    ->toPng();
            } else {
                $imageData = file_get_contents($file->getRealPath());
            }

            // ✅ Save file to "public" disk
            Storage::disk('public')->put($directory . '/' . $filename, $imageData);

            // ✅ Get absolute file path for optimizer
            $fullPath = Storage::disk('public')->path($directory . '/' . $filename);

            // Optimize PNG
            try {
                $optimizer = OptimizerChainFactory::create();
                $optimizer->optimize($fullPath);
            } catch (\Throwable $e) {
                \Log::warning("PNG optimization skipped: " . $e->getMessage());
            }

            // ✅ Public URL
            $url = Storage::disk('public')->url($directory . '/' . $filename);

            return response()->json([
                'message'  => 'File uploaded successfully',
                'filename' => $filename,
                'url'      => $url
            ], 200, ['Content-Type' => 'application/json']);
        }

        return response()->json(['message' => 'No file uploaded'], 400);
    }




    public function deleteImage(Request $request)
    {
        $filename = $request->input('filename');
        $path = 'billboards/' . $filename; // ✅ Correct path relative to 'public' disk

        if (Storage::disk('public')->exists($path)) { // ✅ Use 'public' disk
            Storage::disk('public')->delete($path);   // ✅ Use 'public' disk
            return response()->json(['message' => 'Image deleted successfully'], 200);
        }

        return response()->json(['message' => 'File not found'], 404);
    }
}
