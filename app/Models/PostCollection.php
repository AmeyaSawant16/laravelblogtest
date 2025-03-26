<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class PostCollection extends Model
{
    use HasFactory;

    protected $table = 'post_collections';

    protected $fillable = [
        'title',
        'content',
        'excerpt',
        'image',
        'tags',
        'meta_title',
        'meta_description',
        'publish_type',
        'publish_datetime',
        'created_by',
        'updated_by',
        'published',
    ];

    protected $appends = ['formatted_date'];

    static $chunkSize = 30;

    protected static function booted()
    {
        static::created(function () {
            self::updateCache();
        });

        static::updated(function () {
            self::updateCache();
        });

        static::deleted(function () {
            self::updateCache();
        });
    }

    public static function updateCache()
    {
        
        $totalRecords = self::count();

        // Delete old cache keys
        for ($i = 0; $i < ceil($totalRecords / self::$chunkSize); $i++) {
            Cache::forget('posts_list_chunk_'.($i+1));
        }


        $columns = [
            'post_collections.id',
            'post_collections.title',
            'post_collections.excerpt',
            'post_collections.tags',
            'post_collections.image',
            'post_collections.meta_title',
            'post_collections.meta_description',
            'post_collections.publish_datetime',
            'post_collections.created_by',
            'post_collections.content',
            'post_collections.published',
            'post_collections.created_at',
        ];

        // Store data in chunks
        self::leftJoin('comments', 'post_collections.id', '=', 'comments.post_id') // Join with comments
        ->select(array_merge($columns, [DB::raw('COUNT(comments.id) as comment_count')])) 
        ->where('post_collections.published', 1)
        ->groupBy($columns)
        ->orderBy('post_collections.created_at', 'desc')
        ->chunk(self::$chunkSize, function ($records, $index) {
            Cache::put('posts_list_chunk_'.$index, $records, 3600);
        });
        /* self::where('published', '=', '1')
            ->orderBy('created_at', 'desc')
            ->chunk(self::$chunkSize, function ($records, $index) {
                Cache::put('posts_list_chunk_'.$index, $records, 3600);
            }); */
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->publish_datetime)->format('M d, Y');
    }

    public function getAllPosts(){

        $allRecords = [];

        if (!Cache::has('posts_list_chunk_1')) {
            self::updateCache();
        }

        $totalRecords = self::count();
        for ($i = 0; $i < ceil($totalRecords / self::$chunkSize); $i++) {
            $chunk = Cache::get('posts_list_chunk_'.($i+1), collect());
            $allRecords[] = $chunk;
        }
        return $allRecords;
    }

    public function getUserPosts($userId){
        $posts = self::where('created_by', '=', $userId)->orderBy('created_at', 'desc')->get();
        return $posts;
    }

}
