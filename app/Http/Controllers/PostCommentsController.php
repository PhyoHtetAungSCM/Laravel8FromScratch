<?php

namespace App\Http\Controllers;

use App\Models\Post;

class PostCommentsController extends Controller
{
    public function store(Post $post)
    {
        // validation
        request()->validate([
            'body' => 'required',
        ]);

        // automatically set post_id in this approach
        $post->comments()->create([
            'user_id' => auth()->id(),
            'body' => request('body'),
        ]);

        return back();
    }
}
