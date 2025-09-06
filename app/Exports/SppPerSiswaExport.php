<?php

namespace App\Exports;

use App\Models\AcademicYear;
use App\Models\MClass;
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

class SppPerSiswaExport implements FromArray, WithHeadings, WithTitle, WithStyles, WithCustomStartCell, ShouldAutoSize
{
    protected $studentId;

    public function __construct($studentId)
    {
        $this->studentId = $studentId;
    }

    protected function user()
    {
        return User::findOrFail($this->studentId);
    }

    public function title(): string
    {
        return 'SPP ' . $this->user()->name;
    }

    public function startCell(): string
    {
        return 'A1';
    }

    public function headings(): array
    {
        $academicYear = AcademicYear::findOrFail($this->user()->academic_year_id);
        $class = MClass::findOrFail($this->user()->class_id);

        $months = Transaction::where('student_id', $this->studentId)
            ->pluck('date')
            ->map(function ($date) {
                return Carbon::parse($date)->locale('id')->isoFormat('MMMM YYYY');
            });

        return [
            ['Nama Siswa', $this->user()->first_name . ' ' . $this->user()->last_name],
            ['Tahun Ajaran', $academicYear->year],
            ['Kelas', $class->name ?? '-'],
            [],
            $months->toArray()
        ];
    }

    public function array(): array
    {
        $transactions = Transaction::where('student_id', $this->studentId)
            ->orderBy('date')
            ->get();

        return [
            $transactions->map(function ($trx) {
                return $trx->status === 'OK' ? 'Lunas' : 'Belum Lunas';
            })->toArray()
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:B3')->getFont()->setBold(true);

        $lastColumn = 'L';

        $sheet->getStyle("A5:{$lastColumn}6")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        $sheet->getStyle('A5:' . $lastColumn . '5')->getFont()->setBold(true);

        return [];
    }
}
