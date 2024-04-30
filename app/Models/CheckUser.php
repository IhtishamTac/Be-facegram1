<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckUser extends Model
{
    use HasFactory;

    public static function cekUsername(string $username){
        $user = User::where('username' , $username)->first();
        if(!$user){
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
    }
}
