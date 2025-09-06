<?php

namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles,
    WithEvents
};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Events\AfterSheet;

class ExpenseExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $from;
    protected $to;
    protected $total = 0;

    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to   = $to;
    }

    public function collection()
    {
        $data = Expense::select('date', 'total_expense', 'note')
            ->when($this->from && $this->to, function ($query) {
                $query->whereBetween('date', [$this->from, $this->to]);
            })
            ->orderBy('date')
            ->get();

        $this->total = $data->sum(function ($item) {
            return $this->parseRupiah($item->total_expense);
        });

        return $data;
    }

    public function headings(): array
    {
        return ['Tanggal', 'Total Pengeluaran', 'Keterangan'];
    }

    public function map($row): array
    {
        return [
            \Carbon\Carbon::parse($row->date)->format('d-m-Y'),
            $row->total_expense,
            $row->note,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];

        $sheet->getStyle('A1:C' . $lastRow)->applyFromArray($styleArray);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                $sheet->getStyle("A1:C1")->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);

                $sheet->getStyle("A1:C{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                $totalRow = $lastRow + 1;
                $sheet->mergeCells("A{$totalRow}:B{$totalRow}");
                $sheet->setCellValue("A{$totalRow}", 'TOTAL PENGELUARAN');
                $sheet->setCellValue("C{$totalRow}", 'Rp ' . number_format($this->total, 0, ',', '.'));

                $sheet->getStyle("A{$totalRow}:C{$totalRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFFE599'], // Light yellow
                    ],
                ]);
            },
        ];
    }

    private function parseRupiah(string $rupiah): int
    {
        return (int) str_replace(['Rp', '.', ' '], '', $rupiah);
    }
}
