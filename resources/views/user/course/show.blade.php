@extends('layouts.user')

@section('title', $course->title)

@section('breadcrumbs')
    <x-breadcrumb :items="[
        ['label' => __('user/dashboard.title'), 'url' => route('user.dashboard.index')],
        ['label' => __('user/courses.page_title'), 'url' => route('user.courses.index')],
        ['label' => $course->title],
    ]" />
@endsection

@section('head-extras')
<script>
    var ytReady = false;
    function onYouTubeIframeAPIReady() {
        ytReady = true;
        document.dispatchEvent(new Event('youtube-ready'));
    }
</script>
<script src="https://www.youtube.com/iframe_api"></script>
<style>
    #youtube-player { width: 100% !important; height: 100% !important; }
    [x-cloak] { display: none !important; }
</style>
@endsection

@section('content')
<div class="card overflow-hidden flex min-h-[75vh] relative" x-data="coursePreview({{ $course->id }}, {{ json_encode($watchedLessonIds) }}, {{ json_encode($progress) }})" @keydown.escape.window="sidebarOpen = false">

    <div class="flex-1 overflow-y-auto min-w-0">

        <div
            class="sticky top-0 z-10 flex items-center justify-between px-4 py-2.5 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 lg:hidden">
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300 truncate">{{ $course->title }}</span>
            <button type="button"
                class="flex items-center gap-1.5 text-sm font-medium text-sky-600 dark:text-sky-400 shrink-0 ml-3"
                @click="sidebarOpen = true">
                <i class="bi bi-list-ul text-base"></i>
                {{ __('course/preview.course_content') }}
            </button>
        </div>

        <div x-show="!activeLesson">
            @if ($course->banner)
            <img src="{{ asset('storage/' . $course->banner) }}" alt="{{ $course->title }}"
                class="w-full max-h-56 sm:max-h-72 object-cover">
            @endif
            <div class="p-4 sm:p-6 lg:p-8">
                <h2 class="text-xl lg:text-2xl font-bold text-slate-900 dark:text-slate-100 mb-3">{{ $course->title }}</h2>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">{{ $course->description }}</p>
            </div>
        </div>

        <div x-show="activeLesson" class="p-4 sm:p-6 lg:p-8">
            <h2 class="text-lg lg:text-xl font-bold text-slate-900 dark:text-slate-100 mb-4 lg:mb-5"
                x-text="activeLesson?.title"></h2>

            <div x-show="activeLesson?.type === 'video'"
                class="aspect-video w-full bg-black rounded-xl lg:rounded-2xl overflow-hidden mb-4 lg:mb-6">
                <div id="youtube-player"></div>
            </div>

            <div x-show="activeLesson?.type === 'text'" class="prose dark:prose-invert max-w-none"
                x-html="activeLesson?.content"></div>
        </div>
    </div>

    <div x-show="sidebarOpen" x-transition:enter="transition-opacity duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="absolute inset-0 z-20 bg-black/50 lg:hidden" @click="sidebarOpen = false" x-cloak></div>

    <div class="absolute inset-y-0 right-0 z-30 w-80 shrink-0 border-l border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-y-auto transition-transform duration-300 translate-x-full lg:static lg:translate-x-0"
        :class="{ 'translate-x-0': sidebarOpen, 'translate-x-full': !sidebarOpen }">

        <div
            class="flex items-center justify-between px-4 py-3 border-b border-slate-200 dark:border-slate-800 lg:hidden">
            <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ __('course/preview.course_content') }}</span>
            <button type="button"
                class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"
                @click="sidebarOpen = false">
                <i class="bi bi-x-lg text-base"></i>
            </button>
        </div>

        <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-xs font-medium text-slate-600 dark:text-slate-400">{{ __('course/preview.your_progress') }}</span>
                <span class="text-xs font-semibold text-sky-600 dark:text-sky-400" x-text="progress.percentage + '%'"></span>
            </div>
            <div class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                <div class="h-full bg-sky-500 dark:bg-sky-400 rounded-full transition-all duration-500"
                    :style="'width: ' + progress.percentage + '%'"></div>
            </div>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">
                <span x-text="progress.watched"></span>/<span x-text="progress.total"></span> {{ __('course/preview.lessons_completed') }}
            </p>
        </div>

        @forelse ($course->modules as $module)
        <div class="border-b border-slate-100 dark:border-slate-800 last:border-b-0">

            <button type="button"
                class="w-full flex items-center justify-between gap-3 px-4 py-3.5 text-left hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-100"
                @click="toggleModule({{ $module->id }})">
                <span class="text-sm font-semibold text-slate-800 dark:text-slate-200 leading-snug">{{ $module->title }}</span>
                <i class="bi text-slate-400 shrink-0 transition-transform duration-200"
                    :class="openModules[{{ $module->id }}] ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
            </button>

            <div x-show="openModules[{{ $module->id }}]" x-transition:enter="transition-all duration-200 ease-out"
                x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                @forelse ($module->lessons->sortBy('order') as $lesson)
                <button type="button"
                    class="w-full flex items-start gap-3 px-4 py-3 text-left border-t border-slate-100 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition-colors duration-100"
                    :class="activeLesson?.id === {{ $lesson->id }}
                                ? 'bg-sky-50 dark:bg-sky-900/20'
                                : 'bg-slate-50/60 dark:bg-slate-800/30'" @click="selectLesson({{ json_encode([
                                'id'         => $lesson->id,
                                'title'      => $lesson->title,
                                'type'       => $lesson->type->value,
                                'youtube_id' => $lesson->youtube_id,
                                'content'    => $lesson->content,
                            ]) }})">
                    <i class="bi {{ $lesson->type->value === 'video' ? 'bi-play-circle-fill' : 'bi-file-text' }} mt-0.5 text-sm shrink-0"
                        :class="activeLesson?.id === {{ $lesson->id }}
                                    ? 'text-sky-500 dark:text-sky-400'
                                    : 'text-slate-400'"></i>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium leading-snug" :class="activeLesson?.id === {{ $lesson->id }}
                                        ? 'text-sky-700 dark:text-sky-300'
                                        : 'text-slate-700 dark:text-slate-300'">{{ $lesson->title }}</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                            {{ gmdate('H:i:s', $lesson->duration) }}
                        </p>
                    </div>
                    <i class="bi bi-check-circle-fill text-sm shrink-0 text-emerald-500 dark:text-emerald-400 mt-0.5"
                        :class="watchedLessonIds.includes({{ $lesson->id }}) ? '' : 'hidden'"></i>
                </button>
                @empty
                <p
                    class="px-4 py-3 text-xs text-slate-400 dark:text-slate-500 bg-slate-50/60 dark:bg-slate-800/30 border-t border-slate-100 dark:border-slate-800">
                    {{ __('admin/lessons.no_lessons_found') }}
                </p>
                @endforelse
            </div>

        </div>
        @empty
        <div class="py-16 px-4 text-center">
            <x-empty-state icon="collection" :message="__('admin/modules.no_modules_found')" />
        </div>
        @endforelse

    </div>

