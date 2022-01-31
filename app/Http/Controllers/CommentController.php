<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use App\Models\Post;

class CommentController extends Controller
{
    //
    public function index($id){
        $post= Post::find($id);
        if (!$post) {
            return response([
                'message'=>"Post not found"
            ]);

        }
        return response([
            'comments'=>$post->comments()->with('user:id,name,image')->get()
        ],200);
    }

    public function store(Request $request , $id){
        $post= Post::find($id);
        if (!$post) {
            return response([
                'message'=>"Post not found"
            ]);

        }
        $attr = $request->validate([
            'comment'=>'required|string'
        ]);

        Comment::create([
            'comment'=>$attr['comment'],
            'user_id'=>Auth::user()->id,
            'post_id'=>$id
        ]);
        return response([
            'message'=>"Comment created"
        ],200);
    }

    public function update(Request $request , $id){
        $comment=Comment::find($id);
        if (!$comment) {
            return response([
                'message'=>"This comment does not exist"
            ]);
        }

        if ($comment->user_id != Auth::user()->id) {
            return response([
                'message'=>"You have not right to modify this comment"
            ]);
        }

        $attr = $request->validate([
            'comment'=>'required|string'
        ]);

        $comment->update([
            'comment'=>$attr['comment']
        ]);

        return response([
            'message'=>"Comment update"
        ]);
    }

    public function destroy($id){
        $comment = Comment::find($id);
        if (!$comment) {
            return response([
                'message'=>"This comment does not exist"
            ]);
        }
        if ($comment->user_id !=Auth::user()->id) {
            return response([
                'message'=>"You do not have permissions to delete this comment"
            ]);
        }
        $comment->delete();
        return response([
            'message'=>"comment deleted"
        ]);
    }
}
