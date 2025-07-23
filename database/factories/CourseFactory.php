<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(5),
            'image' => fake()->imageUrl(640, 480, 'education'),
            'duration_hours' => fake()->numberBetween(10, 120),
            'active' => fake()->boolean(80),
        ];
    }

    /**
     * Indicate that the course is active.
     */
    public function active(): static
    {
        return $this->state(['active' => true]);
    }

    /**
     * Indicate that the course is inactive.
     */
    public function inactive(): static
    {
        return $this->state(['active' => false]);
    }
}
