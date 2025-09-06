@extends('layouts.app')

@section('title', 'Laporan SPP Detail')

@section('content')
<div class="container" style="padding: 20px;">
    <p>Kelas: {{ $student->class_name }} | NISN: {{ $student->nisn }}</p>

    {{-- Filter semester --}}
    <form method="GET" action="{{ route('report.spp.detail', $student->id) }}" class="d-flex mb-3">
        <select name="semester" class="form-select me-2" style="width: 220px">
            <option value="1" {{ request('semester') == 1 ? 'selected' : '' }}>Semester 1 (Juli - Desember)</option>
            <option value="2" {{ request('semester') == 2 ? 'selected' : '' }}>Semester 2 (Januari - Juni)</option>
        </select>

        <select name="year" class="form-select me-2" style="width: 150px">
            @for($i = date('Y'); $i >= 2020; $i--)
                <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>
                    {{ $i }}/{{ $i+1 }}
                </option>
            @endfor
        </select>

        <button type="submit" class="btn btn-primary">Tampilkan</button>
    </form>

    {{-- Tabel transaksi --}}
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>No</th>
                <th>Bulan</th>
                <th>Harga</th>
                <th>Jenis Pembayaran</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $key => $trx)
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($trx->date)->translatedFormat('F Y') }}</td>
                    <td>Rp {{ number_format($trx->price, 0, ',', '.') }}</td>
                    <td>{{ $trx->type ?? '-' }}</td>
                    <td>{{ $trx->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data transaksi</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Rekap semester --}}
    <div class="card mt-3 p-3">
        <h5>Rekap Semester {{ $semester }}</h5>
        <p>Total Tagihan : <b>Rp {{ number_format($totalTagihan, 0, ',', '.') }}</b></p>
        <p>Total Dibayar : <b>Rp {{ number_format($totalDibayar, 0, ',', '.') }}</b></p>
        <p>Sisa Belum Lunas : <b>Rp {{ number_format($sisaBelumLunas, 0, ',', '.') }}</b></p>
    </div>

</div>
@endsection
