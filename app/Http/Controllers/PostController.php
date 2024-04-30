<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->mergeIfMissing([
            'size' => $request->input('size', 0),
            'page' => $request->input('page', 10)
        ]);

        $validator = Validator::make($request->all(),[
            'size' => 'integer|min:0',
            'page' => 'integer|min:1',
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'Invalid fields',
                'errors' => $validator->errors()
            ], 422);
        } 

        $posts = Post::with('attachments')->get();
        return response()->json([
            'page' => $request->page,
            'size' => $request->size,
            'posts' => $posts
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'caption' => 'required',
            'attachments.*' => 'required|image:png,jpg,jpeg,webp',
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'Invalid fields',
                'errors' => $validator->errors()
            ], 422);
        } 

        $post = new Post();
        $post->caption = $request->caption;
        $post->user_id = auth()->id();
        $post->save();
        foreach ($request->attachments as $attach) {
            $postat = new PostAttachment();
            $postat->storage_path = $attach->store('post_attachments');
            $postat->post_id = $post->id;
            $postat->save();
        }

        return response()->json([
            'message' => 'Create post success'
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::where('id', $id)->first();
        if(!$post) return response()->json(['message' => 'Post not found'], 404);
        if($post->user_id != auth()->id()) return response()->json(['message' => 'Forbidden access'], 403);
        if($post->delete()){
            return response()->json([], 204);
        }
    }
}
