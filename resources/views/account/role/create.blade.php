@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header"><strong>Tambah Role</strong></div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 col-lg-12">
                <form action="{{ route('roles.store') }}" method="POST">
                    @csrf
                    @include('forms.roleform', ['submit' => 'Simpan'])
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
