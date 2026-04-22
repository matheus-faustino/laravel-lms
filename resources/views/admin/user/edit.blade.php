@extends('layouts.admin')

@section('title', 'Edit user')

@section('content')
<div class="max-w-lg">
    <div class="card p-8">
        <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="space-y-5">
            @csrf
            @method('PUT')
            @include('admin.user.form')
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-primary">
                    <i class="bi bi-floppy" aria-hidden="true"></i>
                    Save changes
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
