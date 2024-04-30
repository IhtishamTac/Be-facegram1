<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    public function post_attachment() {
        return $this->hasMany(PostAttachment::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
