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
        $batchSize = 5000; // Insert in chunks of 5000
        $totalRecords = 200000;

        for ($i = 0; $i < ($totalRecords / $batchSize); $i++) {
            $posts = PostCollection::factory()->count($batchSize)->make()->toArray();
            PostCollection::insert($posts); // Bulk insert
        }
    }
}
