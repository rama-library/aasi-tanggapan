@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title"><h1>Edit Dokumen</h1></div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 col-lg-12">
                        <form action="{{ route('admin.documents.update', $document->slug) }}" method="POST" class="mb-5 row g-3" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            @include('forms.documentform', ['submit' => 'Perbarui'])
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
