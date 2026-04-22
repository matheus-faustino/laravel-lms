@extends('layouts.admin')

@section('title', __('admin/users.create_title'))

@section('content')
<div class="max-w-lg">
    <div class="card p-8">
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
            @csrf
            @include('admin.user.form')
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-primary">
                    <i class="bi bi-person-plus" aria-hidden="true"></i>
                    {{ __('admin/users.create_btn') }}
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">
                    {{ __('shared/ui.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
