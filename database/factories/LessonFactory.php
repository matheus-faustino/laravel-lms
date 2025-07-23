<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lesson>
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
        $type = fake()->randomElement([Lesson::TYPE_VIDEO, Lesson::TYPE_TEXT]);

        return [
            'module_id' => Module::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(2),
            'type' => $type,
            'content' => $type === Lesson::TYPE_TEXT ? fake()->paragraphs(5, true) : null,
            'video_url' => $type === Lesson::TYPE_VIDEO ? fake()->url() : null,
            'duration_minutes' => fake()->numberBetween(5, 60),
            'order' => fake()->numberBetween(1, 20),
        ];
    }

    /**
     * Create a video lesson.
     */
    public function video(): static
    {
        return $this->state([
            'type' => Lesson::TYPE_VIDEO,
            'video_url' => 'https://www.youtube.com/watch?v=' . fake()->regexify('[A-Za-z0-9_-]{11}'),
            'content' => null,
        ]);
    }

    /**
     * Create a text lesson.
     */
    public function text(): static
    {
        return $this->state([
            'type' => Lesson::TYPE_TEXT,
            'content' => fake()->paragraphs(5, true),
            'video_url' => null,
        ]);
    }

    /**
     * Indicate the lesson belongs to a specific module.
     */
    public function forModule(Module $module): static
    {
        return $this->state(['module_id' => $module->id]);
    }

    /**
     * Set a specific order for the lesson.
     */
    public function withOrder(int $order): static
    {
        return $this->state(['order' => $order]);
    }
}
