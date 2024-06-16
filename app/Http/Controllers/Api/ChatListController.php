<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Friend;
use Illuminate\Http\Request;

class ChatListController extends Controller
{
    public function viewFriend()
    {
        if (auth('sanctum')->check()) {
            $user_id = auth('sanctum')->user()->id;

            // Lấy danh sách bạn bè với trạng thái accepted
            $acceptedFriends = Friend::where('user_id', $user_id)
                ->where('status', 'accepted')
                ->orWhere(function($query) use ($user_id) {
                    $query->where('friend_id', $user_id)
                        ->where('status', 'accepted');
                })->get();

            // Lấy thông tin chi tiết của bạn bè
            $friendIds = $acceptedFriends->pluck('friend_id')->merge($acceptedFriends->pluck('user_id'))->unique()->filter(function ($value) use ($user_id) {
                return $value != $user_id;
            });

            $friends = User::whereIn('id', $friendIds)->get();

            return response()->json([
                'status' => 200,
                'friends' => $friends
            ]);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

}
