@extends('layouts.admin')

@section('title', __('admin/categories.create_title'))

@section('breadcrumbs')
    <x-breadcrumb :items="[
        ['label' => __('admin/dashboard.title'), 'url' => route('admin.dashboard.index')],
        ['label' => __('admin/categories.page_title'), 'url' => route('admin.categories.index')],
        ['label' => __('admin/categories.create_title')],
    ]" />
@endsection

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
