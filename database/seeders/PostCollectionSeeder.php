<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PostCollection;

class PostCollectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // PostCollection::factory(2)->create();
        $batchSize = 10; // Insert in chunks of 5000
        $totalRecords = 100;

        for ($i = 0; $i < ($totalRecords / $batchSize); $i++) {
            $posts = PostCollection::factory()->count($batchSize)->make()->each(function ($post) {
                $post->formatted_date = $post->formatted_date; // Ensure it's calculated
            })->toArray();
            PostCollection::insert($posts); // Bulk insert
        }
    }
}
