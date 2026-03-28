<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('pages.landing.title') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center px-4">

    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 text-center">

            <h1 class="text-2xl font-semibold text-gray-900">{{ __('pages.landing.title') }}</h1>
            <p class="mt-2 text-sm text-gray-500">{{ __('pages.landing.subtitle') }}</p>

            <a
                href="{{ route('login') }}"
                class="mt-8 inline-block w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-xs
                       hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
                       transition-colors duration-150"
            >
                {{ __('pages.landing.login_link') }}
            </a>

        </div>
    </div>

</body>
</html>
