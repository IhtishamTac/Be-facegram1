<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(),[
            'full_name' => 'required',
            'bio' => 'required|max:100',
            'username' => 'required|min:3|unique:users,username|regex:/^[a-zA-Z0-9._]+$/',
            'password' => 'required|min:6',
            'is_private' => 'boolean'
        ],[
            'username.regex' => 'only alphanumeric, dot “.” or underscore “_“ allowed'
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'Invalid fields',
                'errors' => $validator->errors()
            ], 422);
        }

        $cuser = User::create([
            'full_name' => $request->full_name,
            'username' => $request->username,
            'bio' => $request->bio,
            'password' => $request->password,
            'is_private' => $request->is_private || 0
        ]);
        $credentials = $request->only(['username', 'password']);
        if(!Auth::attempt($credentials)){
            return response()->json('', 400);
        }
        $token = $cuser->createToken('TOKENSANCTUM')->plainTextToken;
        return response()->json([
            'message' => 'Register succcess',
            'token' => $token,
            'user' => $cuser
        ], 201);
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(),[
            'username' => 'required',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'Invalid fields',
                'errors' => $validator->errors()
            ], 422);
        } 

        $credentials = $request->only(['username', 'password']);
        if(!Auth::attempt($credentials)){
            return response()->json([
                'message' => 'Wrong username or password'
            ], 401);
        }

        $user = User::where('username', $request->username)->first();
        $token = $user->createToken("SACNTUMTOKEN")->plainTextToken;
        return response()->json([
            'message' => 'Login succcess',
            'token' => $token,
            'user' => $user
        ], 201);
    }

    public function logout(Request $request) {
        if($request->user()->Tokens()->delete()){
            return response()->json([
                'message' => 'Logout success'
            ], 200);
        }
    }
}
