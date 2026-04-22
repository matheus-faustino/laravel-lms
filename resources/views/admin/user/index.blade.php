@extends('layouts.admin')

@section('title', 'Users')

@section('content')

@if (session('success'))
    <div class="alert-success mb-6">
        <i class="bi bi-check-circle-fill text-green-500 dark:text-green-400 shrink-0" aria-hidden="true"></i>
        <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
    </div>
@endif

<div class="mb-4 flex items-center justify-between">
    <a href="{{ route('admin.users.create') }}" class="btn-primary">
        <i class="bi bi-person-plus" aria-hidden="true"></i>
        New user
    </a>
</div>

<div class="card overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
        <thead>
            <tr class="bg-slate-50 dark:bg-slate-800/50">
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">User</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Email</th>
                <th scope="col" class="hidden px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 sm:table-cell">Created at</th>
                <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse ($users as $user)
                <tr
                    x-data="{
                        deleting: false,
                        deleteUrl: '{{ route('admin.users.delete', $user->id) }}',
                        async confirmDelete() {
                            if (!window.confirm('Delete user {{ addslashes($user->name) }}? This action cannot be undone.')) return;
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
                                    window.alert(data.message ?? 'An error has occurred.');
                                    this.deleting = false;
                                }
                            } catch {
                                window.alert('Network error. Try again.');
                                this.deleting = false;
                            }
                        }
                    }"
                    class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors duration-100"
                >
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 text-xs font-bold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <span class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">{{ $user->email }}</td>
                    <td class="hidden px-6 py-4 text-sm text-slate-500 dark:text-slate-400 sm:table-cell">
                        {{ $user->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-edit">
                                <i class="bi bi-pencil" aria-hidden="true"></i> Edit
                            </a>
                            <button type="button" @click="confirmDelete()" :disabled="deleting" class="btn-danger">
                                <i class="bi bi-trash" aria-hidden="true"></i>
                                <span x-text="deleting ? 'Deleting…' : 'Delete'"></span>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <i class="bi bi-people text-4xl text-slate-300 dark:text-slate-600" aria-hidden="true"></i>
                            <p class="text-sm font-medium text-slate-400 dark:text-slate-500">No users found</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($users->hasPages())
    <div class="mt-4">{{ $users->links() }}</div>
@endif

@endsection
