@extends('layouts.admin')

@section('title', __('admin/courses.page_title'))

@section('content')

@if (session('success'))
    <div class="alert-success mb-6">
        <i class="bi bi-check-circle-fill text-green-500 dark:text-green-400 shrink-0" aria-hidden="true"></i>
        <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
    </div>
@endif

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
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('admin/courses.table_title_col') }}</th>
                <th scope="col" class="hidden px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 sm:table-cell">{{ __('admin/courses.table_status_col') }}</th>
                <th scope="col" class="hidden px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 sm:table-cell">{{ __('admin/courses.table_created_at_col') }}</th>
                <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('shared/ui.actions_label') }}</th>
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
                        @if ($course->status->value === 'published')
                            <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                <i class="bi bi-circle-fill text-[6px]" aria-hidden="true"></i>
                                {{ __('admin/courses.status_published') }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                                <i class="bi bi-circle-fill text-[6px]" aria-hidden="true"></i>
                                {{ __('admin/courses.status_draft') }}
                            </span>
                        @endif
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
                        <div class="flex flex-col items-center gap-2">
                            <i class="bi bi-play-circle text-4xl text-slate-300 dark:text-slate-600" aria-hidden="true"></i>
                            <p class="text-sm font-medium text-slate-400 dark:text-slate-500">{{ __('admin/courses.no_courses_found') }}</p>
                        </div>
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
