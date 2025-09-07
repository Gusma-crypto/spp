<?php

namespace App\Exports;

use App\Models\Income;
use Illuminate\Support\Facades\DB;
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
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Events\AfterSheet;

class IncomeRkpExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $from;
    protected $to;
    protected $total = 0;
    protected $chartPath;

    public function __construct($from, $to, $chartPath = null)
    {
        $this->from = $from;
        $this->to   = $to;
        $this->chartPath = $chartPath;
    }

    public function collection()
    {
        $data = Income::select(
                DB::raw('DATE(date) as tanggal'),
                DB::raw('SUM(total_income) as total_income'),
                DB::raw('COUNT(id) as jumlah_transaksi')
            )
            ->when($this->from && $this->to, function ($query) {
                $query->whereBetween('date', [$this->from, $this->to]);
            })
            ->groupBy(DB::raw('DATE(date)'))
            ->orderBy('tanggal')
            ->get();

        $this->total = $data->sum('total_income');

        return $data;
    }

    public function headings(): array
    {
        return ['Tanggal', 'Jumlah Transaksi', 'Total Pemasukan'];
    }

    public function map($row): array
    {
        return [
            \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y'),
            $row->jumlah_transaksi,
            'Rp ' . number_format($row->total_income, 0, ',', '.'),
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
                $sheet   = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // Header bold
                $sheet->getStyle("A1:C1")->applyFromArray([
                    'font' => ['bold' => true],
                ]);

                // Border semua data
                $sheet->getStyle("A1:C{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                // Total baris terakhir
                $totalRow = $lastRow + 1;
                $sheet->mergeCells("A{$totalRow}:B{$totalRow}");
                $sheet->setCellValue("A{$totalRow}", 'TOTAL KESELURUHAN');
                $sheet->setCellValue("C{$totalRow}", 'Rp ' . number_format($this->total, 0, ',', '.'));

                $sheet->getStyle("A{$totalRow}:C{$totalRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                    'fill' => [
                        'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFFE599'],
                    ],
                ]);

                // Tambahkan grafik jika ada
                if ($this->chartPath && file_exists($this->chartPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Grafik Pemasukan');
                    $drawing->setDescription('Grafik dari laporan pemasukan');
                    $drawing->setPath($this->chartPath);
                    $drawing->setHeight(250);
                    $drawing->setCoordinates('E2'); // posisi grafik
                    $drawing->setWorksheet($sheet);
                }
            },
        ];
    }
}
