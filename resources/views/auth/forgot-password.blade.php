<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('shared/auth.forgot_password_page_title') }}</title>
    <script>
        (function () {
            var t = localStorage.getItem('theme');
            if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 dark:bg-slate-950 flex items-center justify-center px-4 transition-colors duration-200">

    {{-- Dark mode toggle --}}
    <div class="absolute top-4 right-4">
        <x-dark-mode-toggle />
    </div>

    <div class="w-full max-w-sm">

        <div class="flex items-center justify-center gap-2.5 mb-8">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-600 shadow-lg shadow-sky-500/30">
                <i class="bi bi-grid-fill text-white" aria-hidden="true"></i>
            </div>
            <span class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ config('app.name') }}</span>
        </div>

        <div class="card p-8">
            <div class="mb-6 text-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-100 dark:bg-sky-900/30 mx-auto mb-4">
                    <i class="bi bi-envelope-fill text-sky-600 dark:text-sky-400 text-xl" aria-hidden="true"></i>
                </div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">{{ __('shared/auth.forgot_password_heading') }}</h1>
                <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">{{ __('shared/auth.forgot_password_subtitle') }}</p>
            </div>

            <x-alert :message="session('status')" />

            <x-form-errors />

            <form method="POST" action="{{ route('password.request') }}" class="space-y-5">
                @csrf

                <x-form-input
                    name="email"
                    type="email"
                    :label="__('shared/ui.email_label')"
                    :placeholder="__('shared/auth.forgot_password_email_placeholder')"
                    :required="true"
                    autocomplete="email"
                    autofocus
                />

                <button type="submit" class="btn-primary w-full justify-center">
                    {{ __('shared/auth.forgot_password_submit') }}
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-slate-500 dark:text-slate-400">
                {{ __('shared/auth.forgot_password_remembered') }}
                <a href="{{ route('login') }}" class="font-medium text-sky-600 hover:text-sky-500 dark:text-sky-400 dark:hover:text-sky-300 transition-colors">
                    {{ __('shared/auth.forgot_password_back_to_login') }}
                </a>
            </p>
        </div>
    </div>

</body>
</html>
