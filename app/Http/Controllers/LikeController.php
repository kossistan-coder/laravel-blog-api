<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Like;

class LikeController extends Controller
{
    //
    public function LikeOrUnlike($id){
        $post=Post::find($id);
        if(!$post){
            return response([
                'messsage'=>"Post not found"
            ],403);
        }

        $like = $post->likes()->where('user_id',Auth::user()->id)->first();
        if (!$like) {
            Like::create([
                'user_id'=>Auth::user()->id,
                'post_id'=>$id
            ]);
            return response([
                'message'=>"Liked"
            ],200);
        }

        $like->delete();
        return response([
            'message'=>"Disliked"
        ],200);

    }
}
