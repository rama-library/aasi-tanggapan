@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h4 class="mb-3">Tanggapan untuk Pasal: <strong>{{ $pasal->pasal }}</strong></h4>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('respond.store', ['document' => $document->slug, 'pasal' => $pasal->id]) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="tanggapan" class="form-label">Tanggapan</label>
                    <textarea name="tanggapan" id="tanggapan" class="form-control" rows="5" required>{{ old('tanggapan') }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">Kirim Tanggapan</button>
                <a href="{{ route('tanggapan.detail', $document->slug) }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection