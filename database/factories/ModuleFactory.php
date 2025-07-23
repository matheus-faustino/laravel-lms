<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Module>
 */
class ModuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(3),
            'order' => fake()->numberBetween(1, 10),
        ];
    }

    /**
     * Indicate the module belongs to a specific course.
     */
    public function forCourse(Course $course): static
    {
        return $this->state(['course_id' => $course->id]);
    }

    /**
     * Set a specific order for the module.
     */
    public function withOrder(int $order): static
    {
        return $this->state(['order' => $order]);
    }
}
