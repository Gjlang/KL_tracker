<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OutdoorLookupController extends Controller
{
    public function sites(Request $request)
    {
        $q     = trim((string) $request->get('q', ''));
        $limit = min(max((int) $request->get('limit', 30), 1), 100);

        $rows = DB::table('billboards as b')
            ->leftJoin('locations as l', 'l.id', '=', 'b.location_id')
            ->when($q !== '', function ($qq) use ($q) {
                $like = '%' . $q . '%';
                $qq->where(function ($w) use ($like) {
                    $w->where('b.site_number', 'like', $like)
                      ->orWhere('l.name', 'like', $like)
                      ->orWhere('b.size', 'like', $like);
                });
            })
            ->orderBy('b.site_number')
            ->limit($limit)
            ->get([
                'b.id',
                'b.site_number',
                'b.size',
                'b.gps_latitude',
                'b.gps_longitude',
                'l.name as area',
            ]);

        $data = $rows->map(function ($r) {
            $lat = $r->gps_latitude;
            $lng = $r->gps_longitude;
            return [
                'value'  => (string) $r->id, // billboard id
                'label'  => sprintf('%s – %s (%s • %s,%s)',
                                    $r->site_number ?? '-',
                                    $r->area        ?? '-',
                                    $r->size        ?? '-',
                                    $lat            ?? '-',
                                    $lng            ?? '-'),
                'size'   => $r->size,
                'area'   => $r->area,
                'coords' => ($lat !== null && $lng !== null) ? ($lat.','.$lng) : null,
            ];
        })->values();

        return response()->json($data);
    }
}
