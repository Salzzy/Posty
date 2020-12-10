<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth'])->only('store', 'destroy');
    }

    public function index()
    {
        $posts = Post::latest()->with(['user', 'likes'])->paginate(10);

        return view('posts.index', [
            'posts' => $posts
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'body' => 'required',
        ]);

        // save the post to the user
        auth()->user()->posts()->create($request->only('body'));
        return redirect()->route('posts');
    }

    public function show(Post $post)
    {
        return view('posts.show', [
            'post' => $post,
        ]);
    }

    public function destroy(Post $post)
    {
        // Policy asks if user can delete the post
        // policy registered in AuthServiceProvider
        // policy gets the user implicitly, just need to add the post model
        // throws an exception if not authorized
        $this->authorize('delete', $post);

        $post->delete();

        return back();
    }
}
