<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
   public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }else{
            $user = User::where('email',request('email'))->first();
            if(!$user||!Hash::check(request('password'),$user->password)){
                return response()->json([
                    'status' => 401,
                    'message' => 'Invalid Credentials'
                ]);
            }else{
                $token = $user->createToken($user->email.'_Token',[''])->plainTextToken;
                return response()->json([
                    'status' => 200,
                    'token' => $token,
                    'username'=>$user->name,
                    'message'=>'Login Successfully'
                ]);
            }
        }
    }
    public function register(Request $request){
       $validator = Validator::make($request->all(),[
           'name' => 'required',
           'phone'=>'required',
           'email' => 'required|email|unique:users',
           'password' => 'required',
       ]);
       if($validator->fails()){
           return response()->json([
               'status'=>422,
               'errors'=>$validator->messages(),
           ]);
       }else{
            $user = User::create([
                'name'=>$request->name,
                'phone'=>$request->phone,
                'email'=>$request->email,
                'password'=>Hash::make($request->password),
            ]);
            $token = $user->createToken($user->email.'_Token')->plainTextToken;
            return response()->json([
                'status'=>200,
                'username'=>$user->name,
                'token'=>$token,
                'message'=>'Register successfully',
            ]);
       }
    }
    public function logout(){
       auth()->user()->tokens()->delete();
       return response()->json([
           'status'=>200,
           'message'=>'Logged out',
       ]);
    }
}
