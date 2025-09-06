@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')
    <div class="card-body">
        <div class="d-flex justify-content-start align-items-center mb-5">
            <div class="btn-group" role="group">
                <button id="btnGroupDrop1" type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    Pilih Kelas
                </button>
                <ul class="dropdown-menu shadow overflow-scroll" aria-labelledby="btnGroupDrop1" style="max-height: 120px;">
                    @foreach ($classes as $x)
                        <li>
                            <a class="dropdown-item @if (isset($class) && $class === $x->name) active @endif" href="{{ route('master.student.index', ['class' => $x->name]) }}">
                                {{ $x->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <a href="{{ route('master.student.create') }}" class="btn btn-outline-success btn-block mb-4">Tambah Data</a>

        <table id="table-data">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kelas</th>
                    <th>Tahun Ajaran</th>
                    <th>NISN</th>
                    <th>Nama Awal</th>
                    <th>Nama Akhir</th>
                    <th>Jenis Kelamin</th>
                    <th>Status Orang Tua / Wali</th>
                    <th>Nama Orang Tua / Wali</th>
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
                        <td>{{ $x->class_name }}</td>
                        <td>{{ $x->year }}</td>
                        <td>{{ $x->nisn }}</td>
                        <td>{{ $x->first_name }}</td>
                        <td>{{ $x->last_name }}</td>
                        <td>{{ $x->gender }}</td>
                        <td>{{ $x->parent_status }}</td>
                        <td>{{ $x->parent_name }}</td>
                        <td>{{ $x->email }}</td>
                        <td>{{ $x->phone }}</td>
                        <td>{{ $x->address }}</td>
                        <td class="d-grid gap-2">
                            <a href="{{ route('master.student.edit', $x->id) }}" class="btn btn-outline-info mb-1"><i class="bi bi-pencil-square"></i></a>

                            <form action="{{ route('master.student.destroy', $x->id) }}" method="post" class="d-grid gap-2">
                                @csrf
                                @method('delete')

                                <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
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
