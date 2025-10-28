<?php

namespace App\Exports;

use App\Models\Billboard;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class BillboardExport implements FromCollection, WithHeadings, WithMapping, WithCustomStartCell, WithStyles
{
    protected $filters;
    protected $rowNumber = 0; // For "No" column
    protected $selectedIds; // For selected billboard IDs

    public function __construct(array $filters = [], $selectedIds = null)
    {
        $this->filters = $filters;
        $this->selectedIds = $selectedIds;
    }

    public function collection()
    {
        $query = Billboard::select(
            'billboards.site_number',
            'billboards.site_type',
            'billboards.type',
            'billboards.size',
            'billboards.lighting',
            'locations.name as location_name',
            DB::raw("CONCAT(
                    CASE 
                        WHEN states.name = 'Kuala Lumpur' THEN 'KL'
                        WHEN states.name = 'Selangor' THEN 'SEL'
                        WHEN states.name = 'Negeri Sembilan' THEN 'N9'
                        WHEN states.name = 'Melaka' THEN 'MLK'
                        WHEN states.name = 'Johor' THEN 'JHR'
                        WHEN states.name = 'Perak' THEN 'PRK'
                        WHEN states.name = 'Pahang' THEN 'PHG'
                        WHEN states.name = 'Terengganu' THEN 'TRG'
                        WHEN states.name = 'Kelantan' THEN 'KTN'
                        WHEN states.name = 'Perlis' THEN 'PLS'
                        WHEN states.name = 'Kedah' THEN 'KDH'
                        WHEN states.name = 'Penang' THEN 'PNG'
                        WHEN states.name = 'Sarawak' THEN 'SWK'
                        WHEN states.name = 'Sabah' THEN 'SBH'
                        WHEN states.name = 'Labuan' THEN 'LBN'
                        WHEN states.name = 'Putrajaya' THEN 'PJY'
                        ELSE states.name
                    END, ' - ', districts.name
                ) as area"),
            DB::raw("CONCAT(billboards.gps_latitude, ', ', billboards.gps_longitude) as gps_coordinates"),
            'billboards.traffic_volume',
        )
            ->leftJoin('locations', 'billboards.location_id', '=', 'locations.id')
            ->leftJoin('districts', 'locations.district_id', '=', 'districts.id')
            ->leftJoin('states', 'districts.state_id', '=', 'states.id');

        // ✅ Apply filters
        if (!empty($this->filters['status']) && $this->filters['status'] !== "all") {
            $query->where('billboards.status', $this->filters['status']);
        }
        if (!empty($this->filters['state']) && $this->filters['state'] !== "all") {
            $query->where('states.id', $this->filters['state']);
        }
        if (!empty($this->filters['district']) && $this->filters['district'] !== "all") {
            $query->where('districts.id', $this->filters['district']);
        }
        if (!empty($this->filters['type']) && $this->filters['type'] !== "all") {
            $query->where('billboards.type', $this->filters['type']);
        }
        if (!empty($this->filters['site_type']) && $this->filters['site_type'] !== "all") {
            $query->where('billboards.site_type', $this->filters['site_type']);
        }
        if (!empty($this->filters['size']) && $this->filters['size'] !== "all") {
            $query->where('billboards.size', $this->filters['size']);
        }

        logger()->info('Selected IDs for export: ', ['ids' => $this->selectedIds]);

        // ✅ If selected IDs exist, filter only those
        if (!empty($this->selectedIds)) {
            $ids = is_array($this->selectedIds) ? $this->selectedIds : explode(',', $this->selectedIds);
            $ids = array_map('intval', $ids); // <- important
            $query->whereIn('billboards.id', $ids);
        }

        // ✅ Sort by location name alphabetically
        $query->orderBy('billboards.site_type', 'ASC')->orderByRaw("area ASC");

        return $query->get();
    }

    // ✅ Add "No" column
    public function map($row): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber, // No
            $row->site_number,
            $row->site_type,
            $row->type,
            $row->size,
            $row->lighting,
            $row->location_name,
            $row->area,
            $row->gps_coordinates,
            $row->traffic_volume,
        ];
    }

    public function headings(): array
    {
        return [
            "No",
            "Site Number",
            "New/Existing",
            "Type",
            "Size",
            "Lighting",
            "Location",
            "Area",
            "GPS Coordinates",
            "Traffic Volume",
        ];
    }

    // ✅ Start headings at row 3 (row1 = title, row2 = update info)
    public function startCell(): string
    {
        return 'A3';
    }

    public function styles(Worksheet $sheet)
    {
        // ✅ Build dynamic title
        $titleBase = "Billboard Stock Inventory List";
        if (!empty($this->filters['site_type']) && $this->filters['site_type'] !== "all") {
            $titleBase = ucfirst($this->filters['site_type']) . " Stock Inventory List";
        } elseif (!empty($this->filters['type']) && $this->filters['type'] !== "all") {
            $titleBase = ucfirst($this->filters['type']) . " Stock Inventory List";
        }

        $title = $titleBase . " " . now()->format('d/m/Y');

        // ✅ Title row
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', $title);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // ✅ Update info row (row 2)
        $sheet->setCellValue('A2', 'UPDATE: ' . Carbon::now()->format('d/m/Y H:i'));
        $sheet->getStyle('A2')->getFont()->setItalic(true);

        // ✅ Header row (row 3)
        $sheet->getStyle('A3:J3')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A3:J3')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF4F81BD');
        $sheet->getStyle('A3:J3')->getAlignment()->setHorizontal('center');

        // ✅ Auto-size columns
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // ✅ Apply border to all cells with data
        // Get highest row (last data row)
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // Define border style
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'], // black
                ],
            ],
        ];

        // Apply border from A3 (header) to last cell with data
        $sheet->getStyle("A3:{$highestColumn}{$highestRow}")->applyFromArray($borderStyle);
    }
}
