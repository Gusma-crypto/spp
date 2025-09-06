@extends('layouts.app')

@section('title', 'Laporan Pemasukan')

@section('content')
    <div class="card-body">
        <div class="d-flex justify-content-end align-items-center mb-3">
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#yearModal"><span class="bi bi-file-earmark-excel"></span> Export</button>
        </div>

        <table id="table-data">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Total Pemasukan</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key => $x)
                    <tr>
                        <td>{{ $x->no }}</td>
                        <td>{{ \Carbon\Carbon::parse($x->date)->format('d/m/Y') }}</td>
                        <td>{{ $x->total_income }}</td>
                        <td>{{ $x->note }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="modal fade" id="yearModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <form action="{{ route('report.export.income') }}" method="post">
                        @csrf

                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Export Data Pemasukan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="from" class="form-label"><span class="text-danger">* </span>Dari Tanggal</label>
                                <input type="date" name="from" id="from" class="form-control" required />
                            </div>

                            <div class="mb-3">
                                <label for="to" class="form-label"><span class="text-danger">* </span>Sampai Tanggal</label>
                                <input type="date" name="to" id="to" class="form-control" required />
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
            $(".data-select").select2({
                width: "resolve"
            });

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
