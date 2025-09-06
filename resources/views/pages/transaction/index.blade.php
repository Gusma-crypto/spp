@extends('layouts.app')

@section('title', 'Transaksi SPP')

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
                            <a class="dropdown-item @if (isset($class) && $class === $x->name) active @endif" href="{{ route('spp.transaction.index', ['class' => $x->name]) }}">
                                {{ $x->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
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
                            <a href="{{ route('spp.transaction.show', $x->id) }}" class="btn btn-outline-info">Lihat Transaksi</a>
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
