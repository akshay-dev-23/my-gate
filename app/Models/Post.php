<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'content', 'likes', 'admin_notice', 'user_id', 'media'];
    protected $casts = [
        'media' => 'array',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function liked()
    {
        return $this->hasMany(Like::class);
    }

    // Check if the current user has liked the post
    public function userHasLiked($user_id)
    {
        return $this->liked()->where('user_id', $user_id)->exists();
    }

    public function scopeNoticeFilter($query,  $request)
    {
        if (is_bool($request->admin_notice) and $request->admin_notice) {
            $query->where('admin_notice', true);
        } else {
            $query->where('admin_notice', false);
        }
        return $query;
    }
}
