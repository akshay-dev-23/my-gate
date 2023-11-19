<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function index(Post $post, Request $request)
    {
        $comments = CommentResource::collection($post->comments()->latest()->paginate($request->record_per_page ?? 10));
        $response_data = [
            'total_records' => $comments->total(),
            'current_page' => $comments->currentPage(),
            'posts' => $comments->items(),
        ];
        return $this->successResponse("Comment listing", $response_data);
    }
    public function store(Request $request, Post $post)
    {
        $validator = Validator::make($request->all(), [
            'body' => 'required',
        ]);
        if ($validator->fails())
            throw new Exception($validator->errors()->first(), Response::HTTP_BAD_REQUEST);
        $post->comments()->create([
            'body' => $request->input('body'),
            'user_id' => auth()->id()
        ]);
        return $this->successResponse("Comment created");
    }

    public function destroy(Post $post, Comment $comment)
    {
        $comment->delete();
        return $this->successResponse("Comment deleted");
    }

}
