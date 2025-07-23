<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Enrollment
 * 
 * Represents a student's enrollment in a course.
 * Tracks progress and completion status.
 *
 * @package App\Models
 * 
 * @property int $id
 * @property int $student_id Foreign key to users table
 * @property int $course_id Foreign key to courses table
 * @property \Carbon\Carbon $enrolled_at Enrollment timestamp
 * @property \Carbon\Carbon|null $completed_at Course completion timestamp
 * @property float $progress_percentage Progress percentage (0.00-100.00)
 * @property bool $active Whether the enrollment is active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read User $student
 * @property-read Course $course
 * @property-read \Illuminate\Database\Eloquent\Collection<Progress> $progress
 */
class Enrollment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'student_id',
        'course_id',
        'enrolled_at',
        'completed_at',
        'progress_percentage',
        'active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'enrolled_at' => 'datetime',
            'completed_at' => 'datetime',
            'progress_percentage' => 'float',
            'active' => 'boolean',
        ];
    }

    /**
     * Get the student who owns this enrollment.
     *
     * @return BelongsTo<User, Enrollment>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the course for this enrollment.
     *
     * @return BelongsTo<Course, Enrollment>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get all progress records for this enrollment.
     *
     * @return HasMany<Progress>
     */
    public function progress(): HasMany
    {
        return $this->hasMany(Progress::class);
    }

    /**
     * Update the progress percentage based on completed lessons.
     *
     * @return void
     */
    public function updateProgress(): void
    {
        $totalLessons = $this->course->getTotalLessonsCount();
        $completedLessons = $this->progress()->where('completed', true)->count();
        
        $this->progress_percentage = $totalLessons > 0 ? ($completedLessons / $totalLessons) * 100 : 0;
        
        if ($this->progress_percentage >= 100 && !$this->completed_at) {
            $this->completed_at = now();
        }
        
        $this->save();
    }

    /**
     * Check if the course has been completed.
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return !is_null($this->completed_at);
    }
}