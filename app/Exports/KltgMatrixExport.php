<?php

namespace App\Exports;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;


class KltgMatrixExport
{
    /** @var array<int,array{summary:array,matrix:array}> */
    private array $records;      // single-year records
    private array $catLabels;    // ['KLTG','Video','Article','LB','EM']
    private array $catKeys;      // ['KLTG','VIDEO','ARTICLE','LB','EM']

    public function __construct(array $records, array $catLabels, array $catKeys)
    {
        $this->records   = $records;
        $this->catLabels = $catLabels;
        $this->catKeys   = $catKeys;
    }

    private function col(int $i): string {
        $s=''; while ($i>0){ $m=($i-1)%26; $s=chr(65+$m).$s; $i=intdiv($i-1,26);} return $s;
    }

    private function defaultMonths(): array
    {
        return ['January','February','March','April','May','June','July','August','September','October','November','December'];
    }

    /**
     * Fallback warna kalau DB tidak punya color (berdasar teks status).
     * Menghasilkan ARGB (contoh: 'FFFF0000')
     */
    private function statusColor(?string $status): ?string
    {
        if (!$status) return null;
        $map = [
            'Artwork'      => 'FFFFFF00', // yellow
            'Installation' => 'FFFF0000', // red
            'Dismantle'    => 'FF7F7F7F', // gray
            'Dismantel'    => 'FF7F7F7F', // tolerate typo
            'Payment'      => 'FFFF0000', // red
            'Ongoing'      => 'FF00B0F0', // blue
            'Renewal'      => 'FFFF0000', // red
            'Completed'    => 'FF00B050', // green
            'Material'     => 'FFFFC000', // orange
            'Whatsapp'     => 'FF92D050', // light green
            'Posted'       => 'FF7F7F7F', // gray
            'In Progress'  => 'FF00B0F0',
            'Hold'         => 'FFFFC000',
            'Cancelled'    => 'FF7F7F7F',
            'Active'       => 'FF00B0F0',
        ];
        foreach ($map as $k => $argb) {
            if (strcasecmp($k, $status) === 0) return $argb;
        }
        return null;
    }

    /** --- Helpers untuk konversi HEX DB -> ARGB & kontras font --- */
    private function hexToARGB(?string $hex): ?string
    {
        if (!$hex) return null;
        $h = ltrim(trim($hex), '#');
        if (strlen($h) === 6) return 'FF'.strtoupper($h); // tambah alpha
        if (strlen($h) === 8) return strtoupper($h);      // sudah ARGB
        return null;
    }

    private function contrastFontColor(string $hex): string
    {
        $h = ltrim($hex, '#');
        if (strlen($h) < 6) return Color::COLOR_BLACK;
        $r = hexdec(substr($h,0,2));
        $g = hexdec(substr($h,2,2));
        $b = hexdec(substr($h,4,2));
        $yiq = (($r*299)+($g*587)+($b*114))/1000;
        return $yiq >= 150 ? Color::COLOR_BLACK : Color::COLOR_WHITE;
    }

    /** Shade every other row (odd rows) with a super-light blue */
    private function shadeOddRows(Worksheet $sheet, int $firstDataRow, ?int $lastDataRow = null, int $firstCol = 1, ?int $lastCol = null): void {
        $lastDataRow = $lastDataRow ?: (int)$sheet->getHighestRow();
        $lastCol     = $lastCol ?: Coordinate::columnIndexFromString($sheet->getHighestColumn());

        // very light blue (ARGB)
        $argb = 'FFEFF7FF';
        for ($r = $firstDataRow; $r <= $lastDataRow; $r += 2) {
            $range = $this->col($firstCol) . $r . ':' . $this->col($lastCol) . $r;
            $sheet->getStyle($range)->getFill()
                  ->setFillType(Fill::FILL_SOLID)
                  ->getStartColor()->setARGB($argb);
        }
    }

