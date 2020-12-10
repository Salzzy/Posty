<?php

namespace App\Http\Controllers;

use App\Mail\PostLiked;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PostLikeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function store(Post $post)
    {
        if ($post->likedBy(auth()->user())){
            return response(null, 409);
        }

        $post->likes()->create([
            'user_id' => auth()->user()->id,
        ]);

        // Wenn der user den Post NICHT schonmal geliked hat wird eine Email gesendet
        if (!$post->likes()->onlyTrashed->where('user_id', auth()->user()->id)->count()) {
            // Mail::to($post->user)->send(new PostLiked(auth()->user(), $post));
        }

        return back();
    }

    public function destroy(Post $post)
    {
        auth()->user()->likes()->where('post_id', $post->id)->delete();
        return back();
    }
}
