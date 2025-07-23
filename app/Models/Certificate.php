<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Class Certificate
 * 
 * Represents a certificate issued to a student upon course completion.
 * Contains unique certificate code and management methods.
 *
 * @package App\Models
 * 
 * @property int $id
 * @property int $student_id Foreign key to users table
 * @property int $course_id Foreign key to courses table
 * @property string $certificate_code Unique certificate identifier
 * @property \Carbon\Carbon $issued_at Certificate issuance timestamp
 * @property bool $active Whether the certificate is active/valid
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read User $student
 * @property-read Course $course
 */
class Certificate extends Model
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
        'certificate_code',
        'issued_at',
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
            'issued_at' => 'datetime',
            'active' => 'boolean',
        ];
    }

    /**
     * Get the student who owns this certificate.
     *
     * @return BelongsTo<User, Certificate>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the course for this certificate.
     *
     * @return BelongsTo<Course, Certificate>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Generate a unique certificate code.
     *
     * @return string
     */
    public static function generateCode(): string
    {
        return 'CERT-' . strtoupper(Str::random(10));
    }

    /**
     * Revoke this certificate by marking it as inactive.
     *
     * @return void
     */
    public function revoke(): void
    {
        $this->active = false;
        $this->save();
    }

    /**
     * Check if the certificate is active/valid.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }
}
