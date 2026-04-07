@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<section aria-labelledby="stats-heading">
    <h2 id="stats-heading" class="sr-only">Estatísticas</h2>

    <ul class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3" role="list">
        <li class="rounded-xl border border-gray-200 bg-white p-6">
            <p class="text-sm font-medium text-gray-500">Total de usuários</p>
            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $usersCount }}</p>
        </li>
    </ul>
</section>
@endsection
