@extends('layouts.admin')

@section('title', __('admin/courses.page_title'))

@section('breadcrumbs')
    <x-breadcrumb :items="[
        ['label' => __('admin/dashboard.title'), 'url' => route('admin.dashboard.index')],
        ['label' => __('admin/courses.page_title')],
    ]" />
@endsection

@section('content')

<x-alert :message="session('success')" />

<div class="mb-4 flex items-center justify-between">
    <a href="{{ route('admin.courses.create') }}" class="btn-primary">
        <i class="bi bi-play-circle" aria-hidden="true"></i>
        {{ __('admin/courses.new_course_btn') }}
    </a>
</div>

<div class="card overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
        <thead>
            <tr class="bg-slate-50 dark:bg-slate-800/50">
                <x-table-header-cell>{{ __('admin/courses.table_title_col') }}</x-table-header-cell>
                <x-table-header-cell :hidden="true">{{ __('admin/courses.table_status_col') }}</x-table-header-cell>
                <x-table-header-cell :hidden="true">{{ __('admin/courses.table_created_at_col') }}</x-table-header-cell>
                <x-table-header-cell align="right">{{ __('shared/ui.actions_label') }}</x-table-header-cell>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse ($courses as $course)
                <tr
                    x-data="{
                        deleting: false,
                        publishing: false,
                        deleteUrl: '{{ route('admin.courses.delete', $course->id) }}',
                        publishUrl: '{{ route('admin.courses.publish', $course->id) }}',
                        async confirmDelete() {
                            if (!window.confirm('{{ __('admin/courses.delete_confirm', ['title' => addslashes($course->title)]) }}')) return;
                            this.deleting = true;
                            try {
                                const response = await fetch(this.deleteUrl, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content'),
                                        'Accept': 'application/json',
                                    },
                                });
                                if (response.ok) {
                                    this.$el.remove();
                                } else {
                                    const data = await response.json();
                                    window.alert(data.message ?? '{{ __('shared/errors.generic_error') }}');
                                    this.deleting = false;
                                }
                            } catch {
                                window.alert('{{ __('shared/errors.network_error') }}');
                                this.deleting = false;
                            }
                        },
                        async confirmPublish() {
                            this.publishing = true;
                            try {
                                const response = await fetch(this.publishUrl, {
                                    method: 'PATCH',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content'),
                                        'Accept': 'application/json',
                                    },
                                });
                                if (response.ok) {
                                    window.location.reload();
                                } else {
                                    const data = await response.json();
                                    window.alert(data.message ?? '{{ __('shared/errors.generic_error') }}');
                                    this.publishing = false;
                                }
                            } catch {
                                window.alert('{{ __('shared/errors.network_error') }}');
                                this.publishing = false;
                            }
                        }
                    }"
                    class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors duration-100"
                >
                    <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-slate-100">{{ $course->title }}</td>
                    <td class="hidden px-6 py-4 sm:table-cell">
                        <x-status-badge
                            :status="$course->status->value"
                            :label="$course->status->value === 'published' ? __('admin/courses.status_published') : __('admin/courses.status_draft')"
                        />
                    </td>
                    <td class="hidden px-6 py-4 text-sm text-slate-500 dark:text-slate-400 sm:table-cell">
                        {{ $course->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if ($course->status->value === 'draft')
                                <button type="button" @click="confirmPublish()" :disabled="publishing" class="btn-edit">
                                    <i class="bi bi-send" aria-hidden="true"></i>
                                    <span x-text="publishing ? '{{ __('admin/courses.publishing') }}' : '{{ __('admin/courses.publish_btn') }}'"></span>
                                </button>
                            @endif
                            <a href="{{ route('admin.modules.index', $course->id) }}" class="btn-edit">
                                <i class="bi bi-collection" aria-hidden="true"></i> {{ __('admin/modules.page_title') }}
                            </a>
                            <a href="{{ route('admin.courses.preview', $course->id) }}" class="btn-edit">
                                <i class="bi bi-eye" aria-hidden="true"></i> {{ __('admin/courses.preview_btn') }}
                            </a>
                            <a href="{{ route('admin.courses.edit', $course->id) }}" class="btn-edit">
                                <i class="bi bi-pencil" aria-hidden="true"></i> {{ __('shared/ui.edit') }}
                            </a>
                            <button type="button" @click="confirmDelete()" :disabled="deleting" class="btn-danger">
                                <i class="bi bi-trash" aria-hidden="true"></i>
                                <span x-text="deleting ? '{{ __('shared/ui.deleting') }}' : '{{ __('shared/ui.delete') }}'"></span>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-16 text-center">
                        <x-empty-state icon="play-circle" :message="__('admin/courses.no_courses_found')" />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($courses->hasPages())
    <div class="mt-4">{{ $courses->links() }}</div>
@endif

@endsection
