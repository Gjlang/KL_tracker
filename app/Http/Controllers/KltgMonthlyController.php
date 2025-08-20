<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\KltgMonthlyDetail;
use App\Models\MasterFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class KltgMonthlyController extends Controller
{
    public function upsert(Request $req)
{
    $data = $req->validate([
        'master_file_id' => 'required|integer|exists:master_files,id',
        'year'           => 'required|integer|min:2000|max:2100',
        'month'          => 'required|integer|min:1|max:12',
        'category'       => 'required|string|in:KLTG,VIDEO,ARTICLE,LB,EM',
        'field_type'     => 'nullable|string|in:text,date',
        'value'          => 'nullable|string',
        'value_text'     => 'nullable|string',
        'value_date'     => 'nullable|date',
        'is_date'        => 'nullable|date',
    ]);
    $data['category'] = strtoupper($data['category']);

    if (empty($data['field_type'])) {
        $isDate = (bool)($data['is_date'] ?? false);
        $data['field_type'] = $isDate ? 'date' : 'text';
        $data['value'] = $isDate ? ($data['value_date'] ?? null) : ($data['value_text'] ?? null);
    }
    if ($data['field_type'] === 'date' && !empty($data['value']) &&
        !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['value'])) {
        return response()->json(['message' => 'Invalid date format. Use YYYY-MM-DD.'], 422);
    }
    $key = [
        'master_file_id' => $data['master_file_id'],
        'year'           => $data['year'],
        'month'          => $data['month'],
        'category'       => $data['category'],
        'field_type'     => $data['field_type'], // text | date
    ];
    $attrs = [
        'value'      => $data['value'],
        'value_text' => $data['field_type'] === 'text' ? $data['value'] : null,
        'value_date' => $data['field_type'] === 'date' ? $data['value'] : null,
        'is_date'    => $data['field_type'] === 'date' ? 1 : 0,
        'type'       => 'PUBLICATION',
        'status'     => 'ACTIVE',
    ];

    $row = KltgMonthlyDetail::updateOrCreate($key, $attrs);

    return response()->json([
        'ok'  => true,
        'id'  => $row->id,
        'key' => $key,
    ]);
}

    public static function getDetailMap($masterFileIds, $year)
    {
        $details = KltgMonthlyDetail::whereIn('master_file_id', $masterFileIds)
            ->where('year', $year)
            ->get();

        $detailMap = [];
        foreach ($details as $detail) {
            $mfId = $detail->master_file_id;
            $month = (int)$detail->month;
            $category = strtoupper($detail->category);
            $fieldType = $detail->field_type;

            $detailMap[$mfId][$month][$category][$fieldType] = [
                'text' => $detail->value_text,
                'date' => $detail->value_date,
                'value' => $fieldType === 'date' ? $detail->value_date : $detail->value_text,
                'id' => $detail->id,
            ];
        }

        return $detailMap;
    }

    public function index(Request $request)
{
    $year = (int) $request->input('year', now()->year);

    $baseQuery = MasterFile::query()
        ->select([
            'id',
            'company',
            'product',
            DB::raw('COALESCE(product_category, "") as product_category'),
            'month as month_name',
            'date as start_date',
            'date_finish as end_date',
            DB::raw('CASE
                        WHEN date IS NOT NULL AND date_finish IS NOT NULL
                        THEN DATEDIFF(date_finish, date) + 1
                        ELSE 0
                     END as duration_days'),
            'created_at',
        ])
        ->where('product_category', 'KLTG');

    // ✅ NEW: enforce newest first with stable tiebreaker
    $base = $baseQuery
        ->latest('created_at')   // = orderBy('created_at','desc')
        ->orderByDesc('id')      // tiebreak when created_at equal
        ->get();

    $details = KltgMonthlyDetail::whereIn('master_file_id', $base->pluck('id'))
        ->where('year', $year)
        ->get()
        ->groupBy('master_file_id');

    $categories = ['KLTG', 'VIDEO', 'ARTICLE', 'LB', 'EM'];

    $rows = $base->map(function ($masterFile) use ($details, $categories, $year) {
        $masterFileDetails = $details->get($masterFile->id, collect());
        $publicationRecord = $masterFileDetails->firstWhere('category', 'PUBLICATION');
        $publication = $publicationRecord ? $publicationRecord->value_text : '';

        $grid = [];
        for ($month = 1; $month <= 12; $month++) {
            foreach ($categories as $category) {
                $key = sprintf('%02d_%s', $month, $category);

                $textRecord = $masterFileDetails->first(function ($detail) use ($month, $category) {
                    return (int)$detail->month === $month
                        && $detail->category === $category
                        && $detail->field_type === 'text';
                });
                $dateRecord = $masterFileDetails->first(function ($detail) use ($month, $category) {
                    return (int)$detail->month === $month
                        && $detail->category === $category
                        && $detail->field_type === 'date';
                });

                $grid[$key] = [
                    'text' => $textRecord?->value_text ?? '',
                    'date' => $dateRecord?->value_date ?? '',
                ];
            }
        }

        return [
            'id'          => $masterFile->id,
            'month_name'  => $masterFile->month_name ?? '',
            'created_at'  => optional($masterFile->created_at)->format('d/m/y'),
            'company'     => $masterFile->company,
            'product'     => $masterFile->product,
            'status'      => 'Pending',
            'start'       => $masterFile->start_date ? \Carbon\Carbon::parse($masterFile->start_date)->format('d/m') : null,
            'end'         => $masterFile->end_date ? \Carbon\Carbon::parse($masterFile->end_date)->format('d/m') : null,
            'duration'    => $masterFile->duration_days,
            'publication' => $publication,
            'grid'        => $grid,
        ];
    })->values(); // keep clean numeric keys in view

    $companies = MasterFile::whereNotNull('company')->distinct()->orderBy('company')->pluck('company');
    $products  = MasterFile::whereNotNull('product')->distinct()->orderBy('product')->pluck('product');
    $statuses  = collect(['Pending', 'Ongoing', 'Completed']);

    return view('dashboard.kltg', [
        'year'       => $year,
        'rows'       => $rows,
        'categories' => $categories,
        'companies'  => $companies,
        'products'   => $products,
        'statuses'   => $statuses,
        'selected'   => ['status' => '', 'company' => '', 'product' => ''],
    ]);
}

    public static function getCellValue($detailMap, $masterFileId, $month, $category, $fieldType)
    {
        $category = strtoupper($category);
        $data = $detailMap[$masterFileId][$month][$category][$fieldType] ?? null;

        if (!$data) {
            return '';
        }

        return $fieldType === 'date' ? ($data['date'] ?? '') : ($data['text'] ?? '');
    }
}
