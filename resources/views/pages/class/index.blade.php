@extends('layouts.app')

@section('title', 'Data Kelas')

@section('content')
    <div class="card-body">
        <a href="{{ route('master.class.create') }}" class="btn btn-outline-success btn-block mb-4">Tambah Data</a>

        <table id="table-data">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kelas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $x)
                    <tr>
                        <td>{{ $x->no }}</td>
                        <td>{{ $x->name }}</td>
                        <td class="d-grid gap-2">
                            <a href="{{ route('master.class.edit', $x->id) }}" class="btn btn-outline-info mb-1">Ubah</a>

                            <form action="{{ route('master.class.destroy', $x->id) }}" method="post" class="d-grid gap-2">
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
