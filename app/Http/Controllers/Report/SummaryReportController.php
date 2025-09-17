<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Services\Report\SummaryReportService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class SummaryReportController extends Controller
{
    public function __construct(private SummaryReportService $service) {}

    public function index(Request $request)
    {
        $data = $this->service->get($request->only(['year', 'month', 'status']));
        return view('reports.summary', $data);
    }

    public function pdf(Request $request)
    {
        $data = $this->service->get($request->only(['year', 'month', 'status']));
        $pdf = Pdf::loadView('reports.summary-pdf', $data)
            ->setPaper('a4', 'portrait');
        $fname = 'Summary_' . $data['filters']['year'] . '_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($fname);
    }
}
