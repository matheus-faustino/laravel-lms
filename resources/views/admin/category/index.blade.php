@extends('layouts.admin')

@section('title', __('admin/categories.page_title'))

@section('breadcrumbs')
    <x-breadcrumb :items="[
        ['label' => __('admin/dashboard.title'), 'url' => route('admin.dashboard.index')],
        ['label' => __('admin/categories.page_title')],
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
    <a href="{{ route('admin.categories.create') }}" class="btn-primary">
        <i class="bi bi-tag" aria-hidden="true"></i>
        {{ __('admin/categories.new_category_btn') }}
    </a>
</div>

<div class="card overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
        <thead>
            <tr class="bg-slate-50 dark:bg-slate-800/50">
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('admin/categories.table_name_col') }}</th>
                <th scope="col" class="hidden px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 sm:table-cell">{{ __('admin/categories.table_parent_col') }}</th>
                <th scope="col" class="hidden px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 sm:table-cell">{{ __('admin/categories.table_created_at_col') }}</th>
                <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('shared/ui.actions_label') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse ($categories as $category)
                <tr
                    x-data="{
                        deleting: false,
                        deleteUrl: '{{ route('admin.categories.delete', $category->id) }}',
                        async confirmDelete() {
                            if (!window.confirm('{{ __('admin/categories.delete_confirm', ['name' => addslashes($category->name)]) }}')) return;
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
                    <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-slate-100">{{ $category->name }}</td>
                    <td class="hidden px-6 py-4 text-sm text-slate-500 dark:text-slate-400 sm:table-cell">
                        {{ $category->category?->name ?? '—' }}
                    </td>
                    <td class="hidden px-6 py-4 text-sm text-slate-500 dark:text-slate-400 sm:table-cell">
                        {{ $category->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn-edit">
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
                            <i class="bi bi-tag text-4xl text-slate-300 dark:text-slate-600" aria-hidden="true"></i>
                            <p class="text-sm font-medium text-slate-400 dark:text-slate-500">{{ __('admin/categories.no_categories_found') }}</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($categories->hasPages())
    <div class="mt-4">{{ $categories->links() }}</div>
@endif

@endsection
