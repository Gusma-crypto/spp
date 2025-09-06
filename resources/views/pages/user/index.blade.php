@extends('layouts.app')

@section('title', 'Data Pengguna Aplikasi')

@section('content')
    <div class="card-body">
        <a href="{{ route('master.user.create') }}" class="btn btn-outline-success btn-block mb-4">Tambah Data</a>

        <table id="table-data">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Role</th>
                    <th>Nama Awal</th>
                    <th>Nama Akhir</th>
                    <th>Jenis Kelamin</th>
                    <th>Email</th>
                    <th>Nomor Telepon</th>
                    <th>Alamat Lengkap</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key => $x)
                    <tr>
                        <td>{{ $x->no }}</td>
                        <td>{{ $x->role_name }}</td>
                        <td>{{ $x->first_name }}</td>
                        <td>{{ $x->last_name }}</td>
                        <td>{{ $x->gender }}</td>
                        <td>{{ $x->email }}</td>
                        <td>{{ $x->phone }}</td>
                        <td>{{ $x->address }}</td>
                        <td class="d-grid gap-2">
                            <a href="{{ route('master.user.edit', $x->id) }}" class="btn btn-outline-info mb-1">Ubah</a>

                            <form action="{{ route('master.user.destroy', $x->id) }}" method="post" class="d-grid gap-2">
                                @csrf
                                @method('delete')

                                <button type="submit" class="btn btn-outline-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
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
