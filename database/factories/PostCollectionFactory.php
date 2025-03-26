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
            'title' => fake()->sentence(6),
            'content' => fake()->paragraph(4),
            'excerpt' => fake()->text(100),
            'image' => null,
            'tags' => json_encode(fake()->words(5)),
            'meta_title' => fake()->sentence(8),
            'meta_description' => fake()->text(160),
            'publish_type' => 'now',
            'publish_datetime' => fake()->dateTimeBetween('-1 year', 'now'),
            'created_by' => 1, // Assuming user ID 1 as the creator
            'updated_by' => 1, // Assuming user ID 1 as the updater
            'published' => fake()->boolean(90), // 80% chance of being published
        ];
    }
}
