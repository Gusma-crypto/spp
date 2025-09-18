<?php

namespace App\Exports;

use App\Models\MClass;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class SppPerKelasExport implements FromArray, WithHeadings, WithTitle, WithStyles, WithCustomStartCell, ShouldAutoSize
{
    protected $classId;

    public function __construct($classId)
    {
        $this->classId = $classId;
    }

    public function title(): string
    {
        if ($this->classId === 'all') {
            return 'SPP Semua Kelas';
        }

        $class = MClass::find($this->classId);
        return $class ? 'SPP ' . $class->name : 'SPP (Kelas Tidak Ditemukan)';
    }

    public function startCell(): string
    {
        return 'A1';
    }

    public function headings(): array
    {
        $students = $this->getStudents();
        $studentIds = $students->pluck('id');

        $months = Transaction::whereIn('student_id', $studentIds)
            ->pluck('date')
            ->unique()
            ->sort()
            ->map(function ($date) {
                return Carbon::parse($date)->locale('id')->isoFormat('MMMM YYYY');
            });

        return [
            ['Kelas', $this->classId === 'all' ? 'Semua Kelas' : MClass::find($this->classId)->name],
            [],
            array_merge(['No', 'NISN', 'Nama Siswa', 'Kelas', 'Tahun Ajaran'], $months->toArray())
        ];
    }

    public function array(): array
    {
        $students = $this->getStudents();
        $studentIds = $students->pluck('id');

        $months = Transaction::whereIn('student_id', $studentIds)
            ->pluck('date')
            ->unique()
            ->sort()
            ->map(fn ($date) => Carbon::parse($date)->format('Y-m'))
            ->values();

        $data = [];

        foreach ($students as $index => $student) {
            $row = [
                $index + 1,
                $student->nisn,
                $student->first_name . ' ' . $student->last_name,
                $student->class_name,
                $student->year
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
        $studentCount = $this->getStudents()->count();
        $columnCount = 5 + $this->getMonthCount(); // ada tambahan kolom Tahun Ajaran
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

    private function getStudents()
    {
        $query = User::selectRaw('users.*, classes.name as class_name, academic_years.year')
            ->join('classes', 'classes.id', '=', 'users.class_id')
            ->join('academic_years', 'academic_years.id', '=', 'users.academic_year_id');

        if ($this->classId !== 'all') {
            $query->where('users.class_id', $this->classId);
        }

        return $query->get();
    }

    private function getMonthCount()
    {
        $studentIds = $this->getStudents()->pluck('id');

        return Transaction::whereIn('student_id', $studentIds)
            ->pluck('date')
            ->map(fn ($date) => Carbon::parse($date)->format('Y-m'))
            ->unique()
            ->count();
    }
}