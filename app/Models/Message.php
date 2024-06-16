<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $table = 'messages';
    protected $fillable = [
        'room_id',
        'user_id',
        'content',
    ];
    public function room(){
        return $this->belongsTo(ChatRoom::class, 'room_id','id');
    }
    public function user(){
        return $this->belongsTo(User::class, 'user_id','id');
    }
}
