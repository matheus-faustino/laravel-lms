<?php

namespace Database\Factories;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Certificate>
 */
class CertificateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => User::factory()->state(['role' => User::ROLE_STUDENT]),
            'course_id' => Course::factory(),
            'certificate_code' => Certificate::generateCode(),
            'issued_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'active' => fake()->boolean(95),
        ];
    }

    /**
     * Create an active certificate.
     */
    public function active(): static
    {
        return $this->state(['active' => true]);
    }

    /**
     * Create a revoked certificate.
     */
    public function revoked(): static
    {
        return $this->state(['active' => false]);
    }

    /**
     * Create a certificate for a specific student and course.
     */
    public function forStudentAndCourse(User $student, Course $course): static
    {
        return $this->state([
            'student_id' => $student->id,
            'course_id' => $course->id,
        ]);
    }
}
