@extends('layouts.app')

@section('title', 'Laporan Transaksi SPP')

@section('content')
    <div class="card-body">
        <div class="d-flex justify-content-end align-items-center mb-3">
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#exportModal">
                <span class="bi bi-file-earmark-excel"></span> Export
            </button>
        </div>

        <table id="table-data" class="table table-bordered table-striped">
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
                            <a href="{{ route('report.spp.detail', ['id' => $x->id]) }}" 
                               class="btn btn-outline-success">
                                <span class="bi bi-eye-fill"></span> Lihat
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Modal Export -->
        <div class="modal fade" id="exportModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exportModalLabel">Export Transaksi SPP</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Dropdown pilihan tipe -->
                        <div class="mb-3">
                            <label for="exportType" class="form-label">Pilih Berdasarkan</label>
                            <select id="exportType" class="form-select">
                                <option value="" selected disabled>-- Pilih --</option>
                                <option value="kelas">Kelas</option>
                                <option value="tahun">Tahun Ajaran</option>
                            </select>
                        </div>

                        <!-- Dropdown dinamis -->
                        <div class="mb-3">
                            <label for="exportOption" class="form-label">Opsi</label>
                            <select id="exportOption" class="form-select">
                                <option value="" selected disabled>Silakan pilih terlebih dahulu</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-outline-success" id="btnExport">Export</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    $(document).ready(function () {
        // Notifikasi session
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

        // Ganti isi dropdown kedua berdasarkan pilihan
        $("#exportType").on("change", function () {
            const type = $(this).val();
            let options = '<option value="" selected disabled>-- Pilih --</option>';

            if (type === "kelas") {
                options += `<option value="kelas-all">Semua Kelas</option>`;
                @foreach ($classes as $x)
                    options += `<option value="kelas-{{ $x->id }}">{{ $x->name }}</option>`;
                @endforeach
            } else if (type === "tahun") {
                options += `<option value="tahun-all">Semua Tahun Ajaran</option>`;
                @foreach ($academicYears as $x)
                    options += `<option value="tahun-{{ $x->id }}">{{ $x->year }}</option>`;
                @endforeach
            }

            $("#exportOption").html(options);
        });

        // Tombol export
        $("#btnExport").on('click', function () {
            const selected = $("#exportOption").val();
            if (!selected) {
                notyf.error("Silakan pilih opsi export terlebih dahulu");
                return;
            }

            let url = "";

           if (selected.startsWith("kelas-")) {
                const classId = selected.replace("kelas-", "");
                url = "{{ route('report.export.class') }}" + "?class_id=" + classId;
            } else if (selected.startsWith("tahun-")) {
                const yearId = selected.replace("tahun-", "");
                url = "{{ route('report.export.year', ':id') }}".replace(':id', yearId);
            }

            window.open(url, '_blank');
        });
    });
</script>
@endsection
