<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FriendController;
use App\Http\Controllers\Api\ChatListController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\DirectMessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->group(function (){
    Route::get('/checkingAuthenticated',function (){
        return response()->json(['message' => 'Authenticated user'], 200);
    });
    Route::post('/findPhone',[FriendController::class,'findPhone']);
    Route::post('/add-friends', [FriendController::class, 'store']);
    Route::get('/friends',[ChatListController::class,'viewFriend']);


    // Route để tạo phòng chat và thêm người dùng vào phòng chat
    Route::post('chat-rooms', [MessageController::class, 'createChatRoom']);

    // Route để lưu tin nhắn vào phòng chat
    Route::post('messages', [MessageController::class, 'store']);

    // Route để lấy tin nhắn từ phòng chat
    Route::get('chat-rooms/{room_id}/messages', [MessageController::class, 'getMessages']);

    Route::post('direct-messages', [DirectMessageController::class, 'sendMessage']);

    // Route để lấy tin nhắn trực tiếp giữa hai người dùng
    Route::get('direct-messages/{user_id}', [DirectMessageController::class, 'getMessages']);
});
