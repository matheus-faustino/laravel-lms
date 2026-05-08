@extends('layouts.admin')

@section('title', __('admin/enrollments.page_title'))

@section('breadcrumbs')
    <x-breadcrumb :items="[
        ['label' => __('admin/dashboard.title'), 'url' => route('admin.dashboard.index')],
        ['label' => __('admin/enrollments.page_title')],
    ]" />
@endsection

@section('content')

@if (session('success'))
    <div class="alert-success mb-6">
        <i class="bi bi-check-circle-fill text-green-500 dark:text-green-400 shrink-0" aria-hidden="true"></i>
        <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
    </div>
@endif

<div class="mb-4 flex items-center justify-between">
    <a href="{{ route('admin.enrollments.create') }}" class="btn-primary">
        <i class="bi bi-journal-plus" aria-hidden="true"></i>
        {{ __('admin/enrollments.new_enrollment_btn') }}
    </a>
</div>

<div class="card overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
        <thead>
            <tr class="bg-slate-50 dark:bg-slate-800/50">
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('admin/enrollments.table_user_col') }}</th>
                <th scope="col" class="hidden px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 sm:table-cell">{{ __('admin/enrollments.table_course_col') }}</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('admin/enrollments.table_status_col') }}</th>
                <th scope="col" class="hidden px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 sm:table-cell">{{ __('admin/enrollments.table_progress_col') }}</th>
                <th scope="col" class="hidden px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 sm:table-cell">{{ __('admin/enrollments.table_created_at_col') }}</th>
                <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('shared/ui.actions_label') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse ($enrollments as $enrollment)
                <tr
                    x-data="{
                        deleting: false,
                        deleteUrl: '{{ route('admin.enrollments.delete', $enrollment->id) }}',
                        async confirmDelete() {
                            if (!window.confirm('{{ __('admin/enrollments.delete_confirm', ['name' => addslashes($enrollment->user?->name ?? '')]) }}')) return;
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
                        }
                    }"
                    class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors duration-100"
                >
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ $enrollment->user?->name ?? '—' }}</span>
                            <span class="text-xs text-slate-400 dark:text-slate-500">{{ $enrollment->user?->email ?? '' }}</span>
                        </div>
                    </td>
                    <td class="hidden px-6 py-4 text-sm text-slate-700 dark:text-slate-300 sm:table-cell">
                        {{ $enrollment->course?->title ?? '—' }}
                    </td>
                    <td class="px-6 py-4">
                        @php $status = $enrollment->status->value @endphp
                        @if ($status === 'active')
                            <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                <i class="bi bi-circle-fill text-[6px]" aria-hidden="true"></i>
                                {{ __('admin/enrollments.status_active') }}
                            </span>
                        @elseif ($status === 'completed')
                            <span class="inline-flex items-center gap-1 rounded-full bg-sky-100 px-2.5 py-0.5 text-xs font-medium text-sky-700 dark:bg-sky-900/30 dark:text-sky-400">
                                <i class="bi bi-circle-fill text-[6px]" aria-hidden="true"></i>
                                {{ __('admin/enrollments.status_completed') }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                                <i class="bi bi-circle-fill text-[6px]" aria-hidden="true"></i>
                                {{ __('admin/enrollments.status_cancelled') }}
                            </span>
                        @endif
                    </td>
                    <td class="hidden px-6 py-4 sm:table-cell">
                        @php $p = $progressMap[$enrollment->id] @endphp
                        <div class="w-36">
                            <div class="mb-1.5 flex items-center justify-between">
                                <span class="text-xs font-medium text-slate-600 dark:text-slate-400">{{ $p['watched'] }}/{{ $p['total'] }}</span>
                                <span class="text-xs font-semibold text-sky-600 dark:text-sky-400">{{ $p['percentage'] }}%</span>
                            </div>
                            <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-slate-700">
                                <div class="h-full rounded-full bg-sky-500 dark:bg-sky-400 transition-all duration-500" style="width: {{ $p['percentage'] }}%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="hidden px-6 py-4 text-sm text-slate-500 dark:text-slate-400 sm:table-cell">
                        {{ $enrollment->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.enrollments.progress', $enrollment->id) }}" class="btn-edit">
                                <i class="bi bi-bar-chart" aria-hidden="true"></i> {{ __('admin/enrollments.progress_details_btn') }}
                            </a>
                            <a href="{{ route('admin.enrollments.edit', $enrollment->id) }}" class="btn-edit">
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
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <i class="bi bi-journal-check text-4xl text-slate-300 dark:text-slate-600" aria-hidden="true"></i>
                            <p class="text-sm font-medium text-slate-400 dark:text-slate-500">{{ __('admin/enrollments.no_enrollments_found') }}</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($enrollments->hasPages())
    <div class="mt-4">{{ $enrollments->links() }}</div>
@endif

@endsection
