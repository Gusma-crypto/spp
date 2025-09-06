@extends('layouts.app')

@section('title', 'Data Tahun Ajaran')

@section('content')
    <div class="card-body">
        <form action="{{ route('master.year.update', $data->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="mb-3">
                <label for="year" class="form-label"><span class="text-danger">* </span>Tahun</label>
                <input type="text" name="year" id="year" class="form-control" value="{{ $data->year }}" required />
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-outline-info">Ubah</button>
            </div>
        </form>
    </div>
@endsection
