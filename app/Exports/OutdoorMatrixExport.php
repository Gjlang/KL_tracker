<?php

namespace App\Exports;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OutdoorMatrixExport
{
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

    /** Safe getter that checks both flat and summary shapes */
    private function get(array $rec, string $key, $default = '')
    {
        if (array_key_exists($key, $rec)) return $rec[$key];
        $sum = $rec['summary'] ?? [];
        return $sum[$key] ?? $default;
    }

    /** Parse to Excel date (Carbon) or return empty string */
    private function fmtDate($val): string
    {
        if (empty($val)) return '';
        try {
            return Carbon::parse($val)->format('Y-m-d');
        } catch (\Throwable $e) {
            return '';
        }
    }

    /** Background color for status */
    private function statusColor(?string $status): ?string
    {
        if (!$status) return null;
        $map = [
            'Install'      => 'FF00B0F0', // blue
            'Installation' => 'FF00B0F0',
            'Maintain'     => 'FF92D050', // light green
            'Maintenance'  => 'FF92D050',
            'Booked'       => 'FFFFC000', // orange
            'On Hold'      => 'FFFFC000',
            'Done'         => 'FF00B050', // green
            'Completed'    => 'FF00B050',
            'Dismantel'    => 'FF7F7F7F', // gray
            'Dismantle'    => 'FF7F7F7F',
            'Cancelled'    => 'FF7F7F7F',
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

        // ===== NEW: Main Header Row (Row 1) =====
        $fixedHeaders = ['No','Date','Company','Product','Site','Category','Start','End'];
        $months = [
            'JANUARY','FEBRUARY','MARCH','APRIL','MAY','JUNE',
            'JULY','AUGUST','SEPTEMBER','OCTOBER','NOVEMBER','DECEMBER'
        ];

        $totalCols = count($fixedHeaders) + count($months);
        $lastCol = $this->col($totalCols);

        // Main title spanning all columns with today's date
        $todayDate = Carbon::now()->format('d/m/Y');
        $sheet->setCellValue('A1', "Outdoor - Monthly - {$todayDate}");
        $sheet->mergeCells("A1:{$lastCol}1");

        // Style the main header with yellow background
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER
            ],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THICK]],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFFFF00'] // Yellow
            ],
        ]);

        // ===== Sub Headers (Rows 2-3) =====
        $colIdx = 1;
        foreach ($fixedHeaders as $h) {
            $c = $this->col($colIdx);
            $sheet->setCellValue("{$c}2", $h);
            $sheet->mergeCells("{$c}2:{$c}3"); // 2-row sub header
            $colIdx++;
        }

        // Month groups (each month is one column with two rows per record: Status (top) / Date (below))
        foreach ($months as $mName) {
            $c = $this->col($colIdx);
            $sheet->setCellValue("{$c}2", $mName);
            $sheet->setCellValue("{$c}3", "Status / Date"); // visual hint
            $colIdx++;
        }

        $lastColIdx = $colIdx - 1;
        $lastCol    = $this->col($lastColIdx);

        // Sub header styles
        $sheet->getStyle("A2:{$lastCol}3")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER
            ],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE5E5E5']],
        ]);

        // ===== Body: 2-row block per record (per SITE row) =====
        $rowTop = 4; // Start from row 4 now

        foreach (array_values($this->records) as $idx => $rec) {
            $rowStatus = $rowTop;       // top line (status)
            $rowDate   = $rowTop + 1;   // bottom line (date)

            // ---- Pull values (works for flat or summary)
            $date     = $this->fmtDate($this->get($rec, 'date'));
            $company  = (string) $this->get($rec, 'company');
            $product  = (string) $this->get($rec, 'product');
            $site     = (string) $this->get($rec, 'site');
            $category = (string) ($this->get($rec, 'category') ?: 'Outdoor');
            $start    = $this->fmtDate($this->get($rec, 'start'));
            $end      = $this->fmtDate($this->get($rec, 'end'));

            // ---- Left fixed columns (merge vertically across the two rows)
            $leftVals = [
                $idx + 1,   // No
                $date,      // Date
                $company,
                $product,
                $site,
                $category,
                $start,     // Start
                $end,       // End
            ];

            $col = 1;
            foreach ($leftVals as $val) {
                $c1 = $this->col($col) . $rowStatus;
                $c2 = $this->col($col) . $rowDate;

                // write once then merge down (visual single tall cell)
                $sheet->setCellValueExplicit($c1, (string)$val, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                // Convert date-looking strings in Date/Start/End to real date format
                if (in_array($col, [2, 7, 8], true) && !empty($val)) {
                    try {
                        $d = Carbon::parse($val);
                        $sheet->setCellValue($c1, \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($d->timestamp));
                        $sheet->getStyle($c1)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD);
                    } catch (\Throwable $e) {
                        // leave as string if parse fails
                    }
                }

                $sheet->mergeCells("{$c1}:{$c2}");
                $sheet->getStyle("{$c1}:{$c2}")
                    ->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("{$c1}:{$c2}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $col++;
            }

            // ---- Month columns
            $monthsData = $rec['months'] ?? [];
            for ($m = 1; $m <= 12; $m++, $col++) {
                $status = (string)($monthsData[$m]['status'] ?? '');
                $dateMd = $monthsData[$m]['date']   ?? null;

                // (a) Status (rowStatus)
                $cellStatus = $this->col($col) . $rowStatus;
                $sheet->setCellValueExplicit($cellStatus, $status, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

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
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                // (b) Date (rowDate)
                $cellDate = $this->col($col) . $rowDate;
                if (!empty($dateMd)) {
                    try {
                        $d = Carbon::parse($dateMd);
                        $sheet->setCellValue($cellDate, \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($d->timestamp));
                        $sheet->getStyle($cellDate)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD);
                    } catch (\Throwable $e) {
                        $sheet->setCellValueExplicit($cellDate, (string)$dateMd, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    }
                } else {
                    $sheet->setCellValue($cellDate, '');
                }

                $sheet->getStyle($cellDate)->getAlignment()
                      ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                      ->setVertical(Alignment::VERTICAL_CENTER);
            }

            // Borders for the 2-row block
            $sheet->getStyle("A{$rowStatus}:{$lastCol}{$rowDate}")
                ->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);

            $rowTop += 2;
        }

        // Column widths (No, Date, Company, Product, Site, Category, Start, End)
        $widths = [6, 12, 28, 14, 28, 12, 12, 12];
        for ($i = 1; $i <= count($widths); $i++) {
            $sheet->getColumnDimension($this->col($i))->setWidth($widths[$i-1]);
        }
        for ($i = count($widths) + 1; $i <= $lastColIdx; $i++) {
            $sheet->getColumnDimension($this->col($i))->setWidth(14); // months
        }

        // Freeze header (updated to row 4)
        $sheet->freezePane('A4');

        // Improve overall alignment
        $sheet->getStyle("A4:{$lastCol}{$rowTop}")->getAlignment()->setWrapText(true);

        // Set row height for the main header
        $sheet->getRowDimension(1)->setRowHeight(25);

        return response()->streamDownload(function () use ($ss) {
            (new Xlsx($ss))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
