<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\KltgHeaderSheet;
use App\Exports\Sheets\KltgMonthlyGridSheet;

class KltgMonthlyExport implements WithMultipleSheets
{
    public function __construct(private array $payload) {}

    public function sheets(): array
    {
        return [
            new KltgHeaderSheet($this->payload),
            new KltgMonthlyGridSheet($this->payload),
        ];
    }
}
