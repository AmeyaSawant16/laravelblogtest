<?php

namespace App\Http\Controllers\Comment;

use App\Models\Comments;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    public function addComment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'post_id' => 'required|integer',
            'user_id' => 'required|integer',
            'user_comment' => 'required|string',
        ]);

        $comment = Comments::create($validated);

        return back();
    }

    public function deleteComment(Request $request, Comments $comment_id): RedirectResponse
    {
        $comment_id->delete();

        return back();
    }
}
