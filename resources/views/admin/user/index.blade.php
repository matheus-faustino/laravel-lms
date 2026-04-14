@extends('layouts.admin')

@section('title', 'Users')

@section('content')

{{-- Success flash --}}
@if (session('success'))
    <div class="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
@endif

{{-- Action bar --}}
<div class="mb-4 flex items-center justify-end">
    <a href="{{ route('admin.users.create') }}"
        class="flex items-center gap-1.5 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-xs
               hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
               transition-colors duration-150">
        <i class="bi bi-person-plus"></i> New User
    </a>
</div>

{{-- Table --}}
<div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
            @forelse ($users as $user)
                <tr x-data="{
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
                                    window.alert(data.message ?? 'An error occurred.');
                                    this.deleting = false;
                                }
                            } catch {
                                window.alert('Network error. Please try again.');
                                this.deleting = false;
                            }
                        }
                    }">
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $user->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $user->email }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.users.edit', $user->id) }}"
                                class="inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 shadow-xs hover:bg-gray-50 transition-colors duration-150">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <button
                                type="button"
                                @click="confirmDelete()"
                                :disabled="deleting"
                                class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 shadow-xs hover:bg-red-100 transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                                <i class="bi bi-trash"></i>
                                <span x-text="deleting ? 'Deleting…' : 'Delete'"></span>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-400">
                        No users found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if ($users->hasPages())
    <div class="mt-4">
        {{ $users->links() }}
    </div>
@endif

@endsection
