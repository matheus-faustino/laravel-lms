@extends('errors.layout')

@section('title', __('pages.errors.500_title'))

@section('content')
    <div class="flex flex-col items-center gap-4">
        <p class="text-8xl font-bold text-slate-200 dark:text-slate-800 select-none">500</p>
        <div>
            <h1 class="text-xl font-semibold text-slate-900 dark:text-slate-100">{{ __('pages.errors.500_message') }}</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">An internal error occurred. Please try again in a few moments.</p>
        </div>
        <a href="{{ url('/') }}" class="btn-primary mt-2">
            <i class="bi bi-arrow-left" aria-hidden="true"></i>
            {{ __('pages.errors.back_home') }}
        </a>
    </div>
@endsection
