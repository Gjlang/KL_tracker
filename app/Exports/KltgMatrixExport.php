<?php

namespace App\Exports;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\StreamedResponse;


class KltgMatrixExport
{
    /** @var array<int,array{summary:array,matrix:array}> */
    private array $records;      // per master_file_id
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

    private function statusColor(?string $status): ?string
    {
        if (!$status) return null;
        $map = [
            // match your dropdown
            'Artwork'      => 'FFFFFF00', // yellow
            'Installation' => 'FFFF0000', // red
            'Dismantel'    => 'FF7F7F7F', // gray
            'Payment'      => 'FFFF0000', // red
            'Ongoing'      => 'FF00B0F0', // blue
            'Renewal'      => 'FFFF0000', // red
            'Completed'    => 'FF00B050', // green
            'Material'     => 'FFFFC000', // orange
            'Whatsapp'     => 'FF92D050', // light green
            'Posted'       => 'FF7F7F7F', // gray
            // common alternates
            'In Progress'  => 'FF00B0F0',
            'Hold'         => 'FFFFC000',
            'Cancelled'    => 'FF7F7F7F',
            'Active'       => 'FF00B0F0', // if you still ever store this
        ];
        foreach ($map as $k => $argb) {
            if (strcasecmp($k, $status) === 0) return $argb;
        }
        return null; // no color if unknown
    }


    public function download(string $filename): StreamedResponse
    {
        $ss    = new Spreadsheet();
        $sheet = $ss->getActiveSheet();
        $sheet->setTitle('Export');

        // ========== Header (2 baris) ==========
        $fixedHeaders = ['No','Month','Created At','Company','Product','Publication','Edition','Status','Start','End'];
        $colIdx = 1;
        foreach ($fixedHeaders as $h) {
            $c = $this->col($colIdx);
            $sheet->setCellValue("{$c}1", $h);
            $sheet->mergeCells("{$c}1:{$c}2");
            $colIdx++;
        }

        // Ambil bulan dari record pertama (semua record punya 12 bulan sama urut)
        $months = $this->records ? array_column($this->records[0]['matrix'], 'monthName') : [];

        foreach ($months as $monthName) {
            $startColIdx = $colIdx;
            foreach ($this->catLabels as $lab) {
                $c = $this->col($colIdx);
                $sheet->setCellValue("{$c}2", $lab);
                $colIdx++;
            }
            $c1 = $this->col($startColIdx);
            $c2 = $this->col($colIdx-1);
            $sheet->mergeCells("{$c1}1:{$c2}1");
            $sheet->setCellValue("{$c1}1", $monthName);
        }

        $lastColIdx = $colIdx - 1;
        $lastCol    = $this->col($lastColIdx);

        $sheet->getStyle("A1:{$lastCol}2")->applyFromArray([
            'font'=>['bold'=>true],
            'alignment'=>[
                'horizontal'=>Alignment::HORIZONTAL_CENTER,
                'vertical'=>Alignment::VERTICAL_CENTER
            ],
            'borders'=>['allBorders'=>['borderStyle'=>Border::BORDER_THIN]],
            'fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['argb'=>'FFE5E5E5']],
        ]);

        // ========== Rows (tiap record = 2 baris) ==========
        $row = 3;

        foreach ($this->records as $rec) {
            $s = $rec['summary'];
            // kiri (10 kolom)
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

            // kanan: 12 bulan × 5 kategori
            foreach ($rec['matrix'] as $m) {
                foreach ($this->catKeys as $k) {
                    // baris status (Text)
                    $cell = $this->col($colIdx).$row;
$val  = (string)($m['cats'][$k]['status'] ?? '');
$sheet->setCellValue($cell, $val);

// ★ add color
if ($argb = $this->statusColor($val)) {
    $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)
          ->getStartColor()->setARGB($argb);
}

                    // baris tanggal (Date) — gabung Start–End atau pilih salah satu
                    $dateStr = '';
                    if (!empty($m['cats'][$k]['start'])) {
                        $dateStr = Carbon::parse($m['cats'][$k]['start'])->format('m/d/Y');
                    }
                    // kalau mau gabung end: uncomment 3 baris di bawah
                    if (!empty($m['cats'][$k]['end'])) {
                        $dateStr = trim($dateStr.' – '.Carbon::parse($m['cats'][$k]['end'])->format('m/d/Y'));
                    }
                    $sheet->setCellValue($this->col($colIdx).($row+1), $dateStr);
                    $colIdx++;
                }
            }

            // style block
            $sheet->getStyle("A{$row}:{$lastCol}".($row+1))->applyFromArray([
                'borders'=>['allBorders'=>['borderStyle'=>Border::BORDER_THIN]],
            ]);
            $sheet->getStyle($this->col(11).$row.":{$lastCol}".($row+1))
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A{$row}:{$lastCol}".($row+1))
                ->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            $row += 2;
        }

        // Autosize
        for ($i=1; $i<=$lastColIdx; $i++) {
            $sheet->getColumnDimension($this->col($i))->setAutoSize(true);
        }

        $sheet->freezePane('A3');

        return response()->streamDownload(function () use ($ss) {
            (new Xlsx($ss))->save('php://output');
        }, $filename, [
            'Content-Type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
