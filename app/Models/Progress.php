<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Progress
 * 
 * Represents a student's progress on a specific lesson.
 * Tracks completion status and timestamp.
 *
 * @package App\Models
 * 
 * @property int $id
 * @property int $enrollment_id Foreign key to enrollments table
 * @property int $lesson_id Foreign key to lessons table
 * @property bool $completed Whether the lesson is completed
 * @property \Carbon\Carbon|null $completed_at Lesson completion timestamp
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read Enrollment $enrollment
 * @property-read Lesson $lesson
 */
class Progress extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'enrollment_id',
        'lesson_id',
        'completed',
        'completed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'completed' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Get the enrollment that owns this progress record.
     *
     * @return BelongsTo<Enrollment, Progress>
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Get the lesson for this progress record.
     *
     * @return BelongsTo<Lesson, Progress>
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Mark this lesson as completed and update enrollment progress.
     *
     * @return void
     */
    public function markCompleted(): void
    {
        $this->completed = true;
        $this->completed_at = now();
        $this->save();

        // Update enrollment progress
        $this->enrollment->updateProgress();
    }
}
