<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Income;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Kepala Sekolah
        $kepalaSekolah = User::join('roles', 'users.role_id', '=', 'roles.id')
            ->where('roles.name', 'Kepala Sekolah')
            ->first();

        // Jumlah siswa aktif
        $siswa = User::join('roles', 'users.role_id', '=', 'roles.id')
            ->where('roles.name', 'Siswa')
            ->count();

        // Jumlah pengguna aktif (selain Siswa & Super Admin)
        $pengguna = User::join('roles', 'users.role_id', '=', 'roles.id')
            ->whereNotIn('roles.name', ['Siswa', 'Super Admin'])
            ->count();

        // Bulan ini
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        // Data siswa belum lunas bulan ini
        $unpaidTransactions = Transaction::where('year', $currentYear)
            ->whereMonth('date', $currentMonth)
            ->where('status', '!=', 'OK')
            ->with(['student', 'student.mclass'])
            ->get();

        // Data pemasukan bulan ini dari tabel income
        $incomeRecords = Income::whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonth)
            ->orderBy('date')
            ->get();

        // Labels & data untuk Chart.js
        $incomeLabels = $incomeRecords->map(fn($r) => Carbon::parse($r->date)->format('d'))->toArray();
        $incomeData = $incomeRecords->map(fn($r) => $r->total_income)->toArray();

        return view('home', compact(
            'kepalaSekolah',
            'siswa',
            'pengguna',
            'unpaidTransactions',
            'incomeLabels',
            'incomeData',
            'currentMonth',
            'currentYear'
        ));
    }
}
