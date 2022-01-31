<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    //

    public function register(Request $request){
        $attr = $request->validate([
            'name'=>'required|string',
            'email'=>'required|unique:users|max:225',       
            'tel'=>'required|unique:users|max:8|min:8',
            'password'=>'required|min:6|confirmed'
            
        ]);

        $user=User::create([
            'name'=>$attr['name'],
            'email'=>$attr['email'],
            'tel'=>$attr['tel'],
            'password'=>bcrypt($attr['password'])
        ]);

        return response([
            'user'=>$user,
            'token'=>$user->createToken('secret')->plainTextToken
        ]);
    }

    public function login(Request $request){
        $attr = $request->validate([
            'email'=>'required',
            'password'=>'required|min:6'
        ]);

        if (!Auth::attempt($attr)) {
            return response([
                'message'=>"Invalid credentials"
            ],403);
        }

        return response([
            'user'=>Auth::user(),
            'token'=>Auth::user()->createToken('secret')->plainTextToken
        ],200); 
    }

    public function logout(){
        Auth::user()->tokens()->delete();
        return response([
            'message'=>"logout successful"
        ],200);
    }

    public function user(){
        return response([
            'user'=>Auth::user()
        ],200);
    }

    public function update(Request $request){
        $attr = $request->validate([
            'name'=>'required|string'
        ]);

        $image = $this->saveImage($request->image,'profiles');
        Auth::user()->update([
            'name'=>$attr['name'],
            'image'=>$image
        ]);

        return response([
            'message'=>"User update",
            'user'=>Auth::user()
        ],200);
    }
}
