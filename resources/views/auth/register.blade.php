<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('shared/auth.register_page_title') }}</title>
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
<body class="min-h-screen bg-slate-50 dark:bg-slate-950 transition-colors duration-200">

    {{-- Dark mode toggle --}}
    <div class="absolute top-4 right-4 z-10">
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

    <div class="flex min-h-screen">

        {{-- Left branding panel --}}
        <div class="hidden lg:flex lg:w-[480px] xl:w-[560px] flex-col justify-between bg-gradient-to-br from-sky-600 via-sky-700 to-sky-900 p-12 relative overflow-hidden shrink-0">
            <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
                <div class="absolute -top-32 -right-32 h-96 w-96 rounded-full bg-white/5"></div>
                <div class="absolute -bottom-16 -left-16 h-72 w-72 rounded-full bg-white/5"></div>
                <div class="absolute top-1/3 right-8 h-40 w-40 rounded-full bg-white/5"></div>
            </div>
            <div class="flex items-center gap-3 relative z-10">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/15 backdrop-blur-sm">
                    <i class="bi bi-grid-fill text-white" aria-hidden="true"></i>
                </div>
                <span class="text-base font-semibold text-white">{{ config('app.name') }}</span>
            </div>
            <div class="relative z-10">
                <p class="text-3xl font-bold text-white leading-snug">{{ __('shared/auth.register_welcome_heading') }}</p>
                <p class="mt-3 text-sky-200 text-sm leading-relaxed max-w-xs">
                    {{ __('shared/auth.register_welcome_description') }}
                </p>
                <div class="mt-8 flex items-center gap-3">
                    <div class="h-px flex-1 bg-white/20"></div>
                    <span class="text-xs text-sky-300">{{ __('shared/auth.secure_platform') }}</span>
                    <div class="h-px flex-1 bg-white/20"></div>
                </div>
            </div>
        </div>

        {{-- Right form panel --}}
        <div class="flex flex-1 flex-col items-center justify-center px-6 py-16">

            {{-- Mobile logo --}}
            <div class="flex items-center gap-2 mb-10 lg:hidden">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-sky-600">
                    <i class="bi bi-grid-fill text-white text-sm" aria-hidden="true"></i>
                </div>
                <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ config('app.name') }}</span>
            </div>

            <div class="w-full max-w-sm">
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ __('shared/auth.register_heading') }}</h1>
                    <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">{{ __('shared/auth.register_subtitle') }}</p>
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

                <form method="POST" action="{{ route('register.submit') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="name" class="form-label">{{ __('shared/ui.name_label') }}</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}"
                            required autofocus autocomplete="name"
                            placeholder="{{ __('shared/auth.register_name_placeholder') }}"
                            class="form-input @error('name') form-input-error @enderror">
                    </div>

                    <div>
                        <label for="email" class="form-label">{{ __('shared/ui.email_label') }}</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                            required autocomplete="email"
                            placeholder="{{ __('shared/auth.register_email_placeholder') }}"
                            class="form-input @error('email') form-input-error @enderror">
                    </div>

                    <div>
                        <label for="password" class="form-label">{{ __('shared/ui.password_label') }}</label>
                        <input id="password" type="password" name="password"
                            required autocomplete="new-password"
                            placeholder="{{ __('shared/ui.password_placeholder') }}"
                            class="form-input @error('password') form-input-error @enderror">
                    </div>

                    <div>
                        <label for="password_confirmation" class="form-label">{{ __('shared/ui.confirm_password_label') }}</label>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                            required autocomplete="new-password"
                            placeholder="{{ __('shared/ui.confirm_password_placeholder') }}"
                            class="form-input">
                    </div>

                    <button type="submit" class="btn-primary w-full justify-center">
                        {{ __('shared/auth.register_submit') }}
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-slate-500 dark:text-slate-400">
                    {{ __('shared/auth.register_have_account') }}
                    <a href="{{ route('login') }}" class="font-medium text-sky-600 hover:text-sky-500 dark:text-sky-400 dark:hover:text-sky-300 transition-colors">
                        {{ __('shared/auth.register_login_link') }}
                    </a>
                </p>
            </div>
        </div>
    </div>

</body>
</html>
