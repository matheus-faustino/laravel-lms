<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Course
 * 
 * Represents a course in the educational platform.
 * Contains modules and lessons, tracks student enrollments and certificates.
 *
 * @package App\Models
 * 
 * @property int $id
 * @property string $title Course title
 * @property string $description Course description
 * @property string|null $image Course cover image path
 * @property int $duration_hours Estimated duration in hours
 * @property bool $active Whether the course is active/published
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection<Module> $modules
 * @property-read \Illuminate\Database\Eloquent\Collection<Enrollment> $enrollments
 * @property-read \Illuminate\Database\Eloquent\Collection<Certificate> $certificates
 */
class Course extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'title',
        'description',
        'image',
        'duration_hours',
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
            'active' => 'boolean',
        ];
    }

    /**
     * Get all modules for this course ordered by their sequence.
     *
     * @return HasMany<Module>
     */
    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }

    /**
     * Get all enrollments for this course.
     *
     * @return HasMany<Enrollment>
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get all certificates issued for this course.
     *
     * @return HasMany<Certificate>
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Get the total number of lessons across all modules.
     *
     * @return int
     */
    public function getTotalLessonsCount(): int
    {
        return $this->modules->sum(function ($module) {
            return $module->lessons->count();
        });
    }

    /**
     * Get the progress percentage for a specific student.
     *
     * @param int $studentId The student's ID
     * @return float Progress percentage (0-100)
     */
    public function getProgressForStudent(int $studentId): float
    {
        $enrollment = $this->enrollments()->where('student_id', $studentId)->first();
        return $enrollment ? $enrollment->progress_percentage : 0;
    }
}
