@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<section aria-labelledby="stats-heading">
    <h2 id="stats-heading" class="sr-only">Statistics</h2>

    <ul class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4" role="list">

        <li class="card p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total users</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-slate-100">{{ $usersCount }}</p>
                </div>
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-100 dark:bg-sky-900/30">
                    <i class="bi bi-people-fill text-sky-600 dark:text-sky-400 text-lg" aria-hidden="true"></i>
                </div>
            </div>
            <p class="mt-4 text-xs text-slate-400 dark:text-slate-500 flex items-center gap-1">
                <i class="bi bi-arrow-up-right text-green-500" aria-hidden="true"></i>
                Registered users
            </p>
        </li>

    </ul>
</section>
@endsection
