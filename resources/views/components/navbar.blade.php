<header class="border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 transition-colors duration-200">
    <nav class="flex h-16 items-center justify-end gap-3 px-6" aria-label="Navigation">

        {{-- Dark mode toggle --}}
        <button
            onclick="(function(){var h=document.documentElement,d=h.classList.toggle('dark');localStorage.setItem('theme',d?'dark':'light');})()"
            type="button"
            class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-sky-500"
            aria-label="Toggle dark mode"
        >
            <i class="bi bi-sun text-base dark:hidden" aria-hidden="true"></i>
            <i class="bi bi-moon text-base hidden dark:block" aria-hidden="true"></i>
        </button>

        {{-- Divider --}}
        <div class="h-6 w-px bg-slate-200 dark:bg-slate-700" aria-hidden="true"></div>

        {{-- User menu --}}
        <div class="relative" x-data="{ open: false }">
            <button
                @click="open = !open"
                @keydown.escape="open = false"
                type="button"
                class="flex items-center gap-2.5 rounded-lg px-2.5 py-1.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 cursor-pointer transition-colors duration-150"
                aria-haspopup="true"
                :aria-expanded="open"
            >
                <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-sky-600 text-white text-xs font-bold" aria-hidden="true">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <span class="font-medium hidden sm:block max-w-32 truncate">{{ Auth::user()->name }}</span>
                <svg class="h-4 w-4 text-slate-400 transition-transform duration-150 shrink-0"
                    :class="{ 'rotate-180': open }"
                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                </svg>
            </button>

            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                @click.outside="open = false"
                class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-lg shadow-black/5 dark:shadow-black/30"
                role="menu"
                aria-orientation="vertical"
            >
                <div class="border-b border-slate-100 dark:border-slate-800 px-4 py-3">
                    <p class="mt-0.5 text-sm font-semibold text-slate-900 dark:text-slate-100 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 truncate">{{ Auth::user()->email }}</p>
                </div>
                <div class="p-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 transition-colors duration-150 cursor-pointer"
                            role="menuitem"
                        >
                            <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M3 4.25A2.25 2.25 0 0 1 5.25 2h5.5A2.25 2.25 0 0 1 13 4.25v2a.75.75 0 0 1-1.5 0v-2a.75.75 0 0 0-.75-.75h-5.5a.75.75 0 0 0-.75.75v11.5c0 .414.336.75.75.75h5.5a.75.75 0 0 0 .75-.75v-2a.75.75 0 0 1 1.5 0v2A2.25 2.25 0 0 1 10.75 18h-5.5A2.25 2.25 0 0 1 3 15.75V4.25Z" clip-rule="evenodd" />
                                <path fill-rule="evenodd" d="M19 10a.75.75 0 0 0-.75-.75H8.704l1.048-1.068a.75.75 0 1 0-1.064-1.064l-2.25 2.25a.75.75 0 0 0 0 1.064l2.25 2.25a.75.75 0 1 0 1.064-1.064l-1.048-1.068h9.546A.75.75 0 0 0 19 10Z" clip-rule="evenodd" />
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </nav>
</header>
