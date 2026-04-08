<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name')) — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-gray-50">

    <div class="flex min-h-screen">
        <x-sidebar>
            <nav class="flex-1 px-4 py-4" aria-label="Menu principal">
                <ul class="space-y-1" role="list">
                    <li>
                        <a
                            href="{{ route('admin.dashboard.index') }}"
                            class="{{ request()->routeIs('admin.dashboard.index') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} flex items-center rounded-lg px-3 py-2 text-sm transition-colors duration-150"
                            @if(request()->routeIs('admin.dashboard.index')) aria-current="page" @endif
                        >
                            Dashboard
                        </a>
                    </li>
                </ul>
            </nav>
        </x-sidebar>

        <div class="flex flex-1 flex-col">
            <x-navbar />
            <main class="flex-1 p-6">
                <h1 class="mb-6">@yield('title')</h1>
                @yield('content')
            </main>
        </div>

    </div>

</body>
</html>
