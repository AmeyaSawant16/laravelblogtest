<?php

namespace App\Jobs;

use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\PostCollection;
use Illuminate\Support\Facades\DB;

class IndexPostsToElasticsearch implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // $data = DB::table('post_collections')->get();
        // echo $data;
        // dd(PostCollection::query());
        PostCollection::query()
            ->chunk(1000, function ($posts) {
                // echo $posts;
                foreach ($posts as $post) {
                    // echo $post.'<br/>\n';
                    $post->searchable(); // Scout indexing
                }
            });
    }
}
