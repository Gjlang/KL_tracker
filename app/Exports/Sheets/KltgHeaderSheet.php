<?php

namespace App\Exports\Sheets;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class KltgHeaderSheet implements FromView
{
    public function __construct(private array $payload) {}

    public function view(): View
    {
        // resources/views/exports/kltg_header.blade.php
        return view('exports.kltg_header', $this->payload);
    }
}
