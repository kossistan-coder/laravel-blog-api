<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\User;

class PostController extends Controller
{
    //

    public function index(){
        return response([
            'post'=>Post::orderBy('created_at','desc')->with('user:id,name,image')->withCount('comments','likes')->with('likes',function($likes){
                return $likes->where('user_id',Auth::user())->select('id','user_id','post_id')->get();
            })->get()         
        ],200);
    }

    public function show($id){
        return response([
            'post'=>Post::where('id',$id)->withCount('comments','likes')->get()
        ]);
    }

    public function store(Request $request){
        $attr = $request->validate([
            'body'=>'required|string'
        ]);

        //$image = $this->saveImage($request->image,'posts');
        /*if ($request->hasFile('image') ) {

            $dest_path='public/profiles';
            $image=$request->file('image');
            $img_name=$image->hashName();
            $path=$request->file('image')->storeAs($dest_path,$img_name);
            $img='http://localhost:8000/storage/public/posts/'.$img_name;

         }*/

        $post = Post::create([
            'body'=>$attr['body'],
            'user_id'=>Auth::user()->id,
            //'image'=>$img
        ]);

        return response([
            'message'=>"Post created",
            'post'=>$post
        ],200);
    }

    public function update(Request $request,$id){
        $post = Post::find($id);
        if(!$post){
            return response([
                'messsage'=>"Post not found"
            ],403);
        }

        if ($post->user_id != Auth::user()->id) {
            return response([
                'message'=>"Access denied"
            ],403);

        }

        $attr = $request->validate([
            'body'=>'required|string'
        ]);

        $post->update([
            'body'=>$attr['body']
        ]);

        return response([
            'message'=>"Post updated",
            'post'=>$post
        ],200);

    }

    public function destroy(Request $request , $id){
        $post=Post::find($id);
        if (!$post) {
            return response([
                'message'=>"This post does not exist"
            ]);

        }

        if($post->user_id !=Auth::user()->id){
            return response([
                'message'=>"Persmissions denied"
            ],403);
        }

        $post->comments()->delete();       
        $post->likes()->delete();
        $post->delete();

        return response([
            'message'=>"Post deleted successfully"
        ],200);
    }
}
