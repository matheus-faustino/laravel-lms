<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('shared/auth.reset_password_page_title') }}</title>
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
        <button
            onclick="(function(){var h=document.documentElement,d=h.classList.toggle('dark');localStorage.setItem('theme',d?'dark':'light');})()"
            type="button"
            class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-sky-500 shadow-sm"
            aria-label="{{ __('shared/ui.toggle_dark_mode') }}"
        >
            <i class="bi bi-sun text-base dark:hidden" aria-hidden="true"></i>
            <i class="bi bi-moon text-base hidden dark:block" aria-hidden="true"></i>
        </button>
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
                    <i class="bi bi-shield-lock-fill text-sky-600 dark:text-sky-400 text-xl" aria-hidden="true"></i>
                </div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">{{ __('shared/auth.reset_password_heading') }}</h1>
                <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">{{ __('shared/auth.reset_password_subtitle') }}</p>
            </div>

            @if ($errors->any())
                <div class="alert-error mb-6">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="text-sm text-red-600 dark:text-red-400">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" class="form-label">{{ __('shared/ui.email_label') }}</label>
                    <input id="email" type="email" name="email"
                        value="{{ old('email', request()->query('email')) }}"
                        required autofocus autocomplete="email"
                        placeholder="{{ __('shared/auth.reset_password_email_placeholder') }}"
                        class="form-input @error('email') form-input-error @enderror">
                </div>

                <div>
                    <label for="password" class="form-label">{{ __('shared/auth.reset_password_new_password_label') }}</label>
                    <input id="password" type="password" name="password"
                        required autocomplete="new-password"
                        placeholder="{{ __('shared/auth.reset_password_new_password_placeholder') }}"
                        class="form-input @error('password') form-input-error @enderror">
                </div>

                <div>
                    <label for="password_confirmation" class="form-label">{{ __('shared/auth.reset_password_confirm_label') }}</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                        required autocomplete="new-password"
                        placeholder="{{ __('shared/auth.reset_password_confirm_placeholder') }}"
                        class="form-input">
                </div>

                <button type="submit" class="btn-primary w-full justify-center">
                    {{ __('shared/auth.reset_password_submit') }}
                </button>
            </form>
        </div>
    </div>

</body>
</html>
