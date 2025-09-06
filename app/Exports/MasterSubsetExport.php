<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MasterSubsetExport
{
    /** @var array<int,array<string,mixed>> */
    private array $rows;     // array of associative rows (from ->get()->toArray() or map)
    /** @var string[] */
    private array $columns;  // column keys (order)
    private string $title;   // sheet/report title
    private string $filename;

    /**
     * @param array<int,array<string,mixed>> $rows
     * @param string[] $columns
     */
    public function __construct(array $rows, array $columns, string $title, ?string $filename = null)
    {
        $this->rows = $rows;
        $this->columns = $columns;
        $this->title = $title;
        $safe = preg_replace('/[^A-Za-z0-9_\-]+/', '_', $title) ?: 'export';
        $this->filename = ($filename ?: $safe.'_'.date('Ymd_His')).'.xlsx';
    }

    private function col(int $i): string { // 1->A, 27->AA
        $s=''; while ($i>0){ $m=($i-1)%26; $s=chr(65+$m).$s; $i=intdiv($i-1,26); } return $s;
    }

    private function headerLabel(string $key): string
    {
        // Make "kltg_material_cbp" -> "Kltg Material Cbp"
        $key = str_replace('_',' ', $key);
        return ucwords($key);
    }

    public function download(): StreamedResponse
    {
        $ss = new Spreadsheet();
        $ws = $ss->getActiveSheet();
        $ws->setTitle(substr($this->title, 0, 31));

        // Title row
        $title = $this->title . ' — Generated ' . date('M d, Y H:i');
        $ws->setCellValue('A1', $title);
        $ws->mergeCells('A1:'.$this->col(count($this->columns)).'1');
        $ws->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $ws->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Header row (row 2)
        foreach ($this->columns as $i => $key) {
            $col = $this->col($i+1);
            $ws->setCellValue($col.'2', $this->headerLabel($key));
        }
        $ws->getStyle('A2:'.$this->col(count($this->columns)).'2')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
            'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
        ]);

        // Data rows starting row 3
        $r = 3;
        foreach ($this->rows as $row) {
            foreach ($this->columns as $i => $key) {
                $col = $this->col($i+1);
                $ws->setCellValueExplicit($col.$r, $row[$key] ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            }
            $r++;
        }

        // Freeze header
        $ws->freezePane('A3');

        // Autosize columns
        foreach (range(1, count($this->columns)) as $i) {
            $ws->getColumnDimension($this->col($i))->setAutoSize(true);
        }

        // Output
        return new StreamedResponse(function() use ($ss) {
            $writer = new Xlsx($ss);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$this->filename.'"',
            'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
            'Pragma' => 'public',
        ]);
    }
}
