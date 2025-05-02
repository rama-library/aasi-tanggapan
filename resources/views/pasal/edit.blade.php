@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title"><h1>Edit Pasal</h1></div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 col-lg-12">
                        <form action="{{ route('admin.pasal.update', ['document' => $document->slug, 'pasal' => $pasal->id]) }}" method="POST">
                            @method('PUT')
                            @include('forms.pasalform', ['pasal' => $pasal, 'submit' => 'Perbarui'])
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
