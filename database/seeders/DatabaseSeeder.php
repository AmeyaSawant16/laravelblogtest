<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\PostCollection;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $alreadySeeded;
        if( Schema::hasTable('migrations') ){
            $alreadySeeded = DB::table('migrations')->where('migration', 'seeded_flag')->exists();
        }
        if(!$alreadySeeded){

            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

            // adding posts
            $batchSize = 10; // Insert in chunks of 5000
            $totalRecords = 100;

            for ($i = 0; $i < ($totalRecords / $batchSize); $i++) {
                $posts = PostCollection::factory()->count($batchSize)->make()->each(function ($post) {
                    $post->formatted_date = $post->formatted_date; // Ensure it's calculated
                })->toArray();
                PostCollection::insert($posts); // Bulk insert
            }

            DB::table('migrations')->insert([
                'migration' => 'seeded_flag',
                'batch' => 999 // A high number to avoid conflicts
            ]);

            PostCollection::updateCache();
        }
    }
}
