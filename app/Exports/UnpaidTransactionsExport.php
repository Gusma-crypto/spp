<?php

namespace App\Exports;

use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class UnpaidTransactionsExport implements FromArray, WithHeadings, WithTitle, WithStyles, WithCustomStartCell, ShouldAutoSize
{
    protected $year, $month, $kelasId;

    public function __construct($year, $month, $kelasId = null)
    {
        $this->year = $year;
        $this->month = $month;
        $this->kelasId = $kelasId;
    }

    public function title(): string
    {
        $bulan = Carbon::createFromDate($this->year, $this->month, 1)->locale('id')->isoFormat('MMMM YYYY');
        return 'Tunggakan ' . $bulan;
    }

    public function startCell(): string
    {
        return 'A1';
    }

    public function headings(): array
    {
        $bulan = Carbon::createFromDate($this->year, $this->month, 1)->locale('id')->isoFormat('MMMM YYYY');

        return [
            ['Daftar Siswa Belum Lunas'],
            ['Periode', $bulan],
            [],
            ['No', 'Nama Siswa', 'Kelas', 'Tagihan', 'Status']
        ];
    }

    public function array(): array
    {
        $query = Transaction::whereYear('date', $this->year)
            ->whereMonth('date', $this->month)
            ->where('status', '!=', 'OK')
            ->with(['student.mclass']);

        if ($this->kelasId) {
            $query->whereHas('student.mclass', function ($q) {
                $q->where('id', $this->kelasId);
            });
        }

        $transactions = $query->get();

        $data = [];
        foreach ($transactions as $index => $trx) {
            $data[] = [
                $index + 1,
                $trx->student?->first_name . ' ' . $trx->student?->last_name ?? '-',
                $trx->student?->mclass?->name ?? '-',
                $trx->price,
                $trx->status == 'OK' ? 'Lunas' : 'Belum Lunas',
            ];
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $count = Transaction::whereYear('date', $this->year)
            ->whereMonth('date', $this->month)
            ->where('status', '!=', 'OK')
            ->count();

        $columnCount = 5; // No, Nama Siswa, Kelas, Tagihan, Status
        $endColumn = Coordinate::stringFromColumnIndex($columnCount);

        // Judul bold
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->mergeCells("A1:{$endColumn}1");

        // Header tabel (baris 4)
        $sheet->getStyle("A4:{$endColumn}4")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);
        $sheet->getStyle("A4:{$endColumn}4")->getFont()->setBold(true);

        $startRow = 5;
        $endRow = $startRow + $count - 1;

        return [
            "A{$startRow}:{$endColumn}{$endRow}" => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }
}
