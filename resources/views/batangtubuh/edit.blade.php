@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title"><h1>Edit Batang Tubuh</h1></div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 col-lg-12">
                        <form action="{{ route('admin.batangtubuh.update', ['document' => $document->slug, 'batangtubuh' => $batangtubuh->id]) }}" method="POST">
                            @method('PUT')
                            @include('forms.batangtubuhform', ['batangtubuh' => $batangtubuh, 'submit' => 'Perbarui'])
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
