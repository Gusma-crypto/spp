@extends('layouts.app')

@section('title', 'Data Tahun Ajaran')

@section('content')
    <div class="card-body">
        <form action="{{ route('master.year.store') }}" method="post" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="year" class="form-label"><span class="text-danger">* </span>Tahun</label>
                <input type="text" name="year" id="year" class="form-control" required />
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-outline-success">Simpan</button>
            </div>
        </form>
    </div>
@endsection
