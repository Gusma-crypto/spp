@extends('layouts.app')

@section('title', 'Data Pengeluaran')

@section('content')
    <div class="card-body">
        <form action="{{ route('master.expense.update', $data->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="mb-3">
                <label for="date" class="form-label"><span class="text-danger">* </span>Tanggal</label>
                <input type="date" name="date" id="date" class="form-control" required value="{{ $data->date }}" />
            </div>

            <div class="mb-3">
                <label for="total_expense" class="form-label"><span class="text-danger">* </span>Jumlah Pemasukan</label>
                <input type="text" name="total_expense" id="total_expense" class="form-control" required value="{{ $data->total_expense }}" />
            </div>

            <div class="mb-3">
                <label for="note" class="form-label"><span class="text-danger">* </span>Keterangan</label>
                <textarea name="note" id="note" class="form-control" cols="30" rows="5">{{ $data->note }}</textarea>
            </div>

            <div class="mb-3">
                <label for="file" class="form-label"><span class="text-danger">* </span>Upload Bukti Pengeluaran</label>
                <div class="row">
                    <div class="col-10">
                        <input type="file" name="file" id="file" class="form-control" accept=".pdf,.xlsx,.xls,.csv,.doc,.docx,.png,.jpeg,.jpg,.gif" required />
                    </div>
                    <div class="col">
                        @if ($data->file_path != null)
                            <div class="d-grid gap-2">
                                <a download href="{{ asset($data->file_path) }}" class="btn btn-outline-primary">Download File</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-outline-info">Ubah</button>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#total_expense').on('keyup', function() {
                let value = $(this).val();
                value = value.replace(/[^\d]/g, '');

                if (value) {
                    value = formatRupiah(value);
                }

                $(this).val(value);
            });

            function formatRupiah(angka, prefix) {
                let number_string = angka.replace(/[^,\d]/g, '').toString(),
                    split = number_string.split(','),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                return prefix == undefined ? 'Rp ' + rupiah : (prefix ? 'Rp ' + rupiah : rupiah);
            }
        });
    </script>
@endsection
