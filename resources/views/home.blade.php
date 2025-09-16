@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    if (Auth::check()) {
        $role = App\Models\Role::find(Auth::user()->role_id);
    }
@endphp
<div class="container py-4">
    {{-- Header --}}
    <div class="text-center mb-3">
        <h4 class="mb-2">Selamat Datang di Sistem Informasi Pembayaran SPP</h4>
        <h5 class="mb-3">SMA NEGERI 1 LUMBAN JULU</h5>
        <!-- <img src="{{ asset('assets/static/images/logo-sumut.png') }}" alt="logo-sumut" class="img-fluid w-25" /> -->
         <img src="{{ asset('assets/static/images/logo-sumut.png') }}" 
     alt="logo-sumut" class="img-fluid" style="width:150px; height:auto;">

    </div>

    {{-- Statistik Kartu --}}
    <div class="row g-3 mb-3 mx-2">
        {{-- Nama Sekolah --}}
        <div class="col-md-3 col-sm-6">
            <div class="card text-white bg-primary shadow-sm">
                <div class="card-body text-center p-3">
                    <img src="{{ asset('assets/static/images/icon/school.png') }}" 
                        alt="icon" class="img-fluid mb-2" style="width:30px;height:30px;">
                    <h6 class="card-title mb-1" style="font-size: 14px;">Nama Sekolah</h6>
                    <p class="card-text mb-0" style="font-size: 13px;">SMAN 1 LUMBANJULU</p>
                </div>
            </div>
        </div>

        {{-- Kepala Sekolah --}}
        <div class="col-md-3 col-sm-6">
            <div class="card text-white bg-primary shadow-sm">
                <div class="card-body text-center p-3">
                    <img src="{{ asset('assets/static/images/icon/head.png') }}" 
                        alt="icon" class="img-fluid mb-2" style="width:30px;height:30px;">
                    <h6 class="card-title mb-1" style="font-size: 14px;">Kepala Sekolah</h6>
                    <p class="card-text mb-0" style="font-size: 13px;">
                        {{ $kepalaSekolah->first_name.' '.$kepalaSekolah->last_name }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Siswa Aktif --}}
        <div class="col-md-3 col-sm-6">
            <div class="card text-white bg-success shadow-sm">
                <div class="card-body text-center p-3">
                    <img src="{{ asset('assets/static/images/icon/student.png') }}" 
                        alt="icon" class="img-fluid mb-2" style="width:30px;height:30px;">
                    <h6 class="card-title mb-1" style="font-size: 14px;">Siswa Aktif</h6>
                    <p class="card-text mb-0" style="font-size: 13px;">{{ $siswa }} Orang</p>
                </div>
            </div>
        </div>

        {{-- Pengguna Aktif --}}
        <div class="col-md-3 col-sm-6">
            <div class="card text-white bg-warning shadow-sm">
                <div class="card-body text-center p-3">
                    <img src="{{ asset('assets/static/images/icon/teacher.png') }}" 
                        alt="icon" class="img-fluid mb-2" style="width:30px;height:30px;">
                    <h6 class="card-title mb-1" style="font-size: 14px;">Pengguna Aktif</h6>
                    <p class="card-text mb-0" style="font-size: 13px;">{{ $pengguna }} Orang</p>
                </div>
            </div>
        </div>
    </div>


@if($role->name === 'Super Admin' || $role->name === 'Kepala Sekolah' || $role->name === 'Bendahara')
    {{-- Tabel Siswa Belum Lunas --}}
    <div class="row mb-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-center">
                    <h5>Siswa Belum Lunas Bulan Ini ({{ \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->format('F Y') }})</h5>
                </div>
                {{-- Filter & Export --}}
                <div class="row mb-3 justify-content-center">
                    <div class="col-md-8">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                            {{-- Form Filter --}}
                            <form method="GET" action="{{ route('dashboard') }}" 
                                class="d-flex align-items-center gap-2 flex-grow-1">
                                <select name="kelas_id" class="form-control">
                                    <option value="">-- Semua Kelas --</option>
                                    @foreach($kelasList as $kelas)
                                        <option value="{{ $kelas->id }}" 
                                            {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                            {{ $kelas->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Filter
                                </button>

                                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                    <i class="fas fa-sync"></i> Reset
                                </a>
                            </form>

                            {{-- Export Button --}}
                            <a href="{{ route('export.unpaid', ['kelas_id' => request('kelas_id')]) }}" 
                                class="btn btn-success">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Tagihan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($unpaidTransactions as $key => $trx)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        @if($trx->student)
                                            <a href="{{ route('spp.transaction.show', $trx->student->id) }}">
                                                {{ $trx->student->first_name ?? '-' }} {{ $trx->student->last_name ?? '-' }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $trx->student->mclass->name ?? '-' }}</td>
                                    <td>{{ number_format($trx->price, 0, ',', '.') }}</td>
                                    <td>{{ ucfirst($trx->status) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Semua siswa sudah lunas bulan ini</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif
@if($role->name === 'Siswa')
<div class="row mb-5">
     {{-- Ringkasan Keuangan --}}
    <div class="row mb-4 mx-2 ">
        <div class="col-md-4">
            <div class="card text-bg-primary shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Total Tagihan</h5>
                    <p class="fs-4 fw-bold">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-success shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Sudah Dibayar</h5>
                    <p class="fs-4 fw-bold">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-danger shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Sisa Tagihan</h5>
                    <p class="fs-4 fw-bold">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Bulan Lunas & Belum Lunas --}}
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white text-center">
                <h6>Bulan Belum Lunas ({{ $currentYear }})</h6>
            </div>
            <div class="card-body">
                @if($unpaidMonths->isEmpty())
                    <p class="text-success">Semua bulan sudah lunas 🎉</p>
                @else
                    <ul class="list-group">
                        @foreach($unpaidMonths as $month)
                            <li class="list-group-item list-group-item-warning">
                                {{ $month }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white text-center">
                <h6>Bulan Lunas ({{ $currentYear }})</h6>
            </div>
            <div class="card-body">
                @if($paidMonths->isEmpty())
                    <p class="text-danger">Belum ada bulan yang lunas</p>
                @else
                    <ul class="list-group">
                        @foreach($paidMonths as $month)
                            <li class="list-group-item list-group-item-success">
                                {{ $month }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

</div>

@endsection