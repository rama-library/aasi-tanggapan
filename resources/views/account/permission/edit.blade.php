@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header"><strong>Edit Permission</strong></div>
    <div class="card-body">
        <form action="{{ route('admin.permissions.update', $permission) }}" method="POST">
            @csrf
            @method('PUT')
            @include('forms.permissionform', ['submit' => 'Perbarui', 'permission' => $permission])
        </form>
    </div>
</div>
@endsection
