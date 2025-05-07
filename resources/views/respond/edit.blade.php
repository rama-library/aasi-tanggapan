@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Tanggapan Pasal</h4>

    <div class="card">
        <div class="card-body">
            @php
                $isFinal = request()->is('tanggapan-final/*');
                $routeName = $isFinal ? 'tanggapan.final.update' : 'respond.update';
                $backroute = $isFinal ? 'tanggapan.final.detail' : 'tanggapan.berlangsung.detail';
            @endphp
            <form action="{{ route($routeName, ['document' => $document->slug, 'pasal' => $pasal->id, 'respond' => $respond->id]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label"><strong>Pasal:</strong></label>
                    <p class="form-control-plaintext">{{ $pasal->pasal }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Penjelasan:</strong></label>
                    <p class="form-control-plaintext">{{ $pasal->penjelasan }}</p>
                </div>

                <div class="mb-3">
                    <label for="tanggapan" class="form-label">Tanggapan</label>
                    <textarea name="tanggapan" id="tanggapan" rows="4" class="form-control" required>{{ old('tanggapan', $respond->tanggapan) }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="alasan" class="form-label">Alasan Revisi</label>
                    <textarea name="alasan" id="alasan" rows="3" class="form-control" required>{{ old('alasan') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route($backroute, $document->slug) }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection