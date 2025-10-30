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
    private array $recordsBySubProduct;
    private string $mainTitle;

    public function __construct(array $recordsBySubProduct, string $mainTitle = '')
    {
        $this->recordsBySubProduct = $recordsBySubProduct;
        $this->mainTitle = $mainTitle ?: ('Outdoor - Monthly - ' . Carbon::now()->format('d/m/y'));
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

    /** Normalize months map, accepting keys 1..12 or "01".."12" (and any mix). */
    private function normalizeMonths($months): array
    {
        $out = array_fill(1, 12, ['status' => '', 'date' => null]);
        if (!is_array($months)) return $out;

        foreach ($months as $k => $v) {
            // Accept "1","01",1 etc.
            $idx = (int) ltrim((string) $k, '0');
            if ($idx === 0) $idx = 0; // keep invalid as 0 to skip
            if ($idx >= 1 && $idx <= 12) {
                $out[$idx] = [
                    'status' => (string)($v['status'] ?? ''),
                    'date'   => $v['date'] ?? null,
                ];
            }
        }
        return $out;
    }

    /** Try to write a real Excel date; fallback to empty if invalid. */
    private function writeExcelDate(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, string $cell, $raw): void
    {
        if (empty($raw)) {
            $sheet->setCellValue($cell, '');
            return;
        }
        try {
            // Accept Carbon, DateTimeInterface, unix ts, or parseable string
            if ($raw instanceof \DateTimeInterface) {
                $dt = Carbon::instance(\Carbon\Carbon::parse($raw->format('c')));
            } elseif (is_numeric($raw) && (int)$raw > 10_000) {
                // unix timestamp (seconds)
                $dt = Carbon::createFromTimestamp((int)$raw);
            } else {
                $dt = Carbon::parse($raw);
            }
            $excelVal = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($dt->timestamp);
            $sheet->setCellValue($cell, $excelVal);
            $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('d/m/y');
        } catch (\Throwable $e) {
            // If really not parseable, write as blank (avoid text dates)
            $sheet->setCellValue($cell, '');
        }
    }

    /** Background color for status with tolerant labels */
    private function statusColor(?string $status): ?string
    {
        if (!$status) return null;

        // Normalize common variants
        $s = trim(mb_strtolower($status));
        $aliases = [
            'install' => 'installation',
            'installed' => 'installation',
            'installation' => 'installation',

            'dismantel' => 'dismantle',
            'dismantel' => 'dismantle',
            'dismantled' => 'dismantle',
            'dismantle' => 'dismantle',

            'art work' => 'artwork',
            'artwork' => 'artwork',

            'material' => 'material',

            'payment' => 'payment',

            'renewal' => 'renewal',

            'in progress' => 'ongoing',
            'on going' => 'ongoing',
            'ongoing' => 'ongoing',

            'complete' => 'completed',
            'completed' => 'completed',
            'done' => 'completed',
        ];
        $norm = $aliases[$s] ?? $s;

        $map = [
            'installation' => 'FF22255B', // dark blue
            'dismantle'    => 'FFD33831', // red
            'payment'      => 'FFD33831', // red
            'renewal'      => 'FFD33831', // red
            'completed'    => 'FF16A34A', // green
            'artwork'      => 'FFF97316', // orange
            'material'     => 'FFF97316', // orange
            'ongoing'      => 'FF4BBBED', // light blue
        ];

        return $map[$norm] ?? null;
    }

    /** Text color for status */
    private function statusTextColor(?string $status): string
    {
        if (!$status) return 'FF000000';
        $s = trim(mb_strtolower($status));
        $aliases = [
            'in progress' => 'ongoing',
            'on going' => 'ongoing',
        ];
        $norm = $aliases[$s] ?? $s;

        // Light backgrounds → dark text
        if (in_array($norm, ['ongoing'], true)) {
            return 'FF1C1E26';
        }
        // Otherwise white
        return 'FFFFFFFF';
    }

    public function download(string $filename = 'Outdoor_Matrix.xlsx'): StreamedResponse
    {
        $ss = new Spreadsheet();
        $sheet = $ss->getActiveSheet();
        $sheet->setTitle('Outdoor Monthly');

        $fixedHeaders = ['No','Date','Company','Product','Site','Category','Start','End'];
        $months = [
            'JANUARY','FEBRUARY','MARCH','APRIL','MAY','JUNE',
            'JULY','AUGUST','SEPTEMBER','OCTOBER','NOVEMBER','DECEMBER'
        ];

        $totalCols = count($fixedHeaders) + count($months);
        $lastCol = $this->col($totalCols);

        // ===== Main Header (Row 1) =====
        $printDateTime = Carbon::now('Asia/Kuala_Lumpur')->format('d/m/y H:i:s');
        $sheet->setCellValue('A1', "{$this->mainTitle} - Printed: {$printDateTime}");
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THICK]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFFF00']],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // ===== Column Headers (Rows 2-3) =====
        $colIdx = 1;
        foreach ($fixedHeaders as $h) {
            $c = $this->col($colIdx);
            $sheet->setCellValue("{$c}2", $h);
            $sheet->mergeCells("{$c}2:{$c}3");
            $colIdx++;
        }
        foreach ($months as $mName) {
            $c = $this->col($colIdx);
            $sheet->setCellValue("{$c}2", $mName);
            $sheet->setCellValue("{$c}3", "Status / Date");
            $colIdx++;
        }
        $lastColIdx = $colIdx - 1;
        $lastCol = $this->col($lastColIdx);

        $sheet->getStyle("A2:{$lastCol}3")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE5E5E5']],
        ]);

        // Pre-set number formats for fixed date columns (they’ll be merged cells)
        $dateCols = [
            2 /* Date  */,
            7 /* Start */,
            8 /* End   */,
        ];
        foreach ($dateCols as $i) {
            $c = $this->col($i);
            $sheet->getStyle("{$c}:{$c}")
    ->getNumberFormat()->setFormatCode('d/m/y');
        }

        // ===== Body =====
        $rowTop = 4;
        $overallNo = 1;

        foreach ($this->recordsBySubProduct as $subProduct => $records) {
            if (empty($records) || !is_array($records)) continue;

            // Sub-product separator row
            $sheet->setCellValue("A{$rowTop}", strtoupper((string)$subProduct));
            $sheet->mergeCells("A{$rowTop}:{$lastCol}{$rowTop}");
            $sheet->getStyle("A{$rowTop}:{$lastCol}{$rowTop}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2C3E50']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM]],
            ]);
            $sheet->getRowDimension($rowTop)->setRowHeight(25);
            $rowTop++;

            foreach (array_values($records) as $rec) {
                $rowStatus = $rowTop;
                $rowDate   = $rowTop + 1;

                // Left columns
                $date     = $this->get($rec, 'date');
                $company  = (string) $this->get($rec, 'company');
                $product  = (string) $this->get($rec, 'product');
                $site     = (string) $this->get($rec, 'site');
                $category = (string) ($this->get($rec, 'category') ?: 'Outdoor');
                $start    = $this->get($rec, 'start');
                $end      = $this->get($rec, 'end');

                $leftVals = [
                    $overallNo++, // No
                    $date,        // Date (excel date)
                    $company,     // Company
                    $product,     // Product
                    $site,        // Site
                    $category,    // Category
                    $start,       // Start (excel date)
                    $end,         // End   (excel date)
                ];

                $col = 1;
                foreach ($leftVals as $idx => $val) {
                    $topCell = $this->col($col) . $rowStatus;
                    $botCell = $this->col($col) . $rowDate;

                    // Merge vertically
                    $sheet->mergeCells("{$topCell}:{$botCell}");
                    $sheet->getStyle("{$topCell}:{$botCell}")
                        ->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("{$topCell}:{$botCell}")
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // Columns: 1=No (text), 2=Date(date), 3-6 text, 7=Start(date), 8=End(date)
                    if (in_array($col, [2, 7, 8], true)) {
                        // Real Excel date (not text)
                        $this->writeExcelDate($sheet, $topCell, $val);
                    } else {
                        // Text cells
                        $sheet->setCellValueExplicit(
                            $topCell,
                            (string)$val,
                            \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                        );
                    }

                    $col++;
                }

                // Month columns (status on top row, date on bottom row)
                $monthsData = $this->normalizeMonths($rec['months'] ?? []);
                for ($m = 1; $m <= 12; $m++, $col++) {
                    $status = (string)($monthsData[$m]['status'] ?? '');
                    $dateMd = $monthsData[$m]['date'] ?? null;

                    $cellStatus = $this->col($col) . $rowStatus;
                    $cellDate   = $this->col($col) . $rowDate;

                    // Status (text + background)
                    $sheet->setCellValueExplicit(
                        $cellStatus,
                        $status,
                        \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                    );

                    if ($argb = $this->statusColor($status)) {
                        $textColor = $this->statusTextColor($status);
                        $sheet->getStyle($cellStatus)->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $argb]],
                            'font' => ['color' => ['argb' => $textColor], 'bold' => true],
                        ]);
                    }
                    $sheet->getStyle($cellStatus)->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);

                    // Date row as real Excel date
                    $this->writeExcelDate($sheet, $cellDate, $dateMd);
                    $sheet->getStyle($cellDate)->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);
                }

                // Borders for the 2-row block
                $sheet->getStyle("A{$rowStatus}:{$lastCol}{$rowDate}")
                    ->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]]);

                $rowTop += 2;
            }

            // Spacing between sub-products
            $rowTop += 1;
        }

        // Column widths
        $widths = [6, 12, 28, 14, 28, 12, 12, 12];
        for ($i = 1; $i <= count($widths); $i++) {
            $sheet->getColumnDimension($this->col($i))->setWidth($widths[$i-1]);
        }
        for ($i = count($widths) + 1; $i <= $lastColIdx; $i++) {
            $sheet->getColumnDimension($this->col($i))->setWidth(14);
        }

        $sheet->freezePane('A4');
        $sheet->getStyle("A4:{$lastCol}{$rowTop}")->getAlignment()->setWrapText(true);

        return response()->streamDownload(function () use ($ss) {
            (new Xlsx($ss))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
