<?php

namespace App\Exports;

use Maatwebsite\Excel\Facades\Excel;

class KltgMatrixExport
{
    protected array $months = [
        'January','February','March','April','May','June',
        'July','August','September','October','November','December',
    ];
    protected array $cats   = ['KLTG','Video','Article','LB','EM'];

    public function __construct(protected array $grid) {}

    /** PUBLIC: build the data array (so controller can call it if needed). */
    public function buildData(): array
    {
        $rows = [];
        // Header row
        $rows[] = array_merge(['Month'], $this->cats);

        // 2 rows per month: status row + dates row
        foreach ($this->months as $m) {
            $statusRow = [$m];
            $dateRow   = [''];

            foreach ($this->cats as $cat) {
                $cell = $this->grid[$m][$cat] ?? ['status'=>null,'start'=>null,'end'=>null];
                $statusRow[] = $cell['status'] ?: '';

                $start = !empty($cell['start']) ? date('m/d/Y', strtotime($cell['start'])) : '';
                $end   = !empty($cell['end'])   ? date('m/d/Y', strtotime($cell['end']))   : '';
                $dateRow[] = trim($start . ($start && $end ? ' – ' : '') . $end);
            }

            $rows[] = $statusRow;
            $rows[] = $dateRow;
        }

        return $rows;
    }

    /** PUBLIC: apply styling on the sheet given the $data matrix. */
    public function applySheetStyles($sheet, array $data): void
    {
        // header styles
        $sheet->row(1, function ($row) {
            $row->setFontWeight('bold');
            $row->setAlignment('center');
            $row->setBackground('#e5e7eb');
        });

        $lastRow = count($data);
        $sheet->setBorder("A1:F{$lastRow}", 'thin');
        $sheet->setAutoSize(true);

        // status rows = 2,4,6,...
        for ($r = 2; $r <= $lastRow; $r += 2) {
            $sheet->row($r, function ($row) {
                $row->setAlignment('center');
                $row->setFontWeight('bold');
                $row->setFontSize(11);
            });
        }

        // date rows = 3,5,7,...
        for ($r = 3; $r <= $lastRow; $r += 2) {
            $sheet->row($r, function ($row) {
                $row->setAlignment('center');
                $row->setFontSize(10);
                $row->setBackground('#f8f8f8');
            });
        }

        // color fills for status cells
        $this->colorStatusCells($sheet, $data);
    }

    /** PRIVATE: color the status cells based on the text. */
    private function colorStatusCells($sheet, array $data): void
    {
        $colorMap = [
            'Artwork'      => '#FFEB84', // yellow
            'Installation' => '#FF8080', // red
            'Renewal'      => '#FF8080', // red
            'Completed'    => '#92D050', // green
            'In Progress'  => '#9BC2E6', // blue
            'Hold'         => '#FFC000', // orange
            'Cancelled'    => '#BFBFBF', // gray
        ];

        $totalRows = count($data);

        // Status rows are 2,4,6,...; columns B..F = 2..6
        for ($r = 2; $r <= $totalRows; $r += 2) {
            for ($c = 2; $c <= 6; $c++) {
                $val = $data[$r - 1][$c - 1] ?? '';
                if ($val === '') continue;

                foreach ($colorMap as $label => $hex) {
                    if (strncmp($val, $label, strlen($label)) === 0) {
                        $ref = $this->colLetter($c) . $r;
                        $sheet->cell($ref, function ($cell) use ($hex) {
                            $cell->setBackground($hex);
                        });
                        break;
                    }
                }
            }
        }
    }

    /** PRIVATE: number -> Excel col letter (A..Z). */
    private function colLetter(int $n): string
    {
        $letters = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        return $letters[$n - 1] ?? 'A';
    }

    /** PUBLIC: one-liner to produce and download the XLSX (v2 API). */
    public function download()
    {
        $data = $this->buildData();
        $file = 'export_matrix_masterfile_' . date('Ymd');

        return Excel::create($file, function ($excel) use ($data) {
            $excel->sheet('Matrix', function ($sheet) use ($data) {
                $sheet->fromArray($data, null, 'A1', false, false);
                $this->applySheetStyles($sheet, $data);
            });
        })->download('xlsx');
    }
}
