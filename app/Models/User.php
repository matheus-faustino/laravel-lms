<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class User
 * 
 * Represents a user in the educational platform system.
 * Users can have either admin or student roles.
 *
 * @package App\Models
 * 
 * @property int $id
 * @property string $name User's full name
 * @property string $email User's email address (unique)
 * @property \Carbon\Carbon|null $email_verified_at Email verification timestamp
 * @property string $password Hashed password
 * @property string|null $phone User's phone number
 * @property string $role User role (admin|student)
 * @property string|null $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection<Enrollment> $enrollments
 * @property-read \Illuminate\Database\Eloquent\Collection<Certificate> $certificates
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /** @var string Admin role constant */
    const ROLE_ADMIN = 'admin';

    /** @var string Student role constant */
    const ROLE_STUDENT = 'student';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all enrollments for this student.
     *
     * @return HasMany<Enrollment>
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    /**
     * Get all certificates for this student.
     *
     * @return HasMany<Certificate>
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class, 'student_id');
    }

    /**
     * Check if the user has admin role.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if the user has student role.
     *
     * @return bool
     */
    public function isStudent(): bool
    {
        return $this->role === self::ROLE_STUDENT;
    }
}
