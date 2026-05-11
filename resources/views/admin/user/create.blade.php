@extends('layouts.admin')

@section('title', __('admin/users.create_title'))

@section('breadcrumbs')
    <x-breadcrumb :items="[
        ['label' => __('admin/dashboard.title'), 'url' => route('admin.dashboard.index')],
        ['label' => __('admin/users.page_title'), 'url' => route('admin.users.index')],
        ['label' => __('admin/users.create_title')],
    ]" />
@endsection

@section('content')
<div class="max-w-lg">
    <div class="card p-8">
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
            @csrf
            @include('admin.user.form')
            <x-form-actions
                :submit-label="__('admin/users.create_btn')"
                submit-icon="person-plus"
                :cancel-route="route('admin.users.index')"
            />
        </form>
    </div>
</div>
@endsection
