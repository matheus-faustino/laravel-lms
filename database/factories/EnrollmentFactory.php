<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Enrollment>
 */
class EnrollmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $enrolledAt = fake()->dateTimeBetween('-6 months', 'now');
        $progressPercentage = fake()->randomFloat(2, 0, 100);

        return [
            'student_id' => User::factory()->state(['role' => User::ROLE_STUDENT]),
            'course_id' => Course::factory(),
            'enrolled_at' => $enrolledAt,
            'completed_at' => $progressPercentage >= 100
                ? fake()->dateTimeBetween($enrolledAt, 'now')
                : null,
            'progress_percentage' => $progressPercentage,
            'active' => fake()->boolean(90),
        ];
    }

    /**
     * Create a completed enrollment.
     */
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            $enrolledAt = $attributes['enrolled_at'] ?? fake()->dateTimeBetween('-6 months', '-1 month');

            return [
                'enrolled_at' => $enrolledAt,
                'completed_at' => fake()->dateTimeBetween($enrolledAt, 'now'),
                'progress_percentage' => 100.00,
            ];
        });
    }

    /**
     * Create an in-progress enrollment.
     */
    public function inProgress(): static
    {
        return $this->state([
            'completed_at' => null,
            'progress_percentage' => fake()->randomFloat(2, 10, 95),
            'active' => true,
        ]);
    }

    /**
     * Create an active enrollment.
     */
    public function active(): static
    {
        return $this->state([
            'active' => true,
        ]);
    }

    /**
     * Create an inactive enrollment.
     */
    public function inactive(): static
    {
        return $this->state([
            'active' => false,
        ]);
    }

    /**
     * Create an enrollment for a specific student and course.
     */
    public function forStudentAndCourse(User $student, Course $course): static
    {
        return $this->state([
            'student_id' => $student->id,
            'course_id' => $course->id,
        ]);
    }
}
