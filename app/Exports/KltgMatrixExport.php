<?php

namespace App\Exports;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    private function statusColor(?string $status): ?string
    {
        if (!$status) return null;
        $map = [
            // dropdown utama
            'Artwork'      => 'FFFFFF00', // yellow
            'Installation' => 'FFFF0000', // red
            'Dismantle'    => 'FF7F7F7F', // gray (spelling benar)
            'Dismantel'    => 'FF7F7F7F', // tolerate typo
            'Payment'      => 'FFFF0000', // red
            'Ongoing'      => 'FF00B0F0', // blue
            'Renewal'      => 'FFFF0000', // red
            'Completed'    => 'FF00B050', // green
            'Material'     => 'FFFFC000', // orange
            'Whatsapp'     => 'FF92D050', // light green
            'Posted'       => 'FF7F7F7F', // gray
            // umum
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
            $row += 0; // next row already set by writeRecord
        }

        // Autosize + freeze
        for ($i=1; $i<=$lastColIdx; $i++) $sheet->getColumnDimension($this->col($i))->setAutoSize(true);
        $sheet->freezePane('A3');

        return response()->streamDownload(function () use ($ss) {
            (new Xlsx($ss))->save('php://output');
        }, $filename, ['Content-Type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    /** ---------- MULTI YEAR (blok “YEAR – 2025”, dst.) ---------- */
    public function downloadByYear(array $byYear, string $filename): StreamedResponse
    {
        // $byYear = [ 2025 => [ ['summary'=>..., 'matrix'=>...], ... ], 2026 => [...], ... ]
        $ss    = new Spreadsheet();
        $sheet = $ss->getActiveSheet();
        $sheet->setTitle('Export');

        $months = null;
        // coba ambil dari year pertama yang ada data, else default
        foreach ($byYear as $records) {
            if (!empty($records)) { $months = array_column($records[0]['matrix'], 'monthName'); break; }
        }
        if (!$months) $months = $this->defaultMonths();

        $lastColIdx = 10 + count($months) * count($this->catLabels);
        $lastCol    = $this->col($lastColIdx);

        $row = 1;

        foreach ($byYear as $year => $records) {
            // Title row (kuning) merged full width
            $sheet->setCellValue("A{$row}", "YEAR - {$year}");
            $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
            $sheet->getStyle("A{$row}:{$lastCol}{$row}")
                ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF200');
            $sheet->getStyle("A{$row}:{$lastCol}{$row}")
                ->getFont()->setBold(true);
            $sheet->getStyle("A{$row}:{$lastCol}{$row}")
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
            $row += 1;

            // Header 2 baris untuk blok ini
            [$lastColIdx, $lastCol] = $this->writeHeader($sheet, $row, $months);
            $row += 2;

            // Data rows
            if (empty($records)) {
                // minimal 1 baris kosong biar bloknya kelihatan
                $sheet->getStyle("A{$row}:{$lastCol}{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $row += 2;
            } else {
                foreach ($records as $rec) {
                    $row = $this->writeRecord($sheet, $row, $rec, $lastColIdx);
                }
            }

            // pemisah kosong antar tahun
            $row += 1;
        }

        // Autosize
        for ($i=1; $i<=$lastColIdx; $i++) $sheet->getColumnDimension($this->col($i))->setAutoSize(true);

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
        $fixed = [
            $s['no'],
            $s['month'],
            $s['created_at'] ?? '',
            $s['company'] ?? '',
            $s['product'] ?? '',
            $s['publication'] ?? '',
            $s['edition'] ?? '',
            $s['status'] ?? '',
            $s['start'] ? Carbon::parse($s['start'])->format('m/d/Y') : '',
            $s['end']   ? Carbon::parse($s['end'])->format('m/d/Y')   : '',
        ];
        $colIdx = 1;
        foreach ($fixed as $val) {
            $sheet->setCellValue($this->col($colIdx).$row, $val);
            $colIdx++;
        }

        // kanan: 12 bulan × n kategori → baris status & baris tanggal
        foreach ($rec['matrix'] as $m) {
            foreach ($this->catKeys as $k) {
                $cell = $this->col($colIdx).$row;
                $val  = (string)($m['cats'][$k]['status'] ?? '');
                $sheet->setCellValue($cell, $val);
                if ($argb = $this->statusColor($val)) {
                    $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)
                          ->getStartColor()->setARGB($argb);
                }

                $dateStr = '';
                if (!empty($m['cats'][$k]['start'])) {
                    $dateStr = Carbon::parse($m['cats'][$k]['start'])->format('m/d/Y');
                }
                if (!empty($m['cats'][$k]['end'])) {
                    $dateStr = trim($dateStr.' – '.Carbon::parse($m['cats'][$k]['end'])->format('m/d/Y'));
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
