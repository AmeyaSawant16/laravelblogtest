<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

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
        $user = User::where('email', 'test@example.com')->get();
        return [
            'title' => fake()->sentence(6),
            'content' => fake()->paragraph(4),
            'excerpt' => fake()->text(100),
            'image' => 'post_cover_image_1400x600.svg',
            'tags' => json_encode(fake()->words(5)),
            'meta_title' => fake()->sentence(8),
            'meta_description' => fake()->text(160),
            'publish_type' => 'now',
            'publish_datetime' => fake()->dateTimeBetween('-1 year', 'now'),
            'created_by' => $user[0]->id, // Assuming user ID 1 as the creator
            'updated_by' => $user[0]->id, // Assuming user ID 1 as the updater
            'published' => fake()->boolean(90), // 80% chance of being published
        ];
    }
}
