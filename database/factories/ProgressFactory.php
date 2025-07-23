<?php

namespace Database\Factories;

use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Progress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Progress>
 */
class ProgressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $completed = fake()->boolean(60);

        return [
            'enrollment_id' => Enrollment::factory(),
            'lesson_id' => Lesson::factory(),
            'completed' => $completed,
            'completed_at' => $completed ? fake()->dateTimeBetween('-3 months', 'now') : null,
        ];
    }

    /**
     * Create a completed progress record.
     */
    public function completed(): static
    {
        return $this->state([
            'completed' => true,
            'completed_at' => fake()->dateTimeBetween('-3 months', 'now'),
        ]);
    }

    /**
     * Create an incomplete progress record.
     */
    public function incomplete(): static
    {
        return $this->state([
            'completed' => false,
            'completed_at' => null,
        ]);
    }

    /**
     * Create progress for a specific enrollment and lesson.
     */
    public function forEnrollmentAndLesson(Enrollment $enrollment, Lesson $lesson): static
    {
        return $this->state([
            'enrollment_id' => $enrollment->id,
            'lesson_id' => $lesson->id,
        ]);
    }
}
