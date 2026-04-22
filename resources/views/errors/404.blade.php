@extends('errors.layout')

@section('title', __('shared/errors.not_found_title'))

@section('content')
    <div class="flex flex-col items-center gap-4">
        <p class="text-8xl font-bold text-slate-200 dark:text-slate-800 select-none">404</p>
        <div>
            <h1 class="text-xl font-semibold text-slate-900 dark:text-slate-100">{{ __('shared/errors.not_found_message') }}</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('shared/errors.not_found_description') }}</p>
        </div>
        <a href="{{ url('/') }}" class="btn-primary mt-2">
            <i class="bi bi-arrow-left" aria-hidden="true"></i>
            {{ __('shared/errors.back_home') }}
        </a>
    </div>
@endsection
