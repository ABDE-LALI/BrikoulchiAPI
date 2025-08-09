<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $fillable = ['user_id', 'review_id'];
    public function user(){
        return $this->belongsTo(user::class);
    }
    public function serviceReview(){
        return $this->belongsTo(serviceReview::class);
    }
}
