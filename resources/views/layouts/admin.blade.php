<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name')) — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script>
        (function () {
            var t = localStorage.getItem('theme');
            if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @yield('head-extras')
</head>

<body class="min-h-screen bg-slate-100 dark:bg-slate-950 transition-colors duration-200">

    <div class="flex min-h-screen">
        <x-sidebar>
            <nav class="flex-1 px-3 py-4" aria-label="Main menu">
                <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('shared/ui.menu') }}</p>
                <ul class="space-y-1" role="list">
                    <li>
                        <a href="{{ route('admin.dashboard.index') }}"
                            class="{{ request()->routeIs('admin.dashboard.index') ? 'bg-sky-500/15 text-sky-400' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }} flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-150"
                            @if(request()->routeIs('admin.dashboard.index')) aria-current="page" @endif>
                            <i class="bi bi-speedometer2 text-base w-4 text-center"></i>
                            {{ __('admin/dashboard.title') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.users.index') }}"
                            class="{{ request()->routeIs('admin.users.*') ? 'bg-sky-500/15 text-sky-400' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }} flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-150"
                            @if(request()->routeIs('admin.users.*')) aria-current="page" @endif>
                            <i class="bi bi-people text-base w-4 text-center"></i>
                            {{ __('admin/users.page_title') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.categories.index') }}"
                            class="{{ request()->routeIs('admin.categories.*') ? 'bg-sky-500/15 text-sky-400' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }} flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-150"
                            @if(request()->routeIs('admin.categories.*')) aria-current="page" @endif>
                            <i class="bi bi-tag text-base w-4 text-center"></i>
                            {{ __('admin/categories.page_title') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.courses.index') }}"
                            class="{{ request()->routeIs('admin.courses.*') ? 'bg-sky-500/15 text-sky-400' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }} flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-150"
                            @if(request()->routeIs('admin.courses.*')) aria-current="page" @endif>
                            <i class="bi bi-play-circle text-base w-4 text-center"></i>
                            {{ __('admin/courses.page_title') }}
                        </a>
                    </li>
                </ul>
            </nav>
        </x-sidebar>

        <div class="flex flex-1 flex-col min-w-0">
            <x-navbar />
            <main class="flex-1 p-6 lg:p-8">
                <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100 mb-1">@yield('title')</h1>
                @yield('breadcrumbs')
                @yield('content')
            </main>
        </div>
    </div>

    @yield('scripts')

</body>

</html>
