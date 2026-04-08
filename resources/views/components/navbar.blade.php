<header class="border-b border-gray-200 bg-white">
    <nav class="flex h-16 items-center justify-end px-6" aria-label="Barra de navegação">

        <div class="relative" x-data="{ open: false }">
            <button
                @click="open = !open"
                @keydown.escape="open = false"
                type="button"
                class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 cursor-pointer"
                aria-haspopup="true"
                :aria-expanded="open"
            >
                <span class="font-medium">{{ Auth::user()->name }}</span>
                <svg
                    class="h-4 w-4 text-gray-400"
                    :class="{ 'rotate-180': open }"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                </svg>
            </button>

            <div
                x-show="open"
                @click.outside="open = false"
                class="absolute right-0 z-10 mt-1 w-48 origin-top-right rounded-lg border border-gray-100 bg-white py-1 shadow-lg"
                role="menu"
                aria-orientation="vertical"
            >
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="flex w-full items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 cursor-pointer"
                        role="menuitem"
                    >
                        <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M3 4.25A2.25 2.25 0 0 1 5.25 2h5.5A2.25 2.25 0 0 1 13 4.25v2a.75.75 0 0 1-1.5 0v-2a.75.75 0 0 0-.75-.75h-5.5a.75.75 0 0 0-.75.75v11.5c0 .414.336.75.75.75h5.5a.75.75 0 0 0 .75-.75v-2a.75.75 0 0 1 1.5 0v2A2.25 2.25 0 0 1 10.75 18h-5.5A2.25 2.25 0 0 1 3 15.75V4.25Z" clip-rule="evenodd" />
                            <path fill-rule="evenodd" d="M19 10a.75.75 0 0 0-.75-.75H8.704l1.048-1.068a.75.75 0 1 0-1.064-1.064l-2.25 2.25a.75.75 0 0 0 0 1.064l2.25 2.25a.75.75 0 1 0 1.064-1.064l-1.048-1.068h9.546A.75.75 0 0 0 19 10Z" clip-rule="evenodd" />
                        </svg>
                        Sair
                    </button>
                </form>
            </div>
        </div>

    </nav>
</header>
