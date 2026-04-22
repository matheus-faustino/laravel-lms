<div x-data="{ open: false }" class="md:contents">

    <div x-show="open" x-transition.opacity @click="open = false"
        class="fixed inset-0 z-20 bg-black/60 backdrop-blur-sm md:hidden" aria-hidden="true"></div>

    <aside
        class="fixed inset-y-0 left-0 z-30 flex w-64 flex-col bg-slate-900 transition-transform duration-300 ease-in-out md:static md:translate-x-0"
        :class="open ? 'translate-x-0' : '-translate-x-full'" @keydown.escape.window="open = false">

        <div class="flex h-16 items-center gap-3 border-b border-slate-800 px-4">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-sky-600 shrink-0">
                <i class="bi bi-grid-fill text-white text-sm" aria-hidden="true"></i>
            </div>
            <span class="text-sm font-semibold text-white truncate">{{ config('app.name') }}</span>
        </div>

        {{ $slot }}

    </aside>

    <button @click="open = !open" type="button"
        class="fixed bottom-6 right-6 z-40 flex h-12 w-12 items-center justify-center rounded-full bg-sky-600 text-white shadow-lg shadow-sky-500/30 md:hidden focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2"
        :aria-label="open ? 'Hide menu' : 'Show menu'" :aria-expanded="open">
        <svg x-show="!open" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
            fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd"
                d="M2 4.75A.75.75 0 0 1 2.75 4h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 4.75ZM2 10a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 10Zm0 5.25a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1-.75-.75Z"
                clip-rule="evenodd" />
        </svg>
        <svg x-show="open" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
            fill="currentColor" aria-hidden="true">
            <path
                d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
        </svg>
    </button>

</div>