    /** Draw thick borders around each month block (5 columns per month) */
    private function outlineMonths(
        Worksheet $sheet,
        int $firstMonthColIndex,  // e.g. K==11
        int $firstDataRow,        // first row of data (not header)
        ?int $lastDataRow = null,
        int $colsPerMonth = 5,
        int $months = 12
    ): void {
        $lastDataRow = $lastDataRow ?: (int)$sheet->getHighestRow();

        for ($m = 0; $m < $months; $m++) {
            $left  = $firstMonthColIndex + ($m * $colsPerMonth);
            $right = $left + $colsPerMonth - 1;

            // Thick left border for the month
            $sheet->getStyle($this->col($left)  . $firstDataRow . ':' . $this->col($left)  . $lastDataRow)
                  ->getBorders()->getLeft()->setBorderStyle(Border::BORDER_MEDIUM);

            // Thick right border for the month
            $sheet->getStyle($this->col($right) . $firstDataRow . ':' . $this->col($right) . $lastDataRow)
                  ->getBorders()->getRight()->setBorderStyle(Border::BORDER_MEDIUM);
        }
    }

    /** ---------- SINGLE YEAR (tetap seperti punyamu) ---------- */
    public function download(string $filename): StreamedResponse
    {
        $ss    = new Spreadsheet();
        $sheet = $ss->getActiveSheet();
        $sheet->setTitle('Export');

        // Header 2 baris
        [$lastColIdx, $lastCol] = $this->writeHeader($sheet, 1, $this->records ? array_column($this->records[0]['matrix'], 'monthName') : $this->defaultMonths());

        // Rows (tiap record = 2 baris: status & tanggal)
        $row = 3;
        foreach ($this->records as $rec) {
            $row = $this->writeRecord($sheet, $row, $rec, $lastColIdx);
            $row += 0;
        }

        // Apply styling
        $firstDataRow = 3;
        $firstCol = 1;
        $firstMonthColIndex = 11; // K = 11 (A-J are fixed columns)

        // 1) Shade odd rows with super-light blue (only A-J columns)
        $this->shadeOddRows($sheet, $firstDataRow, null, $firstCol, 10);

        // 2) Add thick borders around each month block
        $this->outlineMonths($sheet, $firstMonthColIndex, $firstDataRow, null, count($this->catLabels), 12);

        // 3) Make the month header rows have thick bottom border
        $headerTopRow = 1;
        $headerLabelRow = 2;
        $sheet->getStyle($this->col($firstMonthColIndex).$headerTopRow.':'.$this->col($lastColIdx).$headerLabelRow)
              ->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);

        // Autosize + freeze
        for ($i=1; $i<=$lastColIdx; $i++) $sheet->getColumnDimension($this->col($i))->setAutoSize(true);
        $sheet->freezePane('K3'); // Freeze columns A-J (No to End) and rows 1-2 (headers)

