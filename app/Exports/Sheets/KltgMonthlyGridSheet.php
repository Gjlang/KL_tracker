<?php

namespace App\Exports\Sheets;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class KltgMonthlyGridSheet implements FromView
{
    public function __construct(private array $payload) {}

    public function view(): View
    {
        // resources/views/exports/kltg_monthly.blade.php
        return view('exports.kltg_monthly', $this->payload);
    }
}
