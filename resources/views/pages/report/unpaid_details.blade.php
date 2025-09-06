@extends('layouts.app')

@section('title', 'Detail Siswa Belum Lunas')

@section('content')
<div class="container" style="padding: 20px;">
    <h3 class="mb-4 text-center">
        Detail Siswa Belum Lunas - 
        {{ \Carbon\Carbon::createFromDate(2000, (int)$month, 1)->format('F') }} {{ $year }}
    </h3>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
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
                @forelse($transactions as $key => $trx)
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
                        <td colspan="5" class="text-center color-red">Tidak ada data siswa belum lunas</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="text-center mt-3">
        <a href="{{ route('report.unpaid.index') }}" class="btn btn-primary"> << Kembali ke Laporan Bulanan</a>
    </div>
</div>
@endsection
