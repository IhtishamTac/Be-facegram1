<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function accFollowRequest(string $username){
        $user = User::where('username', $username)->first();
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        $follow = Follow::where(['following_id' => auth()->id(), 'follower_id' => $user->id])->first();
        if(!$follow){
            return response()->json([
                'message' => 'The user is not following you'
            ], 422);
        }
        if($follow->is_accepted == true){
            return response()->json([
                'message' => 'Follow request is already accepted'
            ], 422);
        }
        $follow->is_accepted = true;
        $follow->save();
        return response()->json([
            'message' => 'Follow request accepted'
        ], 200);
    }

    public function getFollowers(string $username){
        $user = User::where('username', $username)->first();
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        $followers = Follow::where('following_id', $user->id)->get();
        $followerId = $followers->pluck('follower_id');
        $users = User::whereIn('id', $followerId)->get();
        return response()->json([
            'followers' => $users
        ], 200);
    }



    public function getUserNotFollowed(){
        $follow = Follow::where('follower_id', auth()->id())->get();    
        $followingId = $follow->pluck('following_id'); 
        
        $followingId->push(auth()->id());
        
        $mergeId = $followingId->toArray();
        
        $user = User::whereNotIn('id', $mergeId)->get();
        
        return response()->json([
            'users' => $user
        ], 200);
    }

    public function getDetailUser(string $username){
        $user = User::where('username', $username)->with('post.attachments')->first();
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        $isYourAcc = false;
        if($user->id == auth()->id()) $isYourAcc = true;
        $follow = Follow::where(['follower_id' => auth()->id(), 'following_id' => $user->id])->first();
        $followingStatus = $follow ? ($follow->is_accepted ? 'Following' : 'Requested') : 'Not-following';
        $postsCount = Post::where('user_id', $user->id)->count();
        $followersCount = Follow::where('following_id', $user->id)->count();
        $followingCount = Follow::where('follower_id', $user->id)->count();

        $user->is_your_account = $isYourAcc;
        $user->following_status = $followingStatus;
        $user->post_count = $postsCount;
        $user->followers_count = $followersCount;
        $user->following_count = $followingCount;
        return response()->json([
             $user
        ], 200);
    }
}
