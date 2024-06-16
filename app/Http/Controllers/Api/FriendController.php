<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Friend;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FriendController extends Controller
{
    public function findPhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
            ]);
        }else{
            if(auth('sanctum')->check()){
               $user = User::where('phone',$request->phone)->first();
               return response()->json([
                   'status' => 200,
                   'user' => $user,
               ]);

            }else{
                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthorized',
                ]);
            }
        }
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'friend_id'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ]);
        } else {
            if (auth('sanctum')->check()) {
                $user_id = auth('sanctum')->user()->id;
                $friend_id = $request->friend_id;

                $existingFriend = Friend::where(function ($query) use ($user_id, $friend_id) {
                    $query->where('user_id', $user_id)->where('friend_id', $friend_id);
                })->orWhere(function ($query) use ($user_id, $friend_id) {
                    $query->where('user_id', $friend_id)->where('friend_id', $user_id);
                })->first();

                if ($existingFriend) {
                    return response()->json([
                        'status' => 409,
                        'message' => 'Yêu cầu kết bạn đã tồn tại'
                    ]);
                } else {
                    $friend = new Friend();
                    $friend->user_id = $user_id;
                    $friend->friend_id = $friend_id;
                    $friend->status = 'pending';
                    $friend->save();

                    return response()->json([
                        'status' => 201,
                        'message' => 'Yêu cầu kết bạn đã được gửi thành công'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthorized'
                ]);
            }
        }
    }
}
