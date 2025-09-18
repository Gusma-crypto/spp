<?php

namespace App\Http\Controllers; 

use App\Exports\ExpenseExport;
use App\Exports\IncomeExport;
use App\Exports\IncomeRkpExport;
use App\Exports\SppPerKelasExport;
use App\Exports\SppPerSiswaExport;
use App\Exports\SppPerTahunExport;
use App\Models\AcademicYear;
use App\Models\Expense;
use App\Models\Income;
use App\Models\MClass;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; // <--- ini yang benar
use Maatwebsite\Excel\Facades\Excel;


class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = User::selectRaw("users.*, academic_years.year, classes.name AS class_name")
                ->join('roles', 'users.role_id', '=', 'roles.id')
                ->leftJoin('academic_years', 'users.academic_year_id', '=', 'academic_years.id')
                ->leftJoin('classes', 'users.class_id', '=', 'classes.id')
                ->where('roles.name', '=', 'Siswa')
                ->get();

            foreach ($data as $key => $x) {
                $x['no'] = $key + 1;
            }

            $classes = MClass::all();
            $academicYears = AcademicYear::all();

            return view("pages.report.index", compact('data', 'classes', 'academicYears'));
        } catch (\Throwable $th) {
            return redirect()->route('report.index')->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function indexIncome()
    {
         try {
            $data = Income::select([
                    DB::raw('DATE(date) as tanggal'),
                    DB::raw('SUM(total_income) as total_income'),
                    DB::raw('COUNT(*) as jumlah_transaksi'),
                ])
                ->groupBy('tanggal')
                ->orderByDesc('tanggal')
                ->get();

            return view("pages.report.income", compact('data'));
        } catch (\Throwable $th) {
            return redirect()->route('report.income.index')
                ->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }
    /**
     * Display a listing of the resource.
     */

    public function detailIncome($date)
    {
         try {
            $date = Carbon::parse($date)->format('Y-m-d');
            $data = Income::whereDate('date', $date)
                ->orderBy('date', 'asc')
                ->get();

            foreach ($data as $key => $x) {
                $x['no'] = $key + 1;
            }

            return view("pages.report.income_detail", compact('data', 'date'));
        } catch (\Throwable $th) {
            return redirect()->route('report.income.index')
                ->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }
    /**
     * menampilkan grafik income.
     */  
    public function grafikIncome()
    {
        // Group pemasukan per tanggal
        $incomes = DB::table('incomes')
            ->select(DB::raw('date, SUM(total_income) as total'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $labels = $incomes->pluck('date');
        $totals = $incomes->pluck('total');

        return view('pages.report.grafik_income', compact('labels', 'totals'));
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function indexExpense()
    {
        try {
            $data = Expense::all();

            foreach ($data as $key => $x) {
                $x['no'] = $key + 1;
            }

            return view("pages.report.expense", compact('data'));
        } catch (\Throwable $th) {
            return redirect()->route('report.expense.index')->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }

    public function exportStudent(string $student) {
        try {
            $user = User::find($student);
            $filename = 'Laporan Transaksi SPP ' . $user->first_name . ' ' . $user->last_name . '.xlsx';

            return Excel::download(new SppPerSiswaExport($student), $filename);
        } catch (\Throwable $th) {
            return redirect()->route('report.index')->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }

    public function exportClass(Request $request)
    {
        try {
            $classId = $request->query('class_id');

            // === Cek Kelas ===
            if ($classId === 'all') {
                $className = 'Semua Kelas';
            } else {
                $class = MClass::find($classId);
                if (!$class) {
                    return redirect()->route('report.spp.index')->with('error', 'Kelas tidak ditemukan.');
                }
                $className = $class->name;
            }

            // === Nama File ===
            $filename = 'Laporan Transaksi SPP ' . $className . '.xlsx';

            // === Download ===
            return Excel::download(new SppPerKelasExport($classId), $filename);

        } catch (\Throwable $th) {
            return redirect()->route('report.spp.index')->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }


    public function exportYear($yearId)
    {
        try {
            if ($yearId === 'all') {
                $filename = "Laporan Transaksi SPP - Semua.xlsx";
                return Excel::download(new SppPerTahunExport(null), $filename);
            }

            $year = AcademicYear::find($yearId);
            if (!$year) {
                return redirect()->route('report.spp.index')
                    ->with('error', 'Tahun ajaran tidak ditemukan.');
            }

            // --- perbaikan disini ---
            $sanitizedYear = str_replace(['/', '\\'], '-', $year->year);
            $filename = "Laporan Transaksi SPP - {$sanitizedYear}.xlsx";

            return Excel::download(new SppPerTahunExport($yearId), $filename);

        } catch (\Throwable $th) {
            return redirect()->route('report.spp.index')
                ->with('error', 'Terjadi kesalahan saat export per tahun.');
        }
    }

    public function exportIncome(Request $request) {
        try {
            $from = $request->from;
            $to = $request->to;

            return Excel::download(new IncomeExport($from, $to), 'Laporan Pemasukan.xlsx');
        } catch (\Throwable $th) {
            return redirect()->route('report.income.index')->with('error', 'Terjadi kesalahan saat export data.');
        }
    }

    public function exportIncomePdf(Request $request) 
    {
         // Ambil data per tanggal dari tabel incomes
        $data = DB::table('incomes')
            ->selectRaw('DATE(`date`) as tanggal, SUM(total_income) as total_income, COUNT(*) as jumlah_transaksi')
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        // Hitung total semua pemasukan & transaksi
        $grandTotalIncome = $data->sum('total_income');
        $grandTotalTransaksi = $data->sum('jumlah_transaksi');

        // Ambil grafik (base64 dari canvas)
        $chartImage = $request->input('chart_image');

        // Load view PDF
        $pdf = Pdf::loadView('pages.report.income-pdf', [
            'data' => $data,
            'chartImage' => $chartImage,
            'grandTotalIncome' => $grandTotalIncome,
            'grandTotalTransaksi' => $grandTotalTransaksi,
        ])->setPaper('a4', 'portrait');

        // Download PDF
        return $pdf->download('laporan-pemasukan-' . now()->format('Y-m-d') . '.pdf');
    }
    /**
     * export laporan pemasukan excell
     */
    public function exportRkpExcel(Request $request)
    {
        $from = $request->input('from');
        $to   = $request->input('to');

        // ambil chart image (base64)
        $chartImage = $request->input('chart_image');
        $chartPath = null;

        if ($chartImage) {
            $chartData = explode(',', $chartImage)[1]; // hapus prefix data:image/png;base64,
            $chartPath = storage_path('app/temp/chart.png');

            // buat folder temp jika belum ada
            if (!file_exists(dirname($chartPath))) {
                mkdir(dirname($chartPath), 0755, true);
            }

            file_put_contents($chartPath, base64_decode($chartData));
        }

        // download excel
        $fileName = 'rekap_pemasukan_' . now()->format('Y-m-d') . '.xlsx';
        $export   = new IncomeRkpExport($from, $to, $chartPath);

        $response = Excel::download($export, $fileName);

        // hapus chart image setelah export
        if ($chartPath && file_exists($chartPath)) {
            unlink($chartPath);
        }

        return $response;
    }

    

    public function exportExpense(Request $request) {
        try {
            $from = $request->from;
            $to = $request->to;

            return Excel::download(new ExpenseExport($from, $to), 'Laporan Pengeluaran.xlsx');
        } catch (\Throwable $th) {
            return redirect()->route('report.expense.index')->with('error', 'Terjadi kesalahan saat export data.');
        }
    }

    public function laporanSPPDetail(Request $request, $studentId)
    {    
        $student = User::findOrFail($studentId);

        $semester = (int) $request->get('semester', 1); // default semester 1
        $year = (int) $request->get('year', now()->year);

        if ($semester === 1) {
            // Semester 1: Juli–Desember tahun ajaran
            $startDate = Carbon::create($year, 7, 1)->startOfDay();
            $endDate   = Carbon::create($year, 12, 31)->endOfDay();
        } else {
            // Semester 2: Januari–Juni tahun berikutnya
            $startDate = Carbon::create($year + 1, 1, 1)->startOfDay();
            $endDate   = Carbon::create($year + 1, 6, 30)->endOfDay();
        }

        // Ambil semua transaksi semester ini
        $transactions = Transaction::where('student_id', $student->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'asc')
            ->get();

        // Total tagihan semester ini (pakai price)
        $totalTagihan = $transactions->sum('price');

        // Total yang sudah dibayar (status OK)
        $totalDibayar = $transactions->where('status', 'OK')->sum('price');

        // Sisa belum dibayar
        $sisaBelumLunas = $totalTagihan - $totalDibayar;

        return view('pages.report.spp_detail', compact(
            'transactions', 'semester', 'year', 'student',
            'totalTagihan', 'totalDibayar', 'sisaBelumLunas'
        ));
    }
    /**
     *repor siswa belum lunas
     */
     // Laporan Belum Lunas
   public function unpaidReport(Request $request)
    {
        try {
            $academicYears = AcademicYear::all();

            // Ambil tahun sekarang, misal 2025
            $currentYear = date('Y');

            // Cari tahun akademik yg mencakup tahun sekarang
            $activeAcademicYear = AcademicYear::where('year', 'LIKE', "%$currentYear%")->first();

            // Kalau tidak ada, fallback ke tahun pertama di DB
            $selectedYear = $request->get('year', $activeAcademicYear->year ?? $academicYears->first()->year ?? null);

            $monthlyReport = [];
            $semesterReport = [];

            $selectedAcademicYear = AcademicYear::where('year', $selectedYear)->first();
            if (!$selectedAcademicYear) {
                return redirect()->route('report.index')->with('error', 'Tahun akademik tidak ditemukan.');
            }

            // Ambil transaksi BELUM LUNAS (gunakan kolom 'year' di transactions)
            $transactions = \App\Models\Transaction::select(
                    \DB::raw('MONTH(date) as month'),
                    \DB::raw('COUNT(DISTINCT student_id) as total_unpaid'),
                    \DB::raw('SUM(price) as total_due')
                )
                ->where('year', $selectedAcademicYear->year) // pakai kolom 'year'
                ->whereIn('status', ['Belum Lunas', 'Pending','Expired']) // pastikan sesuai status di DB
                ->whereHas('student', function ($q) {
                    $q->where('parent_name', '!=', 'Yatim Piatu'); // ✅ exclude yatim piatu
                })
                ->groupBy(\DB::raw('MONTH(date)'))
                ->get()
                ->keyBy('month');

            // Bulanan (Juli - Desember)
            for ($month = 7; $month <= 12; $month++) {
                $monthlyReport[$selectedYear][$month] = [
                    'total_unpaid' => $transactions[$month]->total_unpaid ?? 0,
                    'total_due' => $transactions[$month]->total_due ?? 0
                ];
            }

            // Bulanan (Januari - Juni)
            for ($month = 1; $month <= 6; $month++) {
                $monthlyReport[$selectedYear][$month] = [
                    'total_unpaid' => $transactions[$month]->total_unpaid ?? 0,
                    'total_due' => $transactions[$month]->total_due ?? 0
                ];
            }

            // Semester
            $semester1 = $transactions->filter(fn($t) => $t->month >= 7 && $t->month <= 12);
            $semester2 = $transactions->filter(fn($t) => $t->month >= 1 && $t->month <= 6);

            $semesterReport[$selectedYear][1] = [
                'total_unpaid' => $semester1->sum('total_unpaid'),
                'total_due' => $semester1->sum('total_due')
            ];

            $semesterReport[$selectedYear][2] = [
                'total_unpaid' => $semester2->sum('total_unpaid'),
                'total_due' => $semester2->sum('total_due')
            ];

            return view('pages.report.unpaid', compact(
                'academicYears', 'selectedYear', 'monthlyReport', 'semesterReport'
            ));

        } catch (\Throwable $th) {
            dd($th->getMessage(), $th->getTraceAsString()); // biar jelas error aslinya
        }
    }



    public function unpaidDetails(Request $request)
    {
        $year = $request->get('year');
        $month = (int) $request->get('month'); // pastikan integer

        // Ambil transaksi belum lunas beserta relasi student dan kelas
        $transactions = \App\Models\Transaction::where('year', $year)
            ->whereMonth('date', $month)
            ->whereIn('status', ['Belum Lunas', 'Pending', 'Expired'])
            ->whereHas('student', function ($q) {
                $q->where('parent_name', '!=', 'Yatim Piatu'); // ✅ exclude yatim piatu
            })
            ->with(['student', 'student.mclass'])
            ->get();

        return view('pages.report.unpaid_details', compact('transactions', 'year', 'month'));
    }
   
}