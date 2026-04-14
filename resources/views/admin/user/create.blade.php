@extends('layouts.admin')

@section('title', 'Create User')

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
            @csrf
            @include('admin.user.form')
            <div class="flex items-center gap-3 pt-1">
                <button
                    type="submit"
                    class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-xs
                           hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
                           transition-colors duration-150 cursor-pointer"
                >
                    <i class="bi bi-person-plus mr-1"></i> Create User
                </button>
                <a href="{{ route('admin.users.index') }}"
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-xs
                           hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2
                           transition-colors duration-150">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
