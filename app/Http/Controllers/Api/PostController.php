<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\SocietyNotification;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = PostResource::collection(Post::latest()->paginate($request->record_per_page ?? 10));
        $response_data = [
            'total_records' => $posts->total(),
            'current_page' => $posts->currentPage(),
            'posts' => $posts->items(),
        ];
        return $this->successResponse("Posts listing.", $response_data);
    }

    public function show(Post $post)
    {
        return $this->successResponse("Posts listing.", new PostResource($post));
    }

    public function update(PostRequest $request, Post $post)
    {
        $post->update([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'admin_notice' => $request->input('admin_notice', false),
            'media' => $request->input('media', [])
        ]);
        return $this->successResponse("Post updated successfully.", new PostResource($post));
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return $this->successResponse("Post deleted successfully");
    }

    public function store(PostRequest $request)
    {
        $post = auth()->user()->posts()->create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'admin_notice' => $request->input('admin_notice', false),
            'media' => $request->input('media', [])
        ]);
        // notify all society member for the post
        SocietyNotification::notifyPostCreated();
        return $this->successResponse("Post created successfully.", new PostResource($post));
    }

    public function like(Post $post)
    {
        $user = auth()->user();
        if ($post->userHasLiked($user->id)) {
            // User has liked the post, so unlike it
            $post->liked()->where('user_id', $user->id)->delete();
            // Decrement the likes count in the posts table
            $post->decrement('likes');
            return $this->successResponse("Like removed.");
        } else {
            $post->liked()->create(['user_id' => $user->id]);

            // Increment the likes count in the posts table
            $post->increment('likes');
            return $this->successResponse("Post Liked.");
        }
    }
}
