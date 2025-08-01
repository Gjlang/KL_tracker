<?php

namespace App\Imports;

use App\Models\MasterFile;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Facades\Log;

use Throwable;

class MasterFileImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    public function model(array $row)
    {
        // Debug: Log the row data to see what's being imported
        Log::info('Importing row: ', $row);

        // Skip empty rows
        if (empty($row['month']) && empty($row['date']) && empty($row['company'])) {
            return null;
        }

        return new MasterFile([
            'month'          => $row['month'] ?? null,
            'date'           => $this->parseDate($row['date'] ?? null),
            'company'        => $row['company'] ?? null,
            'product'        => $row['product'] ?? null,
            'traffic'        => $row['traffic'] ?? null,
            'duration'       => $row['duration'] ?? null,
            'status'         => $row['status'] ?? null,
            'client'         => $row['client'] ?? null,
            'date_finish'    => $this->parseDate($row['date_finish'] ?? null),
            'job_number'     => $row['job_number'] ?? null,
            'artwork'        => $row['artwork'] ?? null,
            'invoice_date'   => $this->parseDate($row['invoice_date'] ?? null),
            'invoice_number' => $row['invoice_number'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'month' => 'required|string',
            'date' => 'required',
            'company' => 'required|string',
            'product' => 'required|string',
            'traffic' => 'required|string',
            'duration' => 'required|string',
            'status' => 'required|string',
            'client' => 'required|string',
        ];
    }

    public function onError(Throwable $e)
    {
        Log::error('Import error: ' . $e->getMessage());
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            Log::error('Import failure on row ' . $failure->row() . ': ' . implode(', ', $failure->errors()));
        }
    }

    private function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        // Handle Excel date serial numbers
        if (is_numeric($date)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d');
        }

        // Handle string dates
        try {
            return \Carbon\Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning('Could not parse date: ' . $date);
            return null;
        }
    }
}
