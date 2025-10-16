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
use App\Models\MasterFile;
use App\Models\OutdoorOngoingJob;
use App\Models\BillboardImage;
use App\Models\State;
use App\Models\District;
use App\Models\Location;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PushNotificationController;
use Illuminate\Support\Facades\Log;

class BillboardAvailabilityController extends Controller
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
        // if (is_null($this->user) || !$this->user->can('billboard.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized to view any project. Contact system admin for access !');
        // }
        $companies = ClientCompany::orderBy('name', 'ASC')->get();
        $states = State::orderBy('name', 'ASC')->get();
        $districts = District::orderBy('name', 'ASC')->get();
        $locations = Location::orderBy('name', 'ASC')->get();
        $types = Billboard::select('type', 'prefix')->distinct()->get();

        return view('billboard.availability.index', compact('companies', 'states', 'districts', 'locations', 'types'));
    }

    public function list(Request $request)
    {
        $user = Auth::user();

        // Get user roles
        $role = $user->roles->pluck('name')[0];

        $userID = $this->user->id;

        logger('sini boi: ');

        $columns = array(
            0 => 'site_number',
            1 => 'company_name',
            2 => 'location',
            3 => 'start_date',
            4 => 'end_date',
            5 => 'duration',
            6 => 'status',
            7 => 'remarks',
            8 => 'id',
            9 => 'billboard_id',
        );

        $limit              = $request->input('length');
        $start              = $request->input('start');
        $orderColumnIndex   = $request->input('order.0.column');
        // $orderColumnName    = $columns[$orderColumnIndex] ?? 'master_files.id';
        $orderColumnName    = $columns[$orderColumnIndex];
        $orderDirection     = $request->input('order.0.dir', 'desc');

        $filters = $this->extractFilters($request);

        $query = $this->baseBookingQuery();

        $this->applyBookingFilters($query, $filters);

        // Get total records count
        $totalData = $query->count();

        // Search (same as other endpoint)
        if (!empty($filters['search_value'])) {
            $searchValue = trim(strtolower($filters['search_value']));
            $query->where(function ($q) use ($searchValue) {
                $q->where('billboards.site_number', 'LIKE', "%{$searchValue}%")
                ->orWhere('client_companies.name', 'LIKE', "%{$searchValue}%")
                ->orWhere('locations.name', 'LIKE', "%{$searchValue}%")
                ->orWhere('districts.name', 'LIKE', "%{$searchValue}%")
                ->orWhere('states.name', 'LIKE', "%{$searchValue}%");
            });
        }

        $query->orderBy('locations.name', 'asc')
        ->orderBy($orderColumnName, $orderDirection);

        

        // Get total filtered records count
        $totalFiltered = $query->count();

        // Apply pagination
        $filteredData = $query->skip($start)->take($limit)->get();

        $data = array();

        logger('filtered data: ' . $filteredData);

        foreach ($filteredData as $d) {
            $created_at = Carbon::parse($d->created_at)->format('Y-m-d');

            $nestedData = array(
                'site_number'           => $d->site_number,
                'company_id'            => $d->company_id,
                'company_name'          => $d->company_name,
                'location_id'           => $d->location_id,
                'location_name'         => $d->location_name,
                'start_date'            => $d->start_date ? Carbon::parse($d->start_date)->format('d/m/y') : null,
                'end_date'              => $d->end_date ? Carbon::parse($d->end_date)->format('d/m/y') : null,
                'remarks'               => $d->remarks,
                'duration'              => ($d->start_date && $d->end_date) ? Carbon::parse($d->start_date)->diffInMonths(Carbon::parse($d->end_date)) + 1 : null,
                'created_at'            => $created_at,
                'status'                => $d->status,
                'id'                    => $d->id,
                'billboard_id'          => $d->billboard_id,
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

    public function getMonthlyBookingAvailability(Request $request)
    {
        $filters = $this->extractFilters($request);
        $billboards = $this->queryFilteredBillboards($filters);

        $results = [];
        foreach ($billboards as $index => $billboard) {

            // Use Carbon dates from filters
            $startDate = $filters['start_date'];
            $endDate   = $filters['end_date'];

            [$isAvailable, $nextAvailableDate] = $this->checkAvailability($billboard, $startDate, $endDate);

            // Build monthly blocks between start and end date
            $months = $this->buildMonthlyBlocks($billboard, $startDate, $endDate);

            $results[] = [
                'site_number'        => $billboard->site_number,
                'location'           => $billboard->location?->name ?? '',
                'area'               => $billboard->location->district->name . ', ' . $billboard->location->district->state->name,
                'site_type'          => $billboard->site_type ?? '-',
                'type'               => $billboard->type ?? '-',
                'size'               => $billboard->size ?? '-',
                'remarks'            => $billboard->remarks ?? '',
                'is_available'       => $isAvailable,
                'next_available_raw' => $isAvailable ? null : $nextAvailableDate,
                'months'             => $months,
            ];
        }

        $results = $this->sortAvailability($results);

        return response()->json([
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => count($results),
            'recordsFiltered' => count($results),
            'data'            => $results,
        ]);
    }

    public function getBillboardAvailability(Request $request)
    {
        $filters = $this->extractFilters($request);
        $billboards = $this->queryFilteredBillboards($filters);

        $results = [];
        foreach ($billboards as $billboard) {
            [$isAvailable, $nextAvailableDate] = $this->checkAvailability(
                $billboard,
                $filters['start_date'],
                $filters['end_date']
            );

            // ✅ take the first booking’s status if exists
            // $bookingStatus = $billboard->bookings->first()->status ?? null;

            $results[] = [
                'id'              => $billboard->id,
                'site_number'     => $billboard->site_number,

                // location
                'location_id'     => $billboard->location->id ?? null,
                'location_name'   => $billboard->location->name ?? '',

                // district
                'district_id'     => $billboard->location->district->id ?? null,
                'district_name'   => $billboard->location->district->name ?? '',

                // state
                'state_id'        => $billboard->location->district->state->id ?? null,
                'state_name'      => $billboard->location->district->state->name ?? '',

                'is_available'    => $isAvailable,
                // 'status'          => $bookingStatus, // ✅ actual booking status
                'next_available_raw' => $isAvailable ? null : $nextAvailableDate,
                'next_available'  => $isAvailable ? null : optional($nextAvailableDate)->format('d/m/Y'),
            ];
        }

        $results = $this->sortAvailability($results);

        // ✅ optional: extra safeguard, filter results by booking status if requested
        if (!empty($filters['status'])) {
            $results = array_values(array_filter($results, function ($item) use ($filters) {
                return $item['status'] === $filters['status'];
            }));
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => count($results),
            'recordsFiltered' => count($results),
            'data' => $results,
        ]);
    }


    private function extractFilters(Request $request)
    {
        return [
            'start_date' => $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfYear(),
            'end_date'   => $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now()->endOfYear(),
            'year'       => $request->input('year', now()->year),
            'state'      => $request->input('state'),
            'district'   => $request->input('district'),
            'location'   => $request->input('location'),
            'type'       => $request->input('type'),
            'site_type'       => $request->input('site_type'),
            'status'     => $request->input('status'),
            'search_value' => $request->input('search.value'),
            'order_column_index' => $request->input('order.0.column'),
            'order_dir' => $request->input('order.0.dir', 'asc'),
        ];
    }

    private function queryFilteredBillboards(array $filters)
    {
        $billboards = Billboard::with([
            'location.district.state',
            'outdoorItems' => function ($q) use ($filters) {
                $q->where(function ($query) use ($filters) {
                    $query->where('start_date', '<=', $filters['end_date'])
                        ->where('end_date', '>=', $filters['start_date']);
                });
            },
            // 'master_files.company_id'
        ])
        ->when($filters['state'], fn($q) => $q->whereHas('location.district.state', fn($q2) => $q2->where('id', $filters['state'])))
        ->when($filters['district'], fn($q) => $q->whereHas('location.district', fn($q2) => $q2->where('id', $filters['district'])))
        ->when($filters['location'], fn($q) => $q->where('location_id', $filters['location']))
        ->when($filters['type'], fn($q) => $q->where('prefix', $filters['type']))
        ->when($filters['site_type'], fn($q) => $q->where('billboards.site_type', $filters['site_type']))
        // ->when($filters['status'], function ($q) use ($filters) {
        //     $q->whereHas('bookings', function ($q2) use ($filters) {
        //         $q2->where('status', $filters['status']);
        //     });
        // })
        ->when(!empty($filters['search_value']), function ($q) use ($filters) {
            $search = $filters['search_value'];
            $q->where(function ($q2) use ($search) {
                $q2->where('billboards.site_number', 'LIKE', "%{$search}%")
                ->orWhereHas('location', fn($q3) => $q3->where('name', 'LIKE', "%{$search}%"))
                ->orWhereHas('location.district', fn($q3) => $q3->where('name', 'LIKE', "%{$search}%"))
                ->orWhereHas('location.district.state', fn($q3) => $q3->where('name', 'LIKE', "%{$search}%"));
            });
        })
        ->get();

        // --- PHP sort by main + sub location ---
        $billboards = $billboards->sort(function ($a, $b) {
            [$aMain, $aSub] = array_map('trim', explode(',', $a->location->name . ','));
            [$bMain, $bSub] = array_map('trim', explode(',', $b->location->name . ','));

            $cmp = strcmp($aMain, $bMain);
            return $cmp !== 0 ? $cmp : strcmp($aSub, $bSub);
        });

        return $billboards->values();
    }

    private function checkAvailability($billboard, Carbon $startDate, Carbon $endDate)
    {
        $isAvailable = true;
        $nextAvailableDate = null;

        foreach ($billboard->outdoorItems as $outdoorItem) {
            $bookingStart = Carbon::parse($outdoorItem->start_date);
            $bookingEnd = Carbon::parse($outdoorItem->end_date);

            if ($bookingStart->lte($endDate) && $bookingEnd->gte($startDate)) {
                $isAvailable = false;

                if (!$nextAvailableDate || $bookingEnd->gt($nextAvailableDate)) {
                    $nextAvailableDate = $bookingEnd->copy()->addDay();
                }
                break;
            }
        }

        return [$isAvailable, $nextAvailableDate];
    }

    private function sortAvailability(array $items)
    {
        return collect($items)
            ->sort(function ($a, $b) {
                // 1️⃣ Not available first
                $availabilityA = $a['is_available'] ? 1 : 0;
                $availabilityB = $b['is_available'] ? 1 : 0;
                if ($availabilityA !== $availabilityB) {
                    return $availabilityA <=> $availabilityB;
                }

                // 2️⃣ Sort by location name safely
                $locA = $a['location'] ?? '';
                $locB = $b['location'] ?? '';
                return strcmp($locA, $locB);
            })
            ->values()
            ->all();
    }


    private function buildMonthlyBlocks($billboard, Carbon $startDate, Carbon $endDate)
    {
        $months = [];
        $processedMonths = [];

        $current = $startDate->copy()->startOfMonth();

        while ($current->lte($endDate)) {
            $monthKey = $current->format('Y-m');

            if (in_array($monthKey, $processedMonths)) {
                $current->addMonth();
                continue;
            }

            $matchedBooking = null;

            foreach ($billboard->outdoorItems as $outdoorItem) {
                $bookingStart = Carbon::parse($outdoorItem->start_date);
                $bookingEnd   = Carbon::parse($outdoorItem->end_date);

                $monthStart = $current->copy()->startOfMonth();
                $monthEnd   = $current->copy()->endOfMonth();

                logger($outdoorItem);

                if ($bookingStart->lte($monthEnd) && $bookingEnd->gte($monthStart)) {
                    $matchedBooking = $outdoorItem;

                    $spanStart = max($bookingStart, $monthStart)->copy()->startOfMonth();
                    $spanEnd   = min($bookingEnd, $endDate)->copy()->startOfMonth();
                    $span = $spanStart->diffInMonths($spanEnd) + 1;

                    for ($m = 0; $m < $span; $m++) {
                        $processedMonths[] = $spanStart->copy()->addMonths($m)->format('Y-m');
                    }

                    $status = $outdoorItem->masterFiles->status;

                    $colorClass = match ($status) {
                        'pending_payment' => 'bg-red-600 text-white',
                        'pending_install' => 'bg-blue-600 text-white',
                        'ongoing'         => 'bg-green-600 text-white',
                        'completed'       => 'bg-yellow-400 text-black',
                        'dismantle'       => 'bg-gray-600 text-white',
                        default           => 'bg-gray-400 text-black',
                    };

                    $months[] = [
                        'month'      => $current->format('m'),
                        'year'       => $current->year,
                        'span'       => $span,
                        'text'       => optional($outdoorItem->clientCompany)->name
                                        ? $outdoorItem->clientCompany->name . ' (' . $bookingStart->format('d/m/Y') . '–' . $bookingEnd->format('d/m/Y') . ')'
                                        : 'Booked (' . $bookingStart->format('d/m/Y') . '–' . $bookingEnd->format('d/m/Y') . ')',
                        'color'      => $colorClass,
                        'booking_id' => $outdoorItem->id, // ✅ Add booking_id here
                        'status'     => $status, // optional: helpful for frontend
                        'client'      => optional($outdoorItem->clientCompany)->name ?? null, // ✅ client name
                        'start_date'  => $bookingStart->format('d/m/Y'), // ✅ booking start
                        'end_date'    => $bookingEnd->format('d/m/Y'),   // ✅ booking end
                        'remarks'     => $outdoorItem->remarks,
                    ];

                    logger('monthhh: ' , $months);

                    break;
                }
            }

            if (!$matchedBooking) {
                $months[] = [
                    'month'      => $current->format('m'),
                    'year'       => $current->year,
                    'span'       => 1,
                    'text'       => '',
                    'color'      => '',
                    'booking_id' => null, // ✅ explicitly null for empty cells
                    'status'     => null,
                    'client'     => null,
                    'start_date' => null,
                    'end_date'   => null,
                ];
            }

            $current->addMonth();
        }

        return $months;
    }

    private function baseBookingQuery()
    {
        return MasterFile::select(
            'master_files.*',
            'billboards.id as billboard_id',
            'billboards.site_number as site_number',
            'client_companies.name as company_name',
            'locations.id as location_id',
            'locations.name as location_name',
            'districts.id as district_id',
            'districts.name as district_name',
            'states.id as state_id',
            'states.name as state_name'
        )
        ->leftJoin('outdoor_items', 'outdoor_items.master_file_id', '=', 'master_files.id')
        ->leftJoin('client_companies', 'client_companies.id', '=', 'master_files.company_id')
        ->leftJoin('billboards', 'billboards.id', '=', 'outdoor_items.billboard_id')
        ->leftJoin('locations', 'locations.id', '=', 'billboards.location_id')
        ->leftJoin('districts', 'districts.id', '=', 'locations.district_id')
        ->leftJoin('states', 'states.id', '=', 'districts.state_id');
    }


    private function applyBookingFilters($query, $filters)
    {
        return $query
            ->when(!empty($filters['start_date']) && !empty($filters['end_date']), function ($q) use ($filters) {
                $q->where(function ($sub) use ($filters) {
                    $sub->where('outdoor_items.start_date', '<=', $filters['end_date'])
                        ->where('outdoor_items.end_date', '>=', $filters['start_date']);
                });
            })
            ->when(!empty($filters['state']), fn($q) => $q->where('states.id', $filters['state']))
            ->when(!empty($filters['district']), fn($q) => $q->where('districts.id', $filters['district']))
            ->when(!empty($filters['location']), fn($q) => $q->where('locations.id', $filters['location']))
            ->when(!empty($filters['status']), fn($q) => $q->where('master_files.status', $filters['status']))
            ->when(!empty($filters['client']), fn($q) => $q->where('master_files.company_id', $filters['client']));
    }







    /**
     * Update status of work order
     */
    public function updateStatus(Request $request)
    {
        $billboard = MasterFile::findOrFail($request->id);
        $billboard->status = $request->status;
        $billboard->remarks = $request->remarks;
        $billboard->save();

        return response()->json(['success' => true]);
    }

    public function downloadPdf($id)
    {
        // $billboard = Billboard::with(['location.district.state', 'images'])->findOrFail($id);

        $billboard = Billboard::with([
            'location' => function ($query) {
                $query->with([
                    'district' => function ($query) {
                        $query->with('state');
                    }
                ]);
            },
            'billboard_images'
        ])->findOrFail($id);

        $pdf = PDF::loadView('billboard.export', compact('billboard'))
        ->setPaper('A4', 'landscape'); // 👈 Set orientation here

        return $pdf->download('billboard-detail-' . $billboard->site_number . '.pdf');
    }

    public function exportListPdf(Request $request)
    {
        $query = Billboard::with(['location.district.state', 'billboard_images']);

        if ($request->filled('state_id') && $request->state_id !== 'all') {
            $query->whereHas('location.district.state', fn($q) => $q->where('id', $request->state_id));
        }

        if ($request->filled('district_id') && $request->district_id !== 'all') {
            $query->whereHas('location.district', fn($q) => $q->where('id', $request->district_id));
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('size') && $request->size !== 'all') {
            $query->where('size', $request->size);
        }

        $billboards = $query->get();

        // Get filename based on state or district
        $filename = 'billboards-master';
        $date = Carbon::now()->format('Y-m-d');

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
            ->setPaper('a4', 'landscape');

        return $pdf->download($filename . '.pdf');
    }
}