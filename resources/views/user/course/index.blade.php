@extends('layouts.user')

@section('title', __('user/courses.page_title'))

@section('breadcrumbs')
    <x-breadcrumb :items="[
        ['label' => __('user/dashboard.title'), 'url' => route('user.dashboard.index')],
        ['label' => __('user/courses.page_title')],
    ]" />
@endsection

@section('content')

<div class="grid gap-4 sm:gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
    @forelse ($courses as $course)
        <a href="{{ route('user.courses.show', $course->id) }}" class="card overflow-hidden group hover:shadow-md hover:shadow-slate-200/50 dark:hover:shadow-black/20 transition-shadow duration-200">
            @if ($course->thumbnail)
                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}"
                    class="w-full h-40 sm:h-44 object-cover">
            @else
                <div class="w-full h-40 sm:h-44 bg-slate-200 dark:bg-slate-800 flex items-center justify-center">
                    <i class="bi bi-play-circle text-4xl text-slate-400 dark:text-slate-600" aria-hidden="true"></i>
                </div>
            @endif
            <div class="p-4">
                <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100 group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors duration-150">
                    {{ $course->title }}
                </h3>
                @if ($course->description)
                    <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
                        {{ $course->description }}
                    </p>
                @endif
            </div>
        </a>
    @empty
        <div class="col-span-full flex flex-col items-center gap-3 py-20 text-center">
            <i class="bi bi-collection-play text-5xl text-slate-300 dark:text-slate-600" aria-hidden="true"></i>
            <p class="text-sm font-medium text-slate-400 dark:text-slate-500">{{ __('user/courses.no_courses_found') }}</p>
        </div>
    @endforelse
</div>

@if ($courses->hasPages())
    <div class="mt-6">{{ $courses->links() }}</div>
@endif

@endsection
