@extends('layouts.admin')

@section('title', __('admin/courses.edit_title'))

@section('breadcrumbs')
    <x-breadcrumb :items="[
        ['label' => __('admin/dashboard.title'), 'url' => route('admin.dashboard.index')],
        ['label' => __('admin/courses.page_title'), 'url' => route('admin.courses.index')],
        ['label' => __('admin/courses.edit_title')],
    ]" />
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="card p-8">
        <form method="POST" action="{{ route('admin.courses.update', $course->id) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')
            @include('admin.course.form')
            <x-form-actions
                :submit-label="__('shared/ui.save_changes')"
                submit-icon="floppy"
                :cancel-route="route('admin.courses.index')"
            />
        </form>
    </div>
</div>
@endsection
