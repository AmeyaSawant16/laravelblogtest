<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ElasticsearchService;
use App\Models\PostCollection;

class IndexData extends Command
{
    protected $signature = 'elasticsearch:posts';
    protected $description = 'Index all posts into Elasticsearch';

    protected $elasticsearch;
    protected $client;

    public function __construct(ElasticsearchService $elasticsearch)
    {
        parent::__construct();
        $this->client = $elasticsearch->getClient();
        $this->elasticsearch = $elasticsearch;
    }

    public function handle()
    {

        $this->info("Starting bulk indexing...");

        $this->elasticsearch->createPostIndex();

        // Process data in chunks to avoid memory issues
        PostCollection::select('id', 'title', 'content', 'image', 'tags', 'excerpt', 'meta_title', 'meta_description', 'created_by')
            ->chunk(1000, function ($posts) {
                $this->bulkIndex($posts);
            });

        $this->info("Indexing completed.");
    }

    private function bulkIndex($posts)
    {
        $bulk = ['body' => []];

        foreach ($posts as $post) {
            $bulk['body'][] = [
                'index' => [
                    '_index' => 'posts',
                    '_id'    => $post->id,
                ],
            ];

            $bulk['body'][] = [
            'id'                   => $post->id,
            'title'                => $post->title,
            'content'              => $post->content,
            'excerpt'              => $post->excerpt,
            'tags'                 => implode(',', json_decode($post->tags, true)),
            'image'                => $post->image,
            'meta_title'           => $post->meta_title,
            'created_by'           => $post->created_by,
            'meta_description'     => $post->meta_description
            ];
        }

        if (!empty($bulk['body'])) {
            $this->client->bulk($bulk);
        }
    }
}
