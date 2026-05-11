@extends('layouts.admin')

@section('title', __('admin/categories.edit_title'))

@section('breadcrumbs')
    <x-breadcrumb :items="[
        ['label' => __('admin/dashboard.title'), 'url' => route('admin.dashboard.index')],
        ['label' => __('admin/categories.page_title'), 'url' => route('admin.categories.index')],
        ['label' => __('admin/categories.edit_title')],
    ]" />
@endsection

@section('content')
<div class="max-w-lg">
    <div class="card p-8">
        <form method="POST" action="{{ route('admin.categories.update', $category->id) }}" class="space-y-5">
            @csrf
            @method('PUT')
            @include('admin.category.form')
            <x-form-actions
                :submit-label="__('shared/ui.save_changes')"
                submit-icon="floppy"
                :cancel-route="route('admin.categories.index')"
            />
        </form>
    </div>
</div>
@endsection
