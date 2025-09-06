@extends('layouts.app')

@section('title', 'Data Tahun Ajaran')

@section('content')
    <div class="card-body">
        <a href="{{ route('master.year.create') }}" class="btn btn-outline-success btn-block mb-4">Tambah Data</a>

        <table id="table-data">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tahun</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key => $x)
                    <tr>
                        <td>{{ $x->no }}</td>
                        <td>{{ $x->year }}</td>
                        <td class="d-grid gap-2">
                            <a href="{{ route('master.year.edit', $x->id) }}" class="btn btn-outline-info mb-1"><i class="bi bi-pencil-square"></i></a>

                            <form action="{{ route('master.year.destroy', $x->id) }}" method="post" class="d-grid gap-2">
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
