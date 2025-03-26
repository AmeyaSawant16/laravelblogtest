<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PostCollection>
 */
class PostCollectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(6),
            'content' => $this->faker->paragraph(4),
            'excerpt' => $this->faker->text(100),
            'image' => '',
            'tags' => implode(',', $this->faker->words(5)),
            'meta_title' => $this->faker->sentence(8),
            'meta_description' => $this->faker->text(160),
            'publish_type' => $this->faker->randomElement(['scheduled', 'immediate']),
            'publish_datetime' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'created_by' => 1, // Assuming user ID 1 as the creator
            'updated_by' => 1, // Assuming user ID 1 as the updater
            'published' => $this->faker->boolean(80), // 80% chance of being published
        ];
    }
}
