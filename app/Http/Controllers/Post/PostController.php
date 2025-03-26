<?php

namespace App\Http\Controllers\Post;

use App\Models\Comments;
use App\Models\User;
use App\Models\PostCollection;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendLoginMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Services\ElasticsearchService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;


class PostController extends Controller
{

    protected $elasticsearch;
    protected $indexName = 'posts';
    protected $postCollection;

    public function __construct(ElasticsearchService $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
        $this->postCollection = new PostCollection();
    }

    public function homePage(Request $request): Response
    {
        $posts = $this->postCollection->getAllPosts();
        // dd(`post:`, $posts);
        $cacheData =  Cache::get("posts_list_chunk_1", collect());
        return Inertia::render('Dashboard', ["postList" => $posts, 'cacheData' => $cacheData]);
    }

    public function postDetail(Request $request, PostCollection $postId): Response
    {  
        $commentsModel = new Comments();
        $comments = $commentsModel->getCommentsByPost($postId->id);

        $userModel = new User();
        $userDetails = $userModel->getUserDetails($postId->created_by);
    
        return Inertia::render('post/Post', ['post' => $postId, 'userDetails' => $userDetails, "comments" => $comments]);
    }

    public function myPost(Request $request): Response
    {
        $posts = $this->postCollection->getUserPosts(Auth::user()->id);
        return Inertia::render('post/MyPost',  ["postList" => $posts]);
    }

    public function createPost(Request $request): Response
    {
        return Inertia::render('post/CreatePost');
    }

    public function storePost(Request $request): Response
    {
        try {

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'excerpt' => 'nullable|string',
                'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'tags' => 'nullable|string',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
                'publish_type' => 'required|in:now,schedule',
                'publish_datetime' => 'nullable|date',
            ]);

            $validated['title'] = strip_tags($validated['title']);
            $validated['content'] = strip_tags($validated['content']);
            $validated['excerpt'] = strip_tags($validated['excerpt'] ?? '');
            $validated['meta_title'] = strip_tags($validated['meta_title'] ?? '');
            $validated['meta_description'] = strip_tags($validated['meta_description'] ?? '');
            $validated['tags'] = isset($validated['tags']) ? Str::of($validated['tags'])->squish() : null; // Remove extra spaces
            $validated['publish_datetime'] = isset($validated['publish_datetime']) ? filter_var($validated['publish_datetime'], FILTER_SANITIZE_STRING) : null;
    
    
            $imageName =null;
    
            if ($request->hasFile('image')) {
    
                $file = $request->file('image');
                $imageName = Str::uuid() . '.' . $file->getClientOriginalExtension();

                $file->storeAs('posts', $imageName, 'public');
            }
           
            $post = PostCollection::create([
                'title' => $request->title,
                'content' => $request->content,
                'excerpt' => $request->excerpt,
                'image' => $imageName, // Store image name
                'tags' => json_encode(explode(',', $request->keywords)),
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'publish_type' => $request->publish_type,
                'publish_datetime' => $request->publish_type == 'schedule' ? $request->publish_datetime : Carbon::now(),
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
                'published' => $request->publish_type == 'now' ? true : false,
            ]);

            $data = ['id' => $post->id,'title' => $post->title, 'content' => $post->content, 'image' => $post->image, 'tags' => implode(',', json_decode($post->tags, true)), 'excerpt' => $post->excerpt, 'meta_title' => $post->meta_title, 'meta_description' => $post->meta_description, 'created_by' => $post->created_by];

            $this->elasticsearch->createPostIndex();

            $this->elasticsearch->indexDocument($this->indexName, $post->id, $data);


            return Inertia::render('post/CreatePost', ['message' => 'Post created successfully']);

        } catch (Throwable $th) {
            return response()->json(['error' => 'Something went wrong.']);
            return redirect()->back()->withErrors(['error' => 'Something went wrong.']);
        }
        

        
    }

    public function editPost(Request $request, PostCollection $post_id): Response
    {
         return Inertia::render('post/UpdatePost', ["postDetails" => $post_id]);
    }

    public function updatePost(Request $request, PostCollection $post): Response
    {
        try {

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'excerpt' => 'nullable|string',
                'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'tags' => 'nullable|string',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
                'publish_type' => 'required|in:now,schedule',
                'publish_datetime' => 'nullable|date',
            ]);

            $validated['title'] = strip_tags($validated['title']);
            $validated['content'] = strip_tags($validated['content']);
            $validated['excerpt'] = strip_tags($validated['excerpt'] ?? '');
            $validated['meta_title'] = strip_tags($validated['meta_title'] ?? '');
            $validated['meta_description'] = strip_tags($validated['meta_description'] ?? '');
            $validated['tags'] = isset($validated['tags']) ? Str::of($validated['tags'])->squish() : null; // Remove extra spaces
            $validated['publish_datetime'] = isset($validated['publish_datetime']) ? filter_var($validated['publish_datetime'], FILTER_SANITIZE_STRING) : null;
    
    
            $imageName = $post->image;
    
            if ($request->hasFile('image')) {

                if($post->image){
                    Storage::disk('public')->delete('posts/' . $post->image);
                }
    
                $file = $request->file('image');
                $imageName = Str::uuid() . '.' . $file->getClientOriginalExtension();

                $file->storeAs('posts', $imageName, 'public');
            }
           
            $post->update([
                'title' => $request->title,
                'content' => $request->content,
                'excerpt' => $request->excerpt,
                'image' => $imageName, // Store image name
                'tags' => json_encode(explode(',', $request->keywords)),
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'publish_type' => $request->publish_type,
                'publish_datetime' => $request->publish_datetime,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
                'published' => $request->publish_type == 'now' ? true : false,
            ]);

            $data = ['title' => $post->title, 'content' => $post->content, 'image' => $post->image, 'tags' => implode(',', json_decode($post->tags, true)), 'excerpt' => $post->excerpt, 'meta_title' => $post->meta_title, 'meta_description' => $post->meta_description];

            $this->elasticsearch->updateDocument($this->indexName, $post->id, $data);


            return Inertia::render('post/UpdatePost', ['message' => 'Post Updated successfully', 'post' => $post->id]);

        } catch (Throwable $th) {
            return response()->json(['error' => 'Something went wrong.']);
            return redirect()->back()->withErrors(['error' => 'Something went wrong.']);
        }
        

        
    }

    public function deletePost(Request $request, PostCollection $post): RedirectResponse
    {
        if($post->image){
            Storage::disk('public')->delete('posts/' . $post->image);
        }

        $this->elasticsearch->deleteDocument($this->indexName, $post->id);
        
        $post->delete();

        return to_route('post.myPost');
    }

    function getCachedImage($filename)
    {
        $path = public_path("storage/posts/{$filename}");
    
        if (!File::exists($path)) {
            abort(404);
        }
    
        // Cache the image content for 1 hour
        $image = Cache::remember("posts:{$filename}", 3600, function () use ($path) {
            return File::get($path);
        });

        $mimeType = File::mimeType($path);
        
        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=604800, immutable'
        ]);
        // return response($image)->header('Content-Type', 'image/svg');
    }
    
}
