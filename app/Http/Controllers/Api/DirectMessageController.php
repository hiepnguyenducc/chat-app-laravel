<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DirectMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DirectMessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        }else{

            if (auth('sanctum')->check()) {
                $sender_id = auth('sanctum')->user()->id;

                $message = DirectMessage::create([
                    'sender_id' => $sender_id,
                    'receiver_id' => $request->receiver_id,
                    'content' => $request->content,
                ]);

                return response()->json([
                    'status' => 201,
                    'message' => 'Tin nhắn đã được gửi thành công',
                    'data' => $message,
                ]);
            } else {
                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthorized',
                ]);
            }
        }
    }
    public function getMessages(Request $request, $user_id)
    {
        if (auth('sanctum')->check()) {
            $auth_user_id = auth('sanctum')->user()->id;

            $messages = DirectMessage::where(function ($query) use ($auth_user_id, $user_id) {
                $query->where('sender_id', $auth_user_id)
                    ->where('receiver_id', $user_id);
            })->orWhere(function ($query) use ($auth_user_id, $user_id) {
                $query->where('sender_id', $user_id)
                    ->where('receiver_id', $auth_user_id);
            })->orderBy('created_at', 'asc')
            ->get();

            return response()->json([
                'status' => 200,
                'data' => $messages,
            ]);
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized',
            ]);
        }
    }

    public function getLatestMessage($userId)
    {
        if (auth('sanctum')->check()) {
            $authUserId = auth('sanctum')->user()->id;

            $latestMessage = DirectMessage::where(function ($query) use ($authUserId, $userId) {
                $query->where('sender_id', $authUserId)
                    ->where('receiver_id', $userId);
            })->orWhere(function ($query) use ($authUserId, $userId) {
                $query->where('sender_id', $userId)
                    ->where('receiver_id', $authUserId);
            })->orderBy('created_at', 'desc')->first();

            return response()->json([
                'status' => 200,
                'message' => $latestMessage,
            ]);
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized',
            ]);
        }
    }

}