</div>
@endsection

@section('scripts')
<script>
    function coursePreview(courseId, watchedLessonIds, progress) {
    return {
        courseId: courseId,
        activeLesson: null,
        openModules: {},
        player: null,
        ytReady: false,
        pendingVideoId: null,
        sidebarOpen: false,
        watchedLessonIds: watchedLessonIds,
        progress: progress,
        csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

        init() {
            if (window.ytReady) {
                this.ytReady = true;
            } else {
                document.addEventListener('youtube-ready', () => {
                    this.ytReady = true;
                    if (this.pendingVideoId) {
                        this.createPlayer(this.pendingVideoId);
                        this.pendingVideoId = null;
                    }
                });
            }
        },

        selectLesson(lesson) {
            this.activeLesson = lesson;
            this.sidebarOpen = false;

            if (lesson.type === 'text') {
                this.markWatched(lesson.id);
                return;
            }

            if (this.ytReady) {
                this.$nextTick(() => {
                    if (this.player) {
                        this.player.loadVideoById(lesson.youtube_id);
                    } else {
                        this.createPlayer(lesson.youtube_id);
                    }
                });
            } else {
                this.pendingVideoId = lesson.youtube_id;
            }
        },

        markWatched(lessonId) {
            if (this.watchedLessonIds.includes(lessonId)) return;

            fetch(`/user/courses/${this.courseId}/lessons/${lessonId}/watch`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.watched) {
                    this.watchedLessonIds.push(lessonId);
                    this.progress = data.progress;
                }
            });
        },

        createPlayer(youtubeId) {
            const self = this;
            this.player = new YT.Player('youtube-player', {
                videoId: youtubeId,
                width: '100%',
                height: '100%',
                playerVars: { autoplay: 1, rel: 0 },
                events: {
                    onStateChange: function(event) {
                        if (event.data === YT.PlayerState.ENDED && self.activeLesson) {
                            self.markWatched(self.activeLesson.id);
                        }
                    },
                },
            });
        },

        toggleModule(moduleId) {
            this.openModules[moduleId] = !this.openModules[moduleId];
        },
    };
}
</script>
@endsection
