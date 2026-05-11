@extends('layouts.admin')

@section('title', __('admin/categories.page_title'))

@section('breadcrumbs')
    <x-breadcrumb :items="[
        ['label' => __('admin/dashboard.title'), 'url' => route('admin.dashboard.index')],
        ['label' => __('admin/categories.page_title')],
    ]" />
@endsection

@section('content')

<x-alert :message="session('success')" />

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
                <x-table-header-cell>{{ __('admin/categories.table_name_col') }}</x-table-header-cell>
                <x-table-header-cell :hidden="true">{{ __('admin/categories.table_parent_col') }}</x-table-header-cell>
                <x-table-header-cell :hidden="true">{{ __('admin/categories.table_created_at_col') }}</x-table-header-cell>
                <x-table-header-cell align="right">{{ __('shared/ui.actions_label') }}</x-table-header-cell>
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
                        <x-empty-state icon="tag" :message="__('admin/categories.no_categories_found')" />
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
