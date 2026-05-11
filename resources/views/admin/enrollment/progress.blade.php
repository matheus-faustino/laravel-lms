@extends('layouts.admin')

@section('title', __('admin/enrollments.progress_details_title'))

@section('breadcrumbs')
    <x-breadcrumb :items="[
        ['label' => __('admin/dashboard.title'), 'url' => route('admin.dashboard.index')],
        ['label' => __('admin/enrollments.page_title'), 'url' => route('admin.enrollments.index')],
        ['label' => __('admin/enrollments.progress_details_title')],
    ]" />
@endsection

@section('content')

<div class="mb-6 card p-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-col gap-1">
            <h2 class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ $enrollment->user?->name ?? '—' }}</h2>
            <span class="text-sm text-slate-400 dark:text-slate-500">{{ $enrollment->user?->email ?? '' }}</span>
            <span class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ $enrollment->course?->title ?? '—' }}</span>
        </div>
        <div class="flex flex-col items-end gap-2">
            @php
                $status = $enrollment->status->value;
                $statusLabels = [
                    'active'    => __('admin/enrollments.status_active'),
                    'completed' => __('admin/enrollments.status_completed'),
                    'cancelled' => __('admin/enrollments.status_cancelled'),
                ];
            @endphp
            <x-status-badge :status="$status" :label="$statusLabels[$status] ?? $status" />
            <div class="w-48">
                <x-progress-bar :percentage="$progress['percentage']" :watched="$progress['watched']" :total="$progress['total']" />
            </div>
        </div>
    </div>
</div>

@if ($modules->isEmpty() || $progress['total'] === 0)
    <div class="card p-12 text-center">
        <x-empty-state icon="journal-x" :message="__('admin/enrollments.progress_no_lessons')" />
    </div>
@else
    <div class="flex flex-col gap-4">
        @foreach ($modules as $module)
            @if ($module->lessons->isNotEmpty())
                <div class="card overflow-hidden">
                    <div class="border-b border-slate-200 bg-slate-50 px-6 py-3 dark:border-slate-800 dark:bg-slate-800/50">
                        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $module->title }}</h3>
                    </div>
                    <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($module->lessons as $lesson)
                            @php $watched = in_array($lesson->id, $watchedLessonIds) @endphp
                            <li class="flex items-center gap-4 px-6 py-3.5">
                                @if ($watched)
                                    <i class="bi bi-check-circle-fill shrink-0 text-lg text-green-500 dark:text-green-400" aria-hidden="true"></i>
                                @else
                                    <i class="bi bi-circle shrink-0 text-lg text-slate-300 dark:text-slate-600" aria-hidden="true"></i>
                                @endif
                                <div class="flex min-w-0 flex-1 items-center justify-between gap-3">
                                    <span class="truncate text-sm text-slate-700 dark:text-slate-300">{{ $lesson->title }}</span>
                                    <div class="flex shrink-0 items-center gap-2">
                                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-xs font-medium
                                            {{ $lesson->type->value === 'video'
                                                ? 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400'
                                                : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">
                                            {{ strtoupper($lesson->type->value) }}
                                        </span>
                                        <span class="text-xs {{ $watched ? 'text-green-600 dark:text-green-400' : 'text-slate-400 dark:text-slate-500' }}">
                                            {{ $watched ? __('admin/enrollments.lesson_completed_label') : __('admin/enrollments.lesson_pending_label') }}
                                        </span>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endforeach
    </div>
@endif

@endsection
