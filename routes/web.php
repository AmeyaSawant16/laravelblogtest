<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Post\PostController;
use App\Http\Controllers\Comment\CommentController;
use Inertia\Inertia;

Route::get('/', [PostController::class, 'homePage'])->name('home');



Route::prefix('post')->group(function(){
    
    Route::middleware('auth')->group(function(){
        Route::get('/my_post', [PostController::class, 'myPost'])->name('post.myPost');
        Route::get('/create_post', [PostController::class, 'createPost'])->name('post.createPost');
        Route::post('/create_post', [PostController::class, 'storePost'])->name('post.storePost');
        Route::get('/update_post/{post_id}', [PostController::class, 'editPost'])->name('post.editPost');
        Route::post('/update_post/{post}', [PostController::class, 'updatePost'])->name('post.updatePost');
        Route::delete('/update_post/{post}', [PostController::class, 'deletePost'])->name('post.deletePost');
    });


    Route::get('/{post_id}', [PostController::class, 'postDetail'])->name('post.postDetail');

    Route::get('/image/{filename}', [PostController::class, 'getCachedImage']);
});

Route::prefix('comment')->group(function(){
    Route::middleware('auth')->group(function (){
        Route::post('/add', [CommentController::class, 'addComment'])->name('comment.addComment');
        Route::delete('/delete/{comment_id}', [CommentController::class, 'deleteComment'])->name('comment.deleteComment');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
