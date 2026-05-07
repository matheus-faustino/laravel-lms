<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\LessonProgressServiceInterface;

class LessonProgressController extends Controller
{
    public function __construct(private LessonProgressServiceInterface $lessonProgressService) {}

    public function watch(int $courseId, int $lessonId)
    {
        $userId = auth()->id();

        $this->lessonProgressService->markAsWatched($userId, $lessonId);

        $progress = $this->lessonProgressService->getCourseProgress($userId, $courseId);

        return response()->json([
            'watched' => true,
            'progress' => $progress,
        ]);
    }
}
