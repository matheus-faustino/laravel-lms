@extends('layouts.admin')

@section('title', __('admin/users.edit_title'))

@section('breadcrumbs')
    <x-breadcrumb :items="[
        ['label' => __('admin/dashboard.title'), 'url' => route('admin.dashboard.index')],
        ['label' => __('admin/users.page_title'), 'url' => route('admin.users.index')],
        ['label' => __('admin/users.edit_title')],
    ]" />
@endsection

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
                    {{ __('shared/ui.save_changes') }}
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">
                    {{ __('shared/ui.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
