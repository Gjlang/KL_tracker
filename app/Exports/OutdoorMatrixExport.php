<?php

namespace App\Exports;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OutdoorMatrixExport
{
    /**
     * Expected $records shape (per row):
     * [
     *   'no'       => 1,
     *   'date'     => '2025-09-18',
     *   'company'  => 'SAMSUNG',
     *   'product'  => 'BB',
     *   'category' => 'Outdoor',
     *   'start'    => '2025-09-18 00:00:00',
     *   'end'      => '2025-11-27 00:00:00',
     *   'months'   => [
     *      1 => ['status' => 'Install',   'date' => '2025-01-05'],
     *      2 => ['status' => 'Maintain',  'date' => '2025-02-10'],
     *      ...
     *      12 => ['status' => 'Dismantel','date' => '2025-12-20'],
     *   ]
     * ]
     */
    private array $records;

    public function __construct(array $records)
    {
        $this->records = $records;
    }

    /** 1->A, 2->B, ... */
    private function col(int $i): string
    {
        $s = '';
        while ($i > 0) {
            $m = ($i - 1) % 26;
            $s = chr(65 + $m) . $s;
            $i = intdiv($i - 1, 26);
        }
        return $s;
    }

    /** Background color for status */
    private function statusColor(?string $status): ?string
    {
        if (!$status) return null;
        $map = [
            'Install'    => 'FF00B0F0', // blue
            'Installation'=> 'FF00B0F0',
            'Maintain'   => 'FF92D050', // light green
            'Maintenance'=> 'FF92D050',
            'Booked'     => 'FFFFC000', // orange
            'On Hold'    => 'FFFFC000',
            'Done'       => 'FF00B050', // green
            'Completed'  => 'FF00B050',
            'Dismantel'  => 'FF7F7F7F', // gray
            'Dismantle'  => 'FF7F7F7F',
            'Cancelled'  => 'FF7F7F7F',
        ];
        foreach ($map as $k => $argb) {
            if (strcasecmp($k, $status) === 0) return $argb;
        }
        return null;
    }

    public function download(string $filename = 'Outdoor_Matrix.xlsx'): StreamedResponse
    {
        $ss    = new Spreadsheet();
        $sheet = $ss->getActiveSheet();
        $sheet->setTitle('Outdoor');

        // ===== Header (2 rows) =====
        // Left fixed columns (merged vertically later per record)
        $fixedHeaders = ['No','Date','Company','Product','Category','Start','End'];
        $colIdx = 1;
        foreach ($fixedHeaders as $h) {
            $c = $this->col($colIdx);
            $sheet->setCellValue("{$c}1", $h);
            $sheet->mergeCells("{$c}1:{$c}2"); // 2-row header
            $colIdx++;
        }

        // Month groups (each month occupies 1 column containing: row = status, row+1 = date)
        $months = [
            'JANUARY','FEBRUARY','MARCH','APRIL','MAY','JUNE',
            'JULY','AUGUST','SEPTEMBER','OCTOBER','NOVEMBER','DECEMBER'
        ];

        foreach ($months as $mName) {
            $c = $this->col($colIdx);
            // Top cell shows the month name, bottom (row 2) shows sub-labels
            $sheet->setCellValue("{$c}1", $mName);
            $sheet->setCellValue("{$c}2", "Status / Date"); // purely visual hint
            $colIdx++;
        }

        $lastColIdx = $colIdx - 1;
        $lastCol    = $this->col($lastColIdx);

        // Header styles
        $sheet->getStyle("A1:{$lastCol}2")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER
            ],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE5E5E5']],
        ]);

        // ===== Body: 2-row block per record =====
        $rowTop = 3;

        foreach ($this->records as $idx => $rec) {
            $s = $rec['summary'] ?? [];
            $rowStatus = $rowTop;       // row that holds status (dropdown in UI)
            $rowDate   = $rowTop + 1;   // row right below, holds date

            // ---- Left fixed columns (merge vertically across the two rows)
            $leftVals = [
                $idx + 1,
                !empty($s['date'])     ? Carbon::parse($s['date'])->format('m/d/Y') : '',
                $s['company']  ?? '',
                $s['product']  ?? '',
                $s['category'] ?? '',
                !empty($s['start']) ? Carbon::parse($s['start'])->format('m/d/Y') : '',
                !empty($s['end'])   ? Carbon::parse($s['end'])->format('m/d/Y')   : '',
            ];

            $col = 1;
            foreach ($leftVals as $val) {
                $c1 = $this->col($col) . $rowStatus;
                $c2 = $this->col($col) . $rowDate;
                // write once then merge down (so it looks like a single tall cell)
                $sheet->setCellValue($c1, $val);
                $sheet->mergeCells("{$c1}:{$c2}");
                $sheet->getStyle("{$c1}:{$c2}")
                    ->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $col++;
            }

            // ---- Month columns
            for ($m = 1; $m <= 12; $m++, $col++) {
                $status = (string)($rec['months'][$m]['status'] ?? '');
                $date   = $rec['months'][$m]['date'] ?? null;

                // (a) Status on the first row (rowStatus)
                $cellStatus = $this->col($col) . $rowStatus;
                $sheet->setCellValue($cellStatus, $status);

                if ($argb = $this->statusColor($status)) {
                    $sheet->getStyle($cellStatus)->applyFromArray([
                        'fill' => [
                            'fillType'   => Fill::FILL_SOLID,
                            'startColor' => ['argb' => $argb],
                            'endColor'   => ['argb' => $argb],
                        ],
                    ]);
                }

                $sheet->getStyle($cellStatus)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);


                // (b) Date right below (rowDate)
                $cellDate = $this->col($col) . $rowDate;
                if (!empty($date)) {
                    $sheet->setCellValue($cellDate, Carbon::parse($date)->format('m/d/Y'));
                } else {
                    $sheet->setCellValue($cellDate, '');
                }
                $sheet->getStyle($cellDate)->getAlignment()
                      ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            // Borders for the 2-row block
            $sheet->getStyle("A{$rowStatus}:{$lastCol}{$rowDate}")
                ->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);

            $rowTop += 2;
        }

        // Column widths (left side fixed widths; months autosize or fixed)
        $widths = [6, 12, 22, 12, 12, 12, 12]; // No, Date, Company, Product, Category, Start, End
        for ($i = 1; $i <= count($widths); $i++) {
            $sheet->getColumnDimension($this->col($i))->setWidth($widths[$i-1]);
        }
        for ($i = count($widths) + 1; $i <= $lastColIdx; $i++) {
            $sheet->getColumnDimension($this->col($i))->setWidth(13); // month columns
        }

        // Freeze header
        $sheet->freezePane('A3');

        return response()->streamDownload(function () use ($ss) {
            (new Xlsx($ss))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
