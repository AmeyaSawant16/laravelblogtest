<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendLoginMail;
use App\Models\PostCollection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CheckSchedulePost implements ShouldQueue
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
        // Log::info('ExampleJob executed at ' . now());
        echo 'Date: '. Carbon::now();
        $posts = DB::table('post_collections')
            ->join('users', 'users.id', '=', 'post_collections.created_by')
            ->where('post_collections.published', '=', '0')
            ->where('post_collections.publish_datetime', '<=', Carbon::now())
            ->orderBy('post_collections.created_at')->get(['post_collections.id as post_id', 'users.email as email', 'post_collections.title as title']);

        foreach ($posts as $post) {
            $post_title_msg = 'Your Schedule Post Published Successfully. <br/> Post Title: '.$post->title;
            Mail::to($post->email)->send(new SendLoginMail($post_title_msg));

            DB::table('post_collections')
            ->where('id', $post->post_id)
            ->update(['published' => 1]);
        }
    }
}
