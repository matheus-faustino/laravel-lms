@props(['items' => []])

<nav aria-label="breadcrumb" class="mt-3 mb-6 border-t border-slate-200 dark:border-slate-700/60 pt-3">
    <ol class="flex items-center flex-wrap gap-1 text-sm">
        @foreach($items as $item)
            @if(!$loop->last)
                <li>
                    <a href="{{ $item['url'] }}"
                       class="text-slate-500 hover:text-sky-600 dark:text-slate-400 dark:hover:text-sky-400 transition-colors">
                        {{ $item['label'] }}
                    </a>
                </li>
                <li aria-hidden="true" class="text-slate-300 dark:text-slate-600">
                    <i class="bi bi-chevron-right text-xs"></i>
                </li>
            @else
                <li aria-current="page" class="text-slate-900 dark:text-slate-100 font-medium">
                    {{ $item['label'] }}
                </li>
            @endif
        @endforeach
    </ol>
</nav>
