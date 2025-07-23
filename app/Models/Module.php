<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Module
 * 
 * Represents a module within a course.
 * Contains lessons and maintains order sequence.
 *
 * @package App\Models
 * 
 * @property int $id
 * @property int $course_id Foreign key to courses table
 * @property string $title Module title
 * @property string $description Module description
 * @property int $order Module sequence order within the course
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read Course $course
 * @property-read \Illuminate\Database\Eloquent\Collection<Lesson> $lessons
 */
class Module extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'order',
    ];

    /**
     * Get the course that owns this module.
     *
     * @return BelongsTo<Course, Module>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get all lessons for this module ordered by their sequence.
     *
     * @return HasMany<Lesson>
     */
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    /**
     * Get the total duration of all lessons in this module.
     *
     * @return int Total duration in minutes
     */
    public function getTotalDuration(): int
    {
        return $this->lessons->sum('duration_minutes');
    }
}
