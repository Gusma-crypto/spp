@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')
    <div class="card-body">
        <form action="{{ route('master.student.store') }}" method="post" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="class_id" class="form-label"><span class="text-danger">* </span>Kelas</label><br>
                <select name="class_id" id="class_id" class="data-select" style="width: 100%" required>
                    <option value="" disabled selected>Pilih Kelas</option>
                    @foreach ($classes as $x)
                        <option value="{{ $x->id }}">{{ $x->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="academic_year_id" class="form-label"><span class="text-danger">* </span>Tahun Ajaran</label><br>
                <select name="academic_year_id" id="academic_year_id" class="data-select" style="width: 100%" required>
                    <option value="" disabled selected>Pilih Tahun Ajaran</option>
                    @foreach ($years as $x)
                        <option value="{{ $x->id }}">{{ $x->year }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="nisn" class="form-label"><span class="text-danger">* </span>NISN</label>
                <input type="text" name="nisn" id="nisn" class="form-control" required />
            </div>

            <div class="mb-3">
                <label for="first_name" class="form-label"><span class="text-danger">* </span>Nama Awal</label>
                <input type="text" name="first_name" id="first_name" class="form-control" required />
            </div>

            <div class="mb-3">
                <label for="last_name" class="form-label"><span class="text-danger">* </span>Nama Akhir</label>
                <input type="text" name="last_name" id="last_name" class="form-control" required />
            </div>

            <div class="mb-3">
                <label for="gender" class="form-label"><span class="text-danger">* </span>Jenis Kelamin</label>
                <select name="gender" class="form-control" required>
                    <option value="" selected disabled>Silahkan Pilih Jenis Kelamin</option>
                    <option value="Pria">Pria</option>
                    <option value="Wanita">Wanita</option>
                    <option value="Tidak Diketahui">Tidak Diketahui</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="parent_status" class="form-label"><span class="text-danger">* </span>Status Orang Tua / Wali</label>
                <select name="parent_status" class="form-control" required>
                    <option value="" selected disabled>Silahkan Pilih Status</option>
                    <option value="Lengkap">Lengkap</option>
                    <option value="Yatim">Yatim</option>
                    <option value="Piatu">Piatu</option>
                    <option value="Yatim Piatu">Yatim Piatu</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="parents_name" class="form-label"><span class="text-danger">* </span>Nama Orang Tua / Wali</label>
                <input type="text" name="parent_name" id="parents_name" class="form-control" required />
            </div>

            <div class="mb-3">
                <label for="email" class="form-label"><span class="text-danger">* </span>Email</label>
                <input type="email" name="email" id="email" class="form-control" required />
            </div>

            <div class="mb-3">
                <label for="password" class="form-label"><span class="text-danger">* </span>Password</label>
                <input type="text" name="password" id="password" class="form-control" required />
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label"><span class="text-danger">* </span>Nomor Telepon</label>
                <input type="text" name="phone" id="phone" class="form-control" required />
            </div>

            <div class="mb-3">
                <label for="Alamat Lengkap" class="form-label"><span class="text-danger">* </span>Alamat Lengkap</label>
                <input type="text" name="address" id="Alamat Lengkap" class="form-control" required />
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-outline-success">Simpan</button>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(() => {
            $(".data-select").select2({
                width: "resolve"
            });
        });
    </script>
@endsection
