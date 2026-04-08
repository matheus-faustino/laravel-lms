<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('pages.reset_password.page_title') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center px-4">

    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">

            {{-- Header --}}
            <div class="mb-8 text-center">
                <h1 class="text-2xl font-semibold text-gray-900">{{ __('pages.reset_password.heading') }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ __('pages.reset_password.subtitle') }}</p>
            </div>

            {{-- Errors --}}
            @if ($errors->any())
                <div class="mb-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="text-sm text-red-600">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" class="space-y-5">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('pages.reset_password.email_label') }}
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email', request()->query('email')) }}"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="{{ __('pages.reset_password.email_placeholder') }}"
                        class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 shadow-xs
                               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                               @error('email') border-red-400 focus:ring-red-400 focus:border-red-400 @enderror"
                    >
                </div>

                {{-- New Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('pages.reset_password.new_password_label') }}
                    </label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        placeholder="{{ __('pages.reset_password.new_password_placeholder') }}"
                        class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 shadow-xs
                               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                               @error('password') border-red-400 focus:ring-red-400 focus:border-red-400 @enderror"
                    >
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('pages.reset_password.confirm_password_label') }}
                    </label>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="{{ __('pages.reset_password.confirm_password_placeholder') }}"
                        class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 shadow-xs
                               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    >
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-xs
                           hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
                           transition-colors duration-150 cursor-pointer"
                >
                    {{ __('pages.reset_password.submit') }}
                </button>
            </form>

        </div>
    </div>

</body>
</html>
