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
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class SppPerTahunExport implements FromArray, WithHeadings, WithTitle, WithStyles, WithCustomStartCell, ShouldAutoSize
{
    protected $academicYearId;

    public function __construct($academicYearId)
    {
        $this->academicYearId = $academicYearId;
    }

    protected function academicYear()
    {
        return AcademicYear::findOrFail($this->academicYearId);
    }

    public function title(): string
    {
        return 'SPP ' . $this->academicYear()->year;
    }

    public function startCell(): string
    {
        return 'A1';
    }

    public function headings(): array
    {
        $student = User::where('academic_year_id', $this->academicYearId)->get();

        $studentIds = $student->pluck('id');

        $months = Transaction::whereIn('student_id', $studentIds)
            ->pluck('date')
            ->unique()
            ->sort()
            ->map(function ($date) {
                return Carbon::parse($date)->locale('id')->isoFormat('MMMM YYYY');
            });

        return [
            ['Tahun Ajaran', $this->academicYear()->year],
            [],
            array_merge(['No', 'NISN', 'Nama Siswa', 'Kelas'], $months->toArray())
        ];
    }

    public function array(): array
    {
        $students = User::selectRaw('users.*, classes.name AS class_name')
            ->join('classes', 'classes.id', '=', 'users.class_id')
            ->where('academic_year_id', $this->academicYearId)
            ->get();

        $studentIds = $students->pluck('id');

        $months = Transaction::whereIn('student_id', $studentIds)
            ->pluck('date')
            ->unique()
            ->sort()
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m');
            })
            ->values();

        $data = [];

        foreach ($students as $index => $student) {
            $row = [
                $index + 1,
                $student->nisn,
                $student->first_name . ' ' . $student->last_name,
                $student->class_name
            ];

            foreach ($months as $month) {
                $carbonMonth = Carbon::createFromFormat('Y-m', $month);

                $paid = Transaction::where('student_id', $student->id)
                    ->whereMonth('date', $carbonMonth->month)
                    ->whereYear('date', $carbonMonth->year)
                    ->where('status', 'OK')
                    ->exists();

                $row[] = $paid ? 'Lunas' : 'Belum Lunas';
            }

            $data[] = $row;
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $studentCount = User::where('academic_year_id', $this->academicYearId)->count();

        $columnCount = 4 + $this->getMonthCount();
        $endColumn = Coordinate::stringFromColumnIndex($columnCount);

        $sheet->getStyle('A1:B1')->getFont()->setBold(true);

        $sheet->getStyle("A3:{$endColumn}3")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        $sheet->getStyle("A3:{$endColumn}3")->getFont()->setBold(true);

        $startRow = 4;
        $endRow = $startRow + $studentCount - 1;

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

    private function getMonthCount()
    {
        $studentIds = User::where('academic_year_id', $this->academicYearId)->pluck('id');

        return Transaction::whereIn('student_id', $studentIds)
            ->pluck('date')
            ->map(fn ($date) => Carbon::parse($date)->format('Y-m'))
            ->unique()
            ->count();
    }
}
