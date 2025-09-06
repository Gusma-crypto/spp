@extends('layouts.app')

@section('title', 'Laporan Transaksi Belum Lunas')

@section('content')
<div class="container" style="padding: 20px;">
    <h1 class="mb-4 text-center">Laporan Transaksi Belum Lunas</h1>

    {{-- Form filter tahun akademik di tengah --}}
    <div class="d-flex justify-content-center mb-4">
        <form action="{{ route('report.unpaid.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-auto">
                <label for="year" class="col-form-label">Tahun Akademik:</label>
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

    {{-- Laporan Bulanan --}}
    <h3 class="mt-4">Laporan Bulanan</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Bulan</th>
                <th>Total Transaksi Belum Lunas</th>
                <th>Total Tagihan Belum Lunas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($monthlyReport[$selectedYear] as $month => $data)
                <tr>
                    <td>
                        <a href="{{ route('report.unpaid.details', ['year' => $selectedYear, 'month' => $month]) }}">
                            {{ \Carbon\Carbon::createFromDate(2000, (int)$month, 1)->format('F') }}
                        </a>
                    </td>
                    <td>{{ $data['total_unpaid'] }}</td>
                    <td>{{ number_format($data['total_due'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Laporan Semester --}}
    <h3 class="mt-4">Laporan Semester</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Semester</th>
                <th>Total Transaksi Belum Lunas</th>
                <th>Total Tagihan Belum Lunas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($semesterReport[$selectedYear] as $semester => $data)
                <tr>
                    <td>Semester {{ $semester }}</td>
                    <td>{{ $data['total_unpaid'] }}</td>
                    <td>{{ number_format($data['total_due'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection