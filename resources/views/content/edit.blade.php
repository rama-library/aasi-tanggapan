@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title"><h1>Edit Konten</h1></div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 col-lg-12">
                        <form action="{{ route('admin.content.update', ['document' => $document->slug, 'content' => content->id]) }}" method="POST">
                            @method('PUT')
                            @include('forms.contentform', ['content' => $content, 'submit' => 'Perbarui'])
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
