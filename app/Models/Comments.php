<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\PostCollection;

class Comments extends Model
{
    protected $fillable = [
        'user_id',
        'post_id',
        'user_comment',
        'comment_date',
    ];

    protected $appends = ['formatted_date'];

    protected static function booted()
    {
        static::created(function () {
            PostCollection::updateCache();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->comment_date)->format('M d, Y');
    }

    public function getCommentsByPost($postId)
    {
        $comments = self::join('users', 'users.id', '=', 'comments.user_id')->where('post_id', '=', $postId)->orderBy('comment_date', 'desc')->get();
        return $comments;
    }
}
