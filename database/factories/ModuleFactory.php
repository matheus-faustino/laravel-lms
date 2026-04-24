<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Module>
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
            'title' => fake()->text(50),
            'order' => fake()->numberBetween(1, 10),
        ];
    }

    public function course(): static
    {
        return $this->state(fn() => [
            'course_id' => Course::factory(),
        ]);
    }
}
