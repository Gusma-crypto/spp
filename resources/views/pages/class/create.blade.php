@extends('layouts.app')

@section('title', 'Data Kelas')

@section('content')
    <div class="card-body">
        <form action="{{ route('master.class.store') }}" method="post" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label"><span class="text-danger">* </span>Nama Kelas</label>
                <input type="text" name="name" id="name" class="form-control" required />
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-outline-success">Simpan</button>
            </div>
        </form>
    </div>
@endsection
