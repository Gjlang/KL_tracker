<?php

namespace App\Exports;

use App\Models\MasterFile;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class MasterFilesExport
{
    /** @var array<string,mixed> */
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $q = MasterFile::query();

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $q->where(function (Builder $b) use ($search) {
                $b->where('company', 'like', "%{$search}%")
                  ->orWhere('client', 'like', "%{$search}%")
                  ->orWhere('product', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhere('month', 'like', "%{$search}%");
            });
        }

        if (!empty($this->filters['status'])) {
            $q->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['month'])) {
            $q->where('month', $this->filters['month']);
        }

        if (!empty($this->filters['product_category'])) {
            $q->where('product_category', $this->filters['product_category']);
        }

        return $q->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'Date Created',
            'Company Name',
            'Client',
            'Product',
            'Month',
            'Start Date',
            'End Date',
            'Duration',
            'Status',
            'Traffic',
            'Job',
            'Artwork',
            'Invoice Date',
            'Invoice Number',
        ];
    }

    public function mapRow($row): array
    {
        $fmt = fn ($v) => $v ? Carbon::parse($v)->format('Y-m-d') : null;

        // Derive Month if not stored - fix untuk Laravel 12
        $monthText = $row->month;
        if (!$monthText) {
            if ($row->date) {
                $monthText = Carbon::parse($row->date)->format('M');
            } elseif ($row->start_date) {
                $monthText = Carbon::parse($row->start_date)->format('M');
            }
        }

        return [
            $row->created_at ? Carbon::parse($row->created_at)->format('Y-m-d') : null,
            $row->company,
            $row->client,
            $row->product,
            $monthText,
            $fmt($row->start_date ?? $row->date),
            $fmt($row->end_date),
            $row->duration,
            $row->status,
            $row->traffic,
            $row->job_number ?? $row->job,
            $row->artwork,
            $fmt($row->invoice_date),
            $row->invoice_number,
        ];
    }

    // Method untuk Laravel Excel v1.x
    public function getData()
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
}
