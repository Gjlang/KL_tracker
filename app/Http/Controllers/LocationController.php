<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\State;
use App\Models\District;
use App\Models\Council;
use App\Models\Location;

class LocationController extends Controller
{
    // ✅ Get districts by state
    public function getDistrictsByState(Request $request)
    {
        $stateId = $request->state_id;

        $districts = District::where('state_id', $stateId)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($districts);
    }

    // ✅ Get councils by district
    public function getCouncils(Request $request)
    {
        $stateId = $request->state_id;

        $councils = Council::where(function ($query) use ($stateId) {
                $query->where('state_id', $stateId)
                    ->orWhereNull('state_id'); // ✅ include KKR
            })
            ->select('id', 'name', 'abbreviation')
            ->orderBy('name')
            ->get();

        return response()->json($councils);
    }


    // ✅ Get locations by district
    public function getLocationsByDistrict(Request $request)
    {
        $districtId = $request->district_id;

        $locations = Location::join('billboards', 'billboards.location_id', '=', 'locations.id')
        ->where('locations.district_id', $districtId)
        ->select(
            'locations.id as id',
            'locations.name as name',
            'billboards.id as billboard_id',
            'billboards.site_number'
        )
        ->orderBy('locations.name')
        ->get();

        return response()->json($locations);
    }

    // ✅ Get all districts
    public function getAllDistricts()
    {
        return response()->json(
            District::orderBy('name', 'ASC')->get()
        );
    }
}
