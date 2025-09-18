@extends('layouts.app')

@section('title', 'Laporan Transaksi Belum Lunas')

@section('content')
<div class="container py-4">
    <h3 class="mb-4 text-center">📊 Laporan Transaksi Belum Lunas</h3>

    {{-- 🔍 Filter Tahun Akademik --}}
    <div class="d-flex justify-content-center mb-4">
        <form action="{{ route('report.unpaid.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-auto">
                <label for="year" class="col-form-label fw-bold">Tahun Akademik:</label>
            </div>
            <div class="col-auto">
                <select name="year" id="year" class="form-select">
                    @foreach($academicYears as $year)
                        <option value="{{ $year->year }}" {{ $selectedYear == $year->year ? 'selected' : '' }}>
                            {{ $year->year }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Tampilkan</button>
            </div>
        </form>
    </div>

    {{-- 📅 Laporan Bulanan --}}
    <h5 class="mt-4">Laporan Bulanan ({{ $selectedYear }})</h5>
    <table class="table table-bordered table-striped text-center align-middle">
        <thead class="table-dark">
            <tr>
                <th>Bulan</th>
                <th>Jumlah Siswa Belum Lunas</th>
                <th>Total Tagihan Belum Lunas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($monthlyReport[$selectedYear] as $month => $data)
                <tr>
                    <td>
                        <a href="{{ route('report.unpaid.details', ['year' => $selectedYear, 'month' => $month]) }}"
                           class="text-decoration-none fw-bold">
                            {{ \Carbon\Carbon::createFromDate(2000, (int)$month, 1)->translatedFormat('F') }}
                        </a>
                    </td>
                    <td>{{ $data['total_unpaid'] }}</td>
                    <td>Rp {{ number_format($data['total_due'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- 🎓 Laporan Semester --}}
    <h5 class="mt-4">Laporan Semester ({{ $selectedYear }})</h5>
    <table class="table table-bordered table-striped text-center align-middle">
        <thead class="table-dark">
            <tr>
                <th>Semester</th>
                <th>Jumlah Tagihan Belum Lunas</th>
                <th>Total Tagihan Belum Lunas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($semesterReport[$selectedYear] as $semester => $data)
                <tr>
                    <td>Semester {{ $semester }}</td>
                    <td>{{ $data['total_unpaid'] }}</td>
                    <td>Rp {{ number_format($data['total_due'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
