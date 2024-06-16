<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserChatRoom extends Model
{
    use HasFactory;
    protected $table = 'user_chat_rooms';
    protected $fillable = [
        'user_id',
        'room_id',
        'joined_at',
    ];
    public function user(){
        return $this->belongsTo(User::class,'user_id','id');

    }
    public function room(){
        return $this->belongsTo(ChatRoom::class,'room_id','id');
    }
}
