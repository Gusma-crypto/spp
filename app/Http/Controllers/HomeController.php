<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Income;
use Carbon\Carbon;
use App\Models\Mclass;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UnpaidTransactionsExport;
use Illuminate\Support\Facades\Auth;
use App\Models\AcademicYear;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // Kepala Sekolah
        $kepalaSekolah = User::whereHas('role', fn($q) => 
            $q->where('name', 'Kepala Sekolah')
        )->first();

        // Jumlah siswa aktif
        $siswa = User::whereHas('role', fn($q) => 
            $q->where('name', 'Siswa')
        )->count();

        // Jumlah pengguna aktif (selain Siswa & Super Admin)
        $pengguna = User::whereHas('role', fn($q) => 
            $q->whereNotIn('name', ['Siswa', 'Super Admin'])
        )->count();

        // Bulan & tahun sekarang
        $currentYear  = now()->year;
        $currentMonth = now()->month;

        // ==================== FILTER TRANSAKSI ====================
        $query = Transaction::with(['student.mclass'])
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonth);

        if ($user->role->name === 'Siswa') {
            $query->where('student_id', $user->id);
        } elseif ($request->filled('kelas_id')) {
            $query->whereHas('student', fn($q) => 
                $q->where('class_id', $request->kelas_id)
            );
        }

        // ✅ hanya ambil transaksi yang benar-benar "Belum Lunas" bulan ini
        $unpaidTransactions = (clone $query)
            ->where('status', 'Belum Lunas')
            ->get();
        // ==========================================================

        // Data pemasukan bulan ini dari tabel income
        $incomeRecords = Income::whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonth)
            ->orderBy('date')
            ->get();

        $incomeLabels = $incomeRecords->map(fn($r) => Carbon::parse($r->date)->format('d'))->toArray();
        $incomeData   = $incomeRecords->pluck('total_income')->toArray();

        $kelasList = Mclass::all();

        // ===== Grafik Lunas vs Belum Lunas per Tahun Ajaran =====
        $academicYear = AcademicYear::latest()->first();
        
        if (!$academicYear) {
            // solusi cepat: kasih default tahun berjalan
            $currentYear = now()->year;
            $trxQuery = Transaction::where('year', $currentYear);
        } else {
            $trxQuery = Transaction::where('year', $academicYear->year);
        }

        if ($user->role->name === 'Siswa') {
            $trxQuery->where('student_id', $user->id);
        }

        $lunasCount = (clone $trxQuery)->where('status', 'OK')->count();
        $belumCount = (clone $trxQuery)->where('status', 'Belum Lunas')->count();

        $paymentStatusLabels = ['Lunas', 'Belum Lunas'];
        $paymentStatusData   = [$lunasCount, $belumCount];
        // ========================================================
        
        // ===== Tambahan: Daftar Bulan Lunas / Belum Lunas + Total =====
        $unpaidMonths = collect();
        $paidMonths   = collect();
        $totalTagihan = $totalDibayar = $sisaTagihan = 0;

        if ($user->role->name === 'Siswa') {
            $allTransactions = Transaction::where('student_id', $user->id)
                ->where('year', $academicYear->year) // cocok dengan format "2025/2026"
                ->get();

            $allMonths = $allTransactions->groupBy(fn($trx) => 
                Carbon::parse($trx->date)->translatedFormat('F Y')
            );

            $unpaidMonths = $allMonths->filter(fn($group) => 
                $group->contains(fn($trx) => $trx->status !== 'OK')
            )->keys();

            $paidMonths = $allMonths->filter(fn($group) => 
                $group->every(fn($trx) => $trx->status === 'OK')
            )->keys();

            // Hitung total (pakai price, bukan amount)
            $totalTagihan = $allTransactions->sum('price');
            $totalDibayar = $allTransactions->where('status', 'OK')->sum('price');
            $sisaTagihan  = $totalTagihan - $totalDibayar;
        }
        // ====================================================================

        return view('home', compact(
            'kepalaSekolah',
            'siswa',
            'pengguna',
            'unpaidTransactions',
            'incomeLabels',
            'incomeData',
            'currentMonth',
            'currentYear',
            'kelasList',
            'paymentStatusLabels',
            'paymentStatusData',
            'unpaidMonths',
            'paidMonths',
            'totalTagihan',
            'totalDibayar',
            'sisaTagihan',
            'allTransactions',
            
        ));
    }





    public function unpaidExport(Request $request)
    {
        $year = Carbon::now()->year;
        $month = Carbon::now()->month;
        $kelasId = $request->kelas_id;

        return Excel::download(
            new UnpaidTransactionsExport($year, $month, $kelasId),
            'siswa-belum-lunas.xlsx'
        );
    }

    // public function index()
    // {
    //     // Kepala Sekolah
    //     $kepalaSekolah = User::join('roles', 'users.role_id', '=', 'roles.id')
    //         ->where('roles.name', 'Kepala Sekolah')
    //         ->first();

    //     // Jumlah siswa aktif
    //     $siswa = User::join('roles', 'users.role_id', '=', 'roles.id')
    //         ->where('roles.name', 'Siswa')
    //         ->count();

    //     // Jumlah pengguna aktif (selain Siswa & Super Admin)
    //     $pengguna = User::join('roles', 'users.role_id', '=', 'roles.id')
    //         ->whereNotIn('roles.name', ['Siswa', 'Super Admin'])
    //         ->count();

    //     // Bulan ini
    //     $currentYear = Carbon::now()->year;
    //     $currentMonth = Carbon::now()->month;

    //     // Data siswa belum lunas bulan ini
    //     $unpaidTransactions = Transaction::where('year', $currentYear)
    //         ->whereMonth('date', $currentMonth)
    //         ->where('status', '!=', 'OK')
    //         ->with(['student', 'student.mclass'])
    //         ->get();

    //     // Data pemasukan bulan ini dari tabel income
    //     $incomeRecords = Income::whereYear('date', $currentYear)
    //         ->whereMonth('date', $currentMonth)
    //         ->orderBy('date')
    //         ->get();

    //     // Labels & data untuk Chart.js
    //     $incomeLabels = $incomeRecords->map(fn($r) => Carbon::parse($r->date)->format('d'))->toArray();
    //     $incomeData = $incomeRecords->map(fn($r) => $r->total_income)->toArray();

    //     return view('home', compact(
    //         'kepalaSekolah',
    //         'siswa',
    //         'pengguna',
    //         'unpaidTransactions',
    //         'incomeLabels',
    //         'incomeData',
    //         'currentMonth',
    //         'currentYear'
    //     ));
    // }
}