        return response()->streamDownload(function () use ($ss) {
            (new Xlsx($ss))->save('php://output');
        }, $filename, ['Content-Type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }




    /** ---------- MULTI YEAR (blok "YEAR – 2025", dst.) ---------- */
    public function downloadByYear(array $byYear, string $filename): StreamedResponse
    {
        $ss    = new Spreadsheet();
        $sheet = $ss->getActiveSheet();
        $sheet->setTitle('Export');

        $months = null;
        foreach ($byYear as $records) {
            if (!empty($records)) { $months = array_column($records[0]['matrix'], 'monthName'); break; }
        }
        if (!$months) $months = $this->defaultMonths();

        $lastColIdx = 10 + count($months) * count($this->catLabels);
        $lastCol    = $this->col($lastColIdx);

        $row = 1;

        foreach ($byYear as $year => $records) {
            // Title row
            $sheet->setCellValue("A{$row}", "YEAR - {$year}");
            $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
            $sheet->getStyle("A{$row}:{$lastCol}{$row}")
                ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF200');
            $sheet->getStyle("A{$row}:{$lastCol}{$row}")
                ->getFont()->setBold(true);
            $sheet->getStyle("A{$row}:{$lastCol}{$row}")
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
            $row += 1;

            // Header 2 baris
            $headerStartRow = $row;
            [$lastColIdx, $lastCol] = $this->writeHeader($sheet, $row, $months);
            $row += 2;

            $firstDataRow = $row;

            // Data rows
            if (empty($records)) {
                $sheet->getStyle("A{$row}:{$lastCol}{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $row += 2;
            } else {
                foreach ($records as $rec) {
                    $row = $this->writeRecord($sheet, $row, $rec, $lastColIdx);
                }
            }

            $lastDataRow = $row - 1;

            // Apply styling for this year's block
            $firstCol = 1;
            $firstMonthColIndex = 11; // K = 11

            // 1) Shade odd rows with super-light blue (only A-J columns)
            $this->shadeOddRows($sheet, $firstDataRow, $lastDataRow, $firstCol, 10);

            // 2) Add thick borders around each month block
            $this->outlineMonths($sheet, $firstMonthColIndex, $firstDataRow, $lastDataRow, count($this->catLabels), count($months));

            // 3) Make the month header rows have thick bottom border
            $headerTopRow = $headerStartRow;
            $headerLabelRow = $headerStartRow + 1;
            $sheet->getStyle($this->col($firstMonthColIndex).$headerTopRow.':'.$this->col($lastColIdx).$headerLabelRow)
                  ->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);

            $row += 1; // spacer antar tahun
        }

        for ($i=1; $i<=$lastColIdx; $i++) $sheet->getColumnDimension($this->col($i))->setAutoSize(true);

        // Freeze columns A-J so they stay visible when scrolling right
        $sheet->freezePane('K1');

        return response()->streamDownload(function () use ($ss) {
            (new Xlsx($ss))->save('php://output');
        }, $filename, ['Content-Type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    /** ---------- Helpers to draw header & record blocks ---------- */
    private function writeHeader(Worksheet $sheet, int $topRow, array $months): array
    {
        $fixedHeaders = ['No','Month','Created At','Company','Product','Publication','Edition','Status','Start','End'];

        $colIdx = 1;
        foreach ($fixedHeaders as $h) {
            $c = $this->col($colIdx);
            $sheet->setCellValue("{$c}{$topRow}", $h);
            $sheet->mergeCells("{$c}{$topRow}:{$c}".($topRow+1));
            $colIdx++;
        }

        foreach ($months as $monthName) {
            $startColIdx = $colIdx;
            foreach ($this->catLabels as $lab) {
                $c = $this->col($colIdx);
                $sheet->setCellValue("{$c}".($topRow+1), $lab);
                $colIdx++;
            }
            $c1 = $this->col($startColIdx);
            $c2 = $this->col($colIdx-1);
            $sheet->mergeCells("{$c1}{$topRow}:{$c2}{$topRow}");
            $sheet->setCellValue("{$c1}{$topRow}", $monthName);
        }

        $lastColIdx = $colIdx - 1;
        $lastCol    = $this->col($lastColIdx);

        $sheet->getStyle("A{$topRow}:{$lastCol}".($topRow+1))->applyFromArray([
            'font'=>['bold'=>true],
            'alignment'=>[
                'horizontal'=>Alignment::HORIZONTAL_CENTER,
                'vertical'=>Alignment::VERTICAL_CENTER
            ],
            'borders'=>['allBorders'=>['borderStyle'=>Border::BORDER_THIN]],
            'fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['argb'=>'FFE5E5E5']],
        ]);

        return [$lastColIdx, $lastCol];
    }

    private function writeRecord(Worksheet $sheet, int $startRow, array $rec, int $lastColIdx): int
    {
        $row = $startRow;

        $s = $rec['summary'];
        $colIdx = 1;

        // A: No
        $sheet->setCellValue($this->col($colIdx++).$row, $s['no']);

        // B: Month (teks)
        $sheet->setCellValue($this->col($colIdx++).$row, $s['month']);

        // C: Created At (Excel date)
        if (!empty($s['created_at'])) {
            $excelVal = ExcelDate::PHPToExcel(Carbon::parse($s['created_at'])->getTimestamp());
            $cell = $this->col($colIdx).$row;
            $sheet->setCellValue($cell, $excelVal);
            $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('d/m/yy');
        } else {
            $sheet->setCellValue($this->col($colIdx).$row, '');
        }
        $colIdx++;

        // D–H: Company, Product, Publication, Edition, Status (teks)
        $sheet->setCellValue($this->col($colIdx++).$row, $s['company'] ?? '');
        $sheet->setCellValue($this->col($colIdx++).$row, $s['product'] ?? '');
        $sheet->setCellValue($this->col($colIdx++).$row, $s['publication'] ?? '');
        $sheet->setCellValue($this->col($colIdx++).$row, $s['edition'] ?? '');
        $sheet->setCellValue($this->col($colIdx++).$row, $s['status'] ?? '');

        // I: Start (Excel date)
        if (!empty($s['start'])) {
            $excelVal = ExcelDate::PHPToExcel(Carbon::parse($s['start'])->getTimestamp());
            $cell = $this->col($colIdx).$row;
            $sheet->setCellValue($cell, $excelVal);
            $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('d/m/yy');
        } else {
            $sheet->setCellValue($this->col($colIdx).$row, '');
        }
        $colIdx++;

        // J: End (Excel date)
        if (!empty($s['end'])) {
            $excelVal = ExcelDate::PHPToExcel(Carbon::parse($s['end'])->getTimestamp());
            $cell = $this->col($colIdx).$row;
            $sheet->setCellValue($cell, $excelVal);
            $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('d/m/yy');
        } else {
            $sheet->setCellValue($this->col($colIdx).$row, '');
        }
        $colIdx++;


        // 12 bulan × n kategori → baris status (row) & baris tanggal (row+1)
        foreach ($rec['matrix'] as $m) {
            foreach ($this->catKeys as $k) {
                $cell = $this->col($colIdx).$row;

                $val    = (string)($m['cats'][$k]['status'] ?? '');
                $hex    = $m['cats'][$k]['color'] ?? null; // <-- warna dari DB (kolom color)
                $sheet->setCellValue($cell, $val);

                // 1) Prioritas: pakai HEX dari DB
                if ($hex) {
                    $argb = $this->hexToARGB($hex);
                    if ($argb) {
                        $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)
                              ->getStartColor()->setARGB($argb);
                        // font kontras agar kebaca saat di print
                        $sheet->getStyle($cell)->getFont()->getColor()
                              ->setARGB($this->contrastFontColor($hex));
                    }
                }
                // 2) Fallback: pakai peta status (kalau tidak ada color)
                elseif ($argb = $this->statusColor($val)) {
                    $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)
                          ->getStartColor()->setARGB($argb);
                    // pakai kontras kasar berdasar fallback rgb (drop 'FF' alpha)
                    $rgb = substr($argb, 2);
                    $sheet->getStyle($cell)->getFont()->getColor()
                          ->setARGB($this->contrastFontColor('#'.$rgb));
                }

                // Tanggal (di baris berikutnya)
                $dateStr = '';
                if (!empty($m['cats'][$k]['start'])) {
                    $dateStr = Carbon::parse($m['cats'][$k]['start'])->format('d/m/y');
                }
                if (!empty($m['cats'][$k]['end'])) {
                    $dateStr = trim($dateStr.' – '.Carbon::parse($m['cats'][$k]['end'])->format('d/m/y'));
                }
                $sheet->setCellValue($this->col($colIdx).($row+1), $dateStr);

                $colIdx++;
            }
        }

        // style block (borders & align)
        $sheet->getStyle("A{$row}:".$this->col($lastColIdx).($row+1))
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle($this->col(11).$row.":".$this->col($lastColIdx).($row+1))
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$row}:".$this->col($lastColIdx).($row+1))
            ->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        return $row + 2; // next start row
    }
}
