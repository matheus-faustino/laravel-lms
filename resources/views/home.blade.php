<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('pages.home.title') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center px-4">

    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 text-center">

            <h1 class="text-2xl font-semibold text-gray-900">
                {{ __('pages.home.greeting', ['name' => Auth::user()->name]) }}
            </h1>
            <p class="mt-2 text-sm text-gray-500">{{ __('pages.home.description') }}</p>

            <form method="POST" action="{{ route('logout') }}" class="mt-8">
                @csrf
                <button
                    type="submit"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-xs
                           hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
                           transition-colors duration-150 cursor-pointer"
                >
                    {{ __('pages.home.logout') }}
                </button>
            </form>

        </div>
    </div>

</body>
</html>
