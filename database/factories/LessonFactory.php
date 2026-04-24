<?php

namespace Database\Factories;

use App\Enums\LessonTypeEnum;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lesson>
 */
class LessonFactory extends Factory
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
            'description' => fake()->text(),
            'type' => fake()->randomElement(LessonTypeEnum::cases()),
            'duration' => fake()->numberBetween(300, 600),
            'order' => fake()->numberBetween(1, 10),
            'content' => fake()->text(),
            'youtube_id' => fake()->text(50),
        ];
    }

    public function text(): static
    {
        return $this->state(fn() => [
            'type'       => LessonTypeEnum::TEXT,
            'youtube_id' => null,
        ]);
    }

    public function video(): static
    {
        return $this->state(fn() => [
            'type'    => LessonTypeEnum::VIDEO,
            'content' => null,
        ]);
    }

    public function module(): static
    {
        return $this->state(fn() => [
            'module_id' => Module::factory()->course(),
        ]);
    }
}
