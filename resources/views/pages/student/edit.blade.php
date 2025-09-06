@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')
    <div class="card-body">
        <form action="{{ route('master.student.update', $data->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="mb-3">
                <label for="class_id" class="form-label"><span class="text-danger">* </span>Kelas</label><br>
                <select name="class_id" id="class_id" class="data-select" style="width: 100%" required>
                    <option value="" disabled selected>Pilih Kelas</option>
                    @foreach ($classes as $x)
                        <option value="{{ $x->id }}" @if ($data->class_id === $x->id) selected @endif>{{ $x->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="academic_year_id" class="form-label"><span class="text-danger">* </span>Tahun Ajaran</label><br>
                <select name="academic_year_id" id="academic_year_id" class="data-select" style="width: 100%" required>
                    <option value="" disabled selected>Pilih Tahun Ajaran</option>
                    @foreach ($years as $x)
                        <option value="{{ $x->id }}" @if ($data->academic_year_id === $x->id) selected @endif>{{ $x->year }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="nisn" class="form-label"><span class="text-danger">* </span>NISN</label>
                <input type="text" name="nisn" id="nisn" class="form-control" value="{{ $data->nisn }}" required />
            </div>

            <div class="mb-3">
                <label for="first_name" class="form-label"><span class="text-danger">* </span>Nama Awal</label>
                <input type="text" name="first_name" id="first_name" class="form-control" value="{{ $data->first_name }}" required />
            </div>

            <div class="mb-3">
                <label for="last_name" class="form-label"><span class="text-danger">* </span>Nama Akhir</label>
                <input type="text" name="last_name" id="last_name" class="form-control" value="{{ $data->last_name }}" required />
            </div>

            <div class="mb-3">
                <label for="gender" class="form-label"><span class="text-danger">* </span>Jenis Kelamin</label>
                <select name="gender" class="form-control" required>
                    <option value="" selected disabled>Silahkan Pilih Jenis Kelamin</option>
                    <option value="Pria" @if ($data->gender === "Pria") selected @endif>Pria</option>
                    <option value="Wanita" @if ($data->gender === "Wanita") selected @endif>Wanita</option>
                    <option value="Tidak Diketahui" @if ($data->gender === "Tidak Diketahui") selected @endif>Tidak Diketahui</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="parent_status" class="form-label"><span class="text-danger">* </span>Status Orang Tua / Wali</label>
                <select name="parent_status" class="form-control" required>
                    <option value="" selected disabled>Silahkan Pilih Status</option>
                    <option value="Lengkap" @if ($data->parent_status === "Lengkap") selected @endif>Lengkap</option>
                    <option value="Yatim" @if ($data->parent_status === "Yatim") selected @endif>Yatim</option>
                    <option value="Piatu" @if ($data->parent_status === "Piatu") selected @endif>Piatu</option>
                    <option value="Yatim Piatu" @if ($data->parent_status === "Yatim Piatu") selected @endif>Yatim Piatu</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="parents_name" class="form-label"><span class="text-danger">* </span>Nama Orang Tua / Wali</label>
                <input type="text" name="parent_name" id="parents_name" class="form-control" value="{{ $data->parent_name }}" required />
            </div>

            <div class="mb-3">
                <label for="email" class="form-label"><span class="text-danger">* </span>Email</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ $data->email }}" required />
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label"><span class="text-danger">* </span>Nomor Telepon</label>
                <input type="text" name="phone" id="phone" class="form-control" value="{{ $data->phone }}" required />
            </div>

            <div class="mb-3">
                <label for="Alamat Lengkap" class="form-label"><span class="text-danger">* </span>Alamat Lengkap</label>
                <input type="text" name="address" id="Alamat Lengkap" class="form-control" value="{{ $data->address }}" required />
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-outline-info">Ubah</button>
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
