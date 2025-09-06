@extends('layouts.app')

@section('title', 'Laporan Transaksi SPP')

@section('content')
    <div class="card-body">
        <div class="d-flex justify-content-end align-items-center mb-3">
            <button type="button" class="btn btn-outline-success me-3" data-bs-toggle="modal" data-bs-target="#classModal"><span class="bi bi-file-earmark-excel"></span> Export Berdasarkan Kelas</button>
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#yearModal"><span class="bi bi-file-earmark-excel"></span> Export Berdasarkan Tahun Ajaran</button>
        </div>

        <table id="table-data">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kelas</th>
                    <th>Tahun Ajaran</th>
                    <th>NISN</th>
                    <th>Nama Lengkap</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key => $x)
                    <tr>
                        <td>{{ $x->no }}</td>
                        <td>{{ $x->class_name }}</td>
                        <td>{{ $x->year }}</td>
                        <td>{{ $x->nisn }}</td>
                        <td>{{ $x->first_name . ' ' . $x->last_name }}</td>
                        <td class="d-grid gap-2">
                            <a href="{{ route('report.spp.detail', ['id' => $x->id]) }}" class="btn btn-outline-success"><span class="bi bi-eye-fill"></span> Lihat</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="modal fade" id="classModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Export Transaksi SPP Berdasarkan Kelas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <select name="class" id="class" class="data-select" style="width: 100%; z-index: 1;">
                                <option value="" selected disabled>Pilih Kelas</option>
                                @foreach ($classes as $x)
                                    <option value="{{ $x->id }}">{{ $x->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <select name="class" id="yearClass" class="data-select" style="width: 100%; z-index: 1;">
                            <option value="" selected disabled>Pilih Tahun Ajaran</option>
                            @foreach ($academicYears as $x)
                                <option value="{{ $x->id }}">{{ $x->year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-outline-success" id="btnExportClass">Export</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="yearModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Export Transaksi SPP Berdasarkan Tahun Ajaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <select name="class" id="academicYear" class="data-select" style="width: 100%; z-index: 1;">
                            <option value="" selected disabled>Pilih Tahun Ajaran</option>
                            @foreach ($academicYears as $x)
                                <option value="{{ $x->id }}">{{ $x->year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-outline-success" id="btnExportYear">Export</button>
                    </div>
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

            $("#btnExportClass").on('click', function () {
                const classId = $("#class").find(":selected").val();
                const academicYearId = $("#yearClass").find(":selected").val();

                let url = "{!! route('report.export.class', ['class_id' => '__CLASS_ID__', 'academic_year_id' => '__YEAR_ID__']) !!}";
                url = url.replace('__CLASS_ID__', classId).replace('__YEAR_ID__', academicYearId);

                window.open(url, '_blank');
            });

            $("#btnExportYear").on('click', function () {
                const academicYearId = $("#academicYear").find(":selected").val();

                let url = "{!! route('report.export.year', '__YEAR_ID__') !!}";
                url = url.replace('__YEAR_ID__', academicYearId);

                window.open(url, '_blank');
            });
        });
    </script>
@endsection
