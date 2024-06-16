<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatRoom;
use App\Models\Message;
use App\Models\User;
use App\Models\UserChatRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{

    public function createChatRoom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array|min=2', // Mảng chứa ID của các người dùng
            'user_ids.*' => 'exists:users,id', // Mỗi ID phải tồn tại trong bảng users
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        }else{
            $chatRoom = ChatRoom::create();

            foreach ($request->user_ids as $user_id) {
                UserChatRoom::create([
                    'chat_room_id' => $chatRoom->id,
                    'user_id' => $user_id,
                ]);
            }
            return response()->json([
                'status'=>200,
                'message'=>'Message sent'
            ]);
        }

    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:chat_rooms,id',
            'content' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ]);
        }else{
            if(auth('sanctum')->check()){
                $user_id = auth('sanctum')->user()->id;
                $message = Message::create([
                    'room_id' => $request->room_id,
                    'user_id' => $user_id,
                    'content' => $request->content,
                ]);
                return response()->json([
                    'status' => 201,
                    'message' => 'Tin nhắn đã được gửi thành công',
                    'data' => $message,
                ]);
            }else{
                return response()->json([
                    'status'=>401,
                    'message'=>'Unauthorized'
                ]);
            }
        }
    }
    public function getMessage(Request $request,$room_id)
    {
        if(auth('sanctum')->check()){
            $user_id = auth('sanctum')->user()->id;
            $isParticipant = UserChatRoom::where('chat_room_id',$room_id)->where('user_id',$user_id)->exists();
            if($isParticipant){
                return response()->json([
                    'status'=>403,
                    'message'=>'Bạn không có quyền truy cập vào phòng chat này'
                ]);
            }
            $message = Message::where('room_id',$room_id)->get();
            return response()->json([
                'status'=>200,
                'message'=>$message
            ]);
        }else{
            return response()->json([
                'status'=>401,
                'message'=>'Unauthorized'
            ]);
        }
    }
}
