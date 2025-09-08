@extends('layouts.app')

@section('title', 'Data Pengeluaran')

@section('content')
@php
    $role = Auth::check() ? App\Models\Role::find(Auth::user()->role_id) : null;
@endphp

<div class="card-body">
    <div class="row mb-4">
        @if($role && ($role->name === "Bendahara" || $role->name === "Super Admin"))
            <div class="col-10">
                <a href="{{ route('master.expense.create') }}" class="btn btn-outline-success btn-block">Tambah Data</a>
            </div>
        @endif
        <div class="col">
            <button type="button" class="btn btn-outline-success btn-block" data-bs-toggle="modal" data-bs-target="#yearModal">
                <span class="bi bi-file-earmark-excel"></span> Export
            </button>
        </div>
    </div>

    <table id="table-data" class="table table-bordered table-sm align-middle">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Jumlah Pengeluaran</th>
                <th>Keterangan</th>
                <th>Status</th>
                <th>Bukti File</th>
                @if($role && ($role->name === "Bendahara" || $role->name === "Super Admin"))
                    <th>Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $x)
                <tr>
                    <td>{{ $x->no }}</td>
                    <td>{{ \Carbon\Carbon::parse($x->date)->format('d/m/Y') }}</td>
                    <td>{{ $x->total_expense }}</td>
                    <td>{{ $x->note }}</td>
                    <td style="
                        color: {{ $x->status === 'pending' ? 'orange' : ($x->status === 'approved' ? 'green' : ($x->status === 'rejected' ? 'red' : 'black')) }};
                        font-style: {{ $x->status === 'pending' ? 'italic' : 'normal' }};
                        font-weight: {{ $x->status === 'pending' ? 'bold' : 'normal' }};
                    ">
                        @php $rolesCanSeeStatus = ['Bendahara', 'Super Admin']; @endphp
                        @if(in_array($role->name, $rolesCanSeeStatus))
                            {{ ucfirst($x->status) }}
                        @elseif($role->name === "Kepala Sekolah")
                            @if($x->status === 'pending')
                                <form action="{{ route('master.expense.approve', $x->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success btn-sm">Approve</button>
                                </form>
                                <form action="{{ route('master.expense.reject', $x->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Reject</button>
                                </form>
                            @else
                                {{ ucfirst($x->status) }}
                            @endif
                        @endif
                    </td>
                    <td>
                        @if ($x->file_path)
                            <div class="d-grid gap-2">
                                <a download href="{{ asset($x->file_path) }}" class="btn btn-outline-primary btn-sm">Download File</a>
                            </div>
                        @else
                            -
                        @endif
                    </td>
                    @if($role && ($role->name === "Bendahara" || $role->name === "Super Admin"))
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('master.expense.edit', $x->id) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('master.expense.destroy', $x->id) }}" method="POST" class="d-grid gap-2">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Modal Export --}}
    <div class="modal fade" id="yearModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form action="{{ route('report.export.expense') }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Export Data Pengeluaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="from" class="form-label">Dari Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="from" id="from" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="to" class="form-label">Sampai Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="to" id="to" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-outline-success">Export</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function () {
    @if(Session::has('success'))
        notyf.success(@json(Session::get('success')));
    @endif
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            notyf.error(@json($error));
        @endforeach
    @endif
    @if (Session::has('error'))
        notyf.error(@json(Session::get('error')));
    @endif
});
</script>
@endsection
