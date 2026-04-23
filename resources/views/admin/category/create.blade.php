@extends('layouts.admin')

@section('title', __('admin/categories.create_title'))

@section('content')
<div class="max-w-lg">
    <div class="card p-8">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-5">
            @csrf
            @include('admin.category.form')
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-primary">
                    <i class="bi bi-tag" aria-hidden="true"></i>
                    {{ __('admin/categories.create_btn') }}
                </button>
                <a href="{{ route('admin.categories.index') }}" class="btn-secondary">
                    {{ __('shared/ui.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
