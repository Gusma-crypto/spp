@extends('layouts.app')

@section('title', 'Data Kelas')

@section('content')
    <div class="card-body">
        <form action="{{ route('master.class.update', $data->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="mb-3">
                <label for="name" class="form-label"><span class="text-danger">* </span>Nama Kelas</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ $data->name }}" required />
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-outline-info">Ubah</button>
            </div>
        </form>
    </div>
@endsection
