<button
    onclick="(function(){var h=document.documentElement,d=h.classList.toggle('dark');localStorage.setItem('theme',d?'dark':'light');})()"
    type="button"
    class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-sky-500 shadow-sm"
    aria-label="{{ __('shared/ui.toggle_dark_mode') }}"
>
    <i class="bi bi-sun text-base dark:hidden" aria-hidden="true"></i>
    <i class="bi bi-moon text-base hidden dark:block" aria-hidden="true"></i>
</button>
