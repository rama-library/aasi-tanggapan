@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header"><strong>Tambah Permission</strong></div>
    <div class="card-body">
        <form action="{{ route('admin.permissions.store') }}" method="POST">
            @csrf
            @include('forms.permissionform', ['submit' => 'Simpan'])
        </form>
    </div>
</div>
@endsection
