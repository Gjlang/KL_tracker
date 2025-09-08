<?php

namespace App\Exports;

use App\Models\MasterFile;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class MasterFilesExport
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $q = MasterFile::query();

        // Date filtering
        if (!empty($this->filters['date_from']) && !empty($this->filters['date_to'])) {
            $dateField = $this->filters['date_field'] ?? 'created_at';
            $q->whereBetween($dateField, [
                $this->filters['date_from'],
                $this->filters['date_to']
            ]);
        }

        // Search/Contains filtering
        if (!empty($this->filters['search']) || !empty($this->filters['contains'])) {
            $search = $this->filters['search'] ?? $this->filters['contains'];
            $q->where(function (Builder $b) use ($search) {
                $b->where('company', 'like', "%{$search}%")
                  ->orWhere('client', 'like', "%{$search}%")
                  ->orWhere('product', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhere('month', 'like', "%{$search}%");
            });
        }

        // Status filtering
        if (!empty($this->filters['status'])) {
            if (is_array($this->filters['status'])) {
                $q->whereIn('status', $this->filters['status']);
            } else {
                $q->where('status', $this->filters['status']);
            }
        }

        // Month filtering
        if (!empty($this->filters['month'])) {
            $q->where('month', $this->filters['month']);
        }

        // Product category filtering
        if (!empty($this->filters['product_category'])) {
            $q->where('product_category', $this->filters['product_category']);
        }

        return $q->orderBy('created_at', 'desc');
    }

    public function headings(): array
{
    $date = now()->format('Y-m-d');
    return [
        ["MASTER PROPOSAL CONFIRMATION - {$date}"],  // Title row
        [
            'Date Created',
            'Company Name',
            'Client',
            'Person In Charge',
            'Email',
            'Contact Number',
            'Product',
            'Month',
            'Start Date',
            'End Date',
            'Duration',
            'Status',
            'Job',
            'Artwork',
            'Traffic',
            'Invoice Date',
            'Invoice Number',
        ],
    ];
}

    public function getData(): array
    {
        $query = $this->query();
        $data = [];

        // Add headers
        $data[] = $this->headings();

        // Add data rows
        foreach ($query->get() as $row) {
            $data[] = $this->mapRow($row);
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
{
    // Merge the first row across all columns
    $sheet->mergeCells('A1:Q1'); // adjust Q to match your last column

    // Style the title
    return [
        1 => [ // row 1
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => 'center'],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => 'FFFF00'], // Yellow
            ],
        ],
        2 => [ // headings row
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => 'DDDDDD'], // Light gray for column headers
            ],
        ],
    ];
}


public function title(): string
{
    return 'Master Files';
}


    public function mapRow($row): array
    {
        $formatDate = function ($date) {
            return $date ? Carbon::parse($date)->format('m/d/Y') : '';
        };

        // Derive Month if not stored
        $monthText = $row->month;
        if (!$monthText) {
            if ($row->date) {
                $monthText = Carbon::parse($row->date)->format('M');
            } elseif ($row->start_date) {
                $monthText = Carbon::parse($row->start_date)->format('M');
            }
        }

        return [
            $formatDate($row->created_at),
            $row->company,
            $row->client,
            $row->person_in_charge,   // ✅ new
            $row->email,              // ✅ new
            $row->contact_number,     // ✅ new
            $row->product,
            $monthText,
            $formatDate($row->start_date ?? $row->date),
            $formatDate($row->end_date),
            $row->duration,
            $row->status,
            $row->job_number ?? $row->job,
            $row->artwork,
            $row->traffic,
            $formatDate($row->invoice_date),
            $row->invoice_number,
        ];
    }
}
