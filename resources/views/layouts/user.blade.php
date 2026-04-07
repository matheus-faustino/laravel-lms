<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name')) — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gray-50">

    <div class="flex min-h-screen">
        <x-sidebar />

        <div class="flex flex-1 flex-col">
            <main class="flex-1 p-6">
                @yield('content')
            </main>
        </div>

    </div>

</body>

</html>