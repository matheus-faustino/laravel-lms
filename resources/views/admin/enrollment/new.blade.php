@extends('layouts.admin')

@section('title', __('admin/enrollments.create_title'))

@section('breadcrumbs')
    <x-breadcrumb :items="[
        ['label' => __('admin/dashboard.title'), 'url' => route('admin.dashboard.index')],
        ['label' => __('admin/enrollments.page_title'), 'url' => route('admin.enrollments.index')],
        ['label' => __('admin/enrollments.create_title')],
    ]" />
@endsection

@section('head-extras')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <style>
        .select2-container--default .select2-selection--single {
            height: auto;
            border: 1px solid rgb(203 213 225);
            border-radius: 0.75rem;
            background: white;
            padding: 0.625rem 0.875rem;
            outline: none;
            box-shadow: none;
        }
        html.dark .select2-container--default .select2-selection--single {
            border-color: rgb(71 85 105);
            background: rgb(30 41 59);
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding: 0;
            line-height: inherit;
            color: inherit;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            display: none;
        }
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: rgb(148 163 184);
        }
        .select2-dropdown {
            border-radius: 0.75rem;
            border: 1px solid rgb(203 213 225);
            background: white;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            overflow: hidden;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: rgb(14 165 233);
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid rgb(203 213 225);
            border-radius: 0.5rem;
            padding: 0.375rem 0.625rem;
            font-size: 0.875rem;
            outline: none;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            border-color: rgb(14 165 233);
            box-shadow: 0 0 0 2px rgb(14 165 233 / 0.3);
        }
        html.dark .select2-dropdown {
            background: rgb(30 41 59);
            border-color: rgb(71 85 105);
        }
        html.dark .select2-container--default .select2-results__option {
            color: rgb(226 232 240);
            background: rgb(30 41 59);
        }
        html.dark .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: rgb(14 165 233);
            color: white;
        }
        html.dark .select2-container--default .select2-search--dropdown .select2-search__field {
            background: rgb(15 23 42);
            border-color: rgb(71 85 105);
            color: rgb(226 232 240);
        }
        html.dark .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: rgb(100 116 139);
        }
    </style>
@endsection

@section('content')
<div class="max-w-lg">
    <div class="card p-8">
        <form method="POST" action="{{ route('admin.enrollments.store') }}" class="space-y-5">
            @csrf
            @include('admin.enrollment.form')
            <x-form-actions
                :submit-label="__('admin/enrollments.create_btn')"
                submit-icon="journal-plus"
                :cancel-route="route('admin.enrollments.index')"
            />
        </form>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function () {
            var isDark = document.documentElement.classList.contains('dark');

            $('#user_id').select2({
                placeholder: '{{ __('admin/enrollments.user_placeholder') }}',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#user_id').closest('.card'),
            });

            $('#course_id').select2({
                placeholder: '{{ __('admin/enrollments.course_placeholder') }}',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#course_id').closest('.card'),
            });
        });
    </script>
@endsection
