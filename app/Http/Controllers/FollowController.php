<?php

namespace App\Http\Controllers;

use App\Models\CheckUser;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function followUser(string $username)
    {
        $user = User::where('username', $username)->first();
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        if ($user->id == auth()->id()) {
            return response()->json([
                'message' => 'You are not allowed to follow yourself'
            ], 422);
        }
        $alreadyFollow = Follow::where(['follower_id' => auth()->id(), 'following_id' => $user->id])->first();
        if($alreadyFollow){
            return response()->json([
                'message' => 'You already followed',
                'status' => $alreadyFollow->is_accepted ? 'following' : 'requested'
            ], 422);
        }
        $accepted = $user->is_private ? false : true;
        $follow = new Follow();
        $follow->follower_id = auth()->id();
        $follow->following_id = $user->id;
        $follow->is_accepted = $accepted;
        $follow->save();

        $status = $accepted ? 'following' : 'requested';
        return response()->json([
            'message' => 'Follow success',
            'status' => $status
        ], 200);
    }

    public function unfollowUser(string $username){
        $user = User::where('username', $username)->first();
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        $following = Follow::where(['follower_id' => auth()->id(), 'following_id' => $user->id])->first(); 
        if(!$following){
            return response()->json([
                'message' => 'You are not following the user',
            ], 422);
        }
        if($following->delete()){
            return response()->json([
            ], 204);
        }
    }

    public function getFollowings(){
        $followings = Follow::where('follower_id', auth()->id())->get();
        $followingId = $followings->pluck('following_id');
        $users = User::whereIn('id', $followingId)->get();
        return response()->json([
            'following' => $users
        ], 200);
    }
}
