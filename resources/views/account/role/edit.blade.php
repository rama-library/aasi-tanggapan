@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header"><strong>Edit Role</strong></div>
    <div class="card-body">
        <form action="{{ route('roles.update', $role) }}" method="POST">
            @csrf
            @method('PUT')
            @include('forms.roleform', ['submit' => 'Perbarui', 'role' => $role])
        </form>
    </div>
</div>
@endsection
