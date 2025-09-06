@extends('layouts.app')

@section('title', 'Data Pengguna Aplikasi')

@section('content')
    <div class="card-body">
        <form action="{{ route('master.user.update', $data->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="mb-3">
                <label for="role_id" class="form-label"><span class="text-danger">* </span>Role</label><br>
                <select name="role_id" id="role_id" class="data-select" style="width: 100%" required>
                    <option value="" disabled selected>Pilih Role</option>
                    @foreach ($role as $x)
                        <option value="{{ $x->id }}"  @if ($x->id === $data->role_id) selected @endif>{{ $x->name }}</option>
                    @endforeach
                </select>
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
