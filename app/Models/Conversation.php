<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;
    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }
    public function user1Data(){
        return $this->belongsTo(User::class, 'user1');
    }
    public function user2Data(){
        return $this->belongsTo(User::class, 'user2');
    }
}
