@extends('layouts.admin')

@section('title', __('admin/courses.create_title'))

@section('breadcrumbs')
    <x-breadcrumb :items="[
        ['label' => __('admin/dashboard.title'), 'url' => route('admin.dashboard.index')],
        ['label' => __('admin/courses.page_title'), 'url' => route('admin.courses.index')],
        ['label' => __('admin/courses.create_title')],
    ]" />
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="card p-8">
        <form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @include('admin.course.form')
            <x-form-actions
                :submit-label="__('admin/courses.create_btn')"
                submit-icon="play-circle"
                :cancel-route="route('admin.courses.index')"
            />
        </form>
    </div>
</div>
@endsection
