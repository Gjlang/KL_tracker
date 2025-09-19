<?php
// app/Exports/OutdoorWhiteboardLedgerExport.php
namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class OutdoorWhiteboardLedgerExport implements FromArray, WithEvents, ShouldAutoSize
{
    private Collection $rows;
    private array $grid = [];
    private array $sectionStarts = []; // row index => product name (for styling)

    // Column order (exactly as requested)
    private array $headers = [
        'No.',
        'Created',
        'INV Number',
        'Purchase Order',
        'Product',
        'Company',
        'Location',
        'Installation',
        'Dismantle',
        'Supplier',
        'Storage',
    ];

    public function __construct(Collection $rows)
    {
        $this->rows = $rows;
        $this->buildGrid();
    }

    public function array(): array
    {
        return $this->grid;
    }

    private function buildGrid(): void
    {
        // Group by product
        $byProduct = $this->rows->groupBy(fn ($r) => (string)($r->product ?? '—'));

        $currentRow = 1;

        foreach ($byProduct as $product => $items) {
            // Section header (green bar)
            $this->grid[] = ["Product: {$product}"];
            $this->sectionStarts[$currentRow] = $product;
            $currentRow++;

            // Table header
            $this->grid[] = $this->headers;
            $currentRow++;

            // Rows
            $i = 1;
            foreach ($items as $r) {
                $supplier = $this->stackTextDate($r->supplier_text ?? null, $r->supplier_date ?? null);
                $storage  = $this->stackTextDate($r->storage_text ?? null,  $r->storage_date ?? null);

                $this->grid[] = [
                    $i,
                    $this->fmtDate($r->created ?? null),
                    $this->nullToBlank($r->inv_number ?? null),
                    $this->nullToBlank($r->po_text ?? null),
                    $this->nullToBlank($r->product ?? null),
                    $this->nullToBlank($r->company ?? null),
                    $this->nullToBlank($r->location ?? null),
                    $this->fmtDate($r->installation ?? null),
                    $this->fmtDate($r->dismantle ?? null),
                    $supplier,
                    $storage,
                ];
                $i++;
                $currentRow++;
            }

            // Blank spacer row between sections
            $this->grid[] = [""];
            $currentRow++;
        }
    }

    private function nullToBlank($v): string
    {
        return isset($v) && $v !== '0' ? (string)$v : '';
    }

    private function fmtDate($v): string
    {
        if (empty($v)) return '';
        try {
            return \Carbon\Carbon::parse($v)->format('m/d/Y'); // mm/dd/yyyy
        } catch (\Throwable) {
            return '';
        }
    }

    // "stack" note + date in one cell with a line break; Excel will wrap via AfterSheet
    private function stackTextDate(?string $text, $date): string
    {
        $t = trim((string)($text ?? ''));
        $d = $this->fmtDate($date);
        if ($t !== '' && $d !== '') return $t . PHP_EOL . $d;
        if ($t !== '') return $t;
        if ($d !== '') return $d;
        return '';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $highestRow = count($this->grid);
                $highestCol = count($this->headers); // 11 cols
                $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($highestCol);

                // Global base styles (paper/ink look + borders)
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

                // Wrap Supplier + Storage columns across the whole sheet
                foreach (['J', 'K'] as $col) {
                    $sheet->getStyle("{$col}1:{$col}{$highestRow}")
                          ->getAlignment()->setWrapText(true);
                }

                // Style each section
                $row = 1;
                while ($row <= $highestRow) {
                    if (isset($this->sectionStarts[$row])) {
                        // SECTION BAR
                        $sheet->mergeCells("A{$row}:{$lastColLetter}{$row}");
                        $sheet->setCellValue("A{$row}", "Product: ".$this->sectionStarts[$row]);
                        $sheet->getStyle("A{$row}:{$lastColLetter}{$row}")->applyFromArray([
                            'fill' => [
                                'fillType' => 'solid',
                                'startColor' => ['rgb' => '92D050'], // green like your mock
                            ],
                            'font' => [
                                'bold' => true,
                            ],
                        ]);
                        $row++;

                        // HEADERS
                        $sheet->getStyle("A{$row}:{$lastColLetter}{$row}")->applyFromArray([
                            'font' => ['bold' => true],
                            'fill' => [
                                'fillType' => 'solid',
                                'startColor' => ['rgb' => 'FFF2CC'], // light yellow header like your mock
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                    'color' => ['rgb' => 'EAEAEA'],
                                ],
                            ],
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                            ],
                        ]);

                        // DATA rows until next blank (we inserted a blank spacer after each section)
                        $startData = $row + 1;
                        $endData = $startData;
                        while ($endData <= $highestRow && trim((string)$sheet->getCell("A{$endData}")->getValue()) !== '') {
                            $endData++;
                        }
                        $endData--; // last data row

                        if ($endData >= $startData) {
                            // borders + vertical alignment
                            $sheet->getStyle("A{$startData}:{$lastColLetter}{$endData}")->applyFromArray([
                                'borders' => [
                                    'allBorders' => [
                                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                        'color' => ['rgb' => 'EAEAEA'],
                                    ],
                                ],
                                'alignment' => [
                                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                                ],
                            ]);

                            // Column widths (autosize is on, but set a few minimums)
                            $sheet->getColumnDimension('A')->setWidth(5);   // No.
                            $sheet->getColumnDimension('B')->setWidth(12);  // Created
                            $sheet->getColumnDimension('C')->setWidth(18);  // INV
                            $sheet->getColumnDimension('D')->setWidth(20);  // PO
                            $sheet->getColumnDimension('E')->setWidth(12);  // Product
                            $sheet->getColumnDimension('F')->setWidth(22);  // Company
                            $sheet->getColumnDimension('G')->setWidth(22);  // Location
                            $sheet->getColumnDimension('H')->setWidth(12);  // Installation
                            $sheet->getColumnDimension('I')->setWidth(12);  // Dismantle
                            $sheet->getColumnDimension('J')->setWidth(20);  // Supplier (wrap)
                            $sheet->getColumnDimension('K')->setWidth(20);  // Storage (wrap)
                        }

                        // move to spacer + next section
                        $row = $endData + 2;
                    } else {
                        $row++;
                    }
                }

                // Freeze first data header row per section isn’t trivial; instead freeze top-left
                $sheet->freezePane('A3');
            },
        ];
    }
}
