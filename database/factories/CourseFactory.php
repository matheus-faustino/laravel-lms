<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
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
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'thumbnail' => 'courses/thumbnails/' . fake()->uuid() . '.jpg',
            'banner' => 'courses/banners/' . fake()->uuid() . '.jpg',
            'status' => fake()->randomElement(\App\Enums\CourseStatusEnum::cases()),
        ];
    }

    public function draft(): static
    {
        return $this->state(['status' => \App\Enums\CourseStatusEnum::DRAFT]);
    }

    public function published(): static
    {
        return $this->state(['status' => \App\Enums\CourseStatusEnum::PUBLISHED]);
    }
}
