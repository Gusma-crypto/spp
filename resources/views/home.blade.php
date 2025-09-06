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
    <div class="text-center mb-5">
        <h4 class="mb-3">Selamat Datang di Sistem Informasi Pembayaran SPP</h4>
        <h5 class="mb-4">SMA NEGERI 1 LUMBAN JULU</h5>
        <img src="{{ asset('assets/static/images/logo-sumut.png') }}" alt="logo-sumut" class="img-fluid w-25" />
    </div>

    {{-- Statistik Kartu --}}
    <div class="row g-4 mb-5">
        {{-- Nama Sekolah --}}
        <div class="col-md-3 col-sm-6">
            <div class="card text-white bg-primary h-100">
                <div class="card-body text-center">
                    <img src="{{ asset('assets/static/images/icon/school.png') }}" alt="icon" class="img-fluid w-50 mb-2" />
                    <h6 class="card-title">Nama Sekolah</h6>
                    <p class="card-text">SMAN 1 LUMBANJULU</p>
                </div>
            </div>
        </div>

        {{-- Kepala Sekolah --}}
        <div class="col-md-3 col-sm-6">
            <div class="card text-white bg-primary h-100">
                <div class="card-body text-center">
                    <img src="{{ asset('assets/static/images/icon/head.png') }}" alt="icon" class="img-fluid w-50 mb-2" />
                    <h6 class="card-title">Kepala Sekolah</h6>
                    <p class="card-text">{{ $kepalaSekolah->first_name.' '.$kepalaSekolah->last_name }}</p>
                </div>
            </div>
        </div>

        {{-- Siswa Aktif --}}
        <div class="col-md-3 col-sm-6">
            <div class="card text-white bg-success h-100">
                <div class="card-body text-center">
                    <img src="{{ asset('assets/static/images/icon/student.png') }}" alt="icon" class="img-fluid w-50 mb-2" />
                    <h6 class="card-title">Siswa Aktif</h6>
                    <p class="card-text">{{ $siswa }} Orang</p>
                </div>
            </div>
        </div>

        {{-- Pengguna Aktif --}}
        <div class="col-md-3 col-sm-6">
            <div class="card text-white bg-warning h-100">
                <div class="card-body text-center">
                    <img src="{{ asset('assets/static/images/icon/teacher.png') }}" alt="icon" class="img-fluid w-50 mb-2" />
                    <h6 class="card-title">Pengguna Aktif</h6>
                    <p class="card-text">{{ $pengguna }} Orang</p>
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
</div>

@endsection
