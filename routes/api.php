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

    Route::post('chat-rooms', [MessageController::class, 'createChatRoom']);

    Route::post('messages', [MessageController::class, 'store']);

    Route::get('chat-rooms/{room_id}/messages', [MessageController::class, 'getMessages']);
    Route::post('direct-messages', [DirectMessageController::class, 'sendMessage']);
    Route::get('direct-messages/{user_id}', [DirectMessageController::class, 'getMessages']);
    Route::get('direct-messages/latest/{userId}',[DirectMessageController::class, 'getLatestMessage']);
    Route::get('accepted-friends', [FriendController::class, 'getAcceptedFriends']);
    Route::get('pending-friends', [FriendController::class, 'getPendingFriends']);
});
