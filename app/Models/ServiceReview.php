<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceReview extends Model
{
    use HasFactory;
    protected $fillable = ['service_id', 'user_id', 'rating', 'text'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
    public function likes()
    {
        return $this->hasMany(Like::class);
    }
    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'review_likes', 'review_id', 'user_id')->withTimestamps();
    }
}
