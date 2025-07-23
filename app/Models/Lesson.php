<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Lesson
 * 
 * Represents a lesson within a module.
 * Can be either video or text content type.
 *
 * @package App\Models
 * 
 * @property int $id
 * @property int $module_id Foreign key to modules table
 * @property string $title Lesson title
 * @property string $description Lesson description
 * @property string $type Lesson type (video|text)
 * @property string|null $content Text content for text lessons
 * @property string|null $video_url Video URL for video lessons
 * @property int $duration_minutes Estimated duration in minutes
 * @property int $order Lesson sequence order within the module
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read Module $module
 * @property-read \Illuminate\Database\Eloquent\Collection<Progress> $progress
 */
class Lesson extends Model
{
    use HasFactory;

    /** @var string Video lesson type constant */
    const TYPE_VIDEO = 'video';

    /** @var string Text lesson type constant */
    const TYPE_TEXT = 'text';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'module_id',
        'title',
        'description',
        'type',
        'content',
        'video_url',
        'duration_minutes',
        'order',
    ];

    /**
     * Get the module that owns this lesson.
     *
     * @return BelongsTo<Module, Lesson>
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Get all progress records for this lesson.
     *
     * @return HasMany<Progress>
     */
    public function progress(): HasMany
    {
        return $this->hasMany(Progress::class);
    }

    /**
     * Check if this lesson is a video type.
     *
     * @return bool
     */
    public function isVideo(): bool
    {
        return $this->type === self::TYPE_VIDEO;
    }

    /**
     * Check if this lesson is a text type.
     *
     * @return bool
     */
    public function isText(): bool
    {
        return $this->type === self::TYPE_TEXT;
    }

    /**
     * Check if this lesson has been completed by a specific student.
     *
     * @param int $studentId The student's ID
     * @return bool
     */
    public function isCompletedByStudent(int $studentId): bool
    {
        return $this->progress()
            ->whereHas('enrollment', function ($query) use ($studentId) {
                $query->where('student_id', $studentId);
            })
            ->where('completed', true)
            ->exists();
    }
}
