<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function generateRandomText($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $shuffled = str_shuffle($characters);
        return substr($shuffled, 0, $length);
    }

    public function bot_register(Request $request){
        if(User::where('phone', $request->phone)->first()){
            return response()->json([
                'status' => 0,
                'message' => 'Phone Number is already used, please try a different one.',
            ]);
        }
        $data = new User();
        $data->role_level = 4;
        $data->phone = $request->phone;
        $data->name = $request->name;
        $data->email = $request->phone;
        $data->code = $this->generateRandomText();
        $data->password = Hash::make($data->code);
        $data->save();

        return response()->json([
            'status' => 1,
            'message' => 'Created Successfully.',
            'data' => new UserResource($data),
            'password' => $data->code,
        ]);
    }

    public function password(Request $request){
        $user_id = Auth::user()->id;
        $data = User::find($user_id);
        $data->code = $request->password;
        $data->password = Hash::make($request->password);
        $data->save();

        return response()->json([
            'status' => 1,
            'message' => 'Updated Successfully.',
        ]);
    }

    public function login(Request $request){
        $user = User::where('email', $request->email)->first();
        if(!isset($user)){
            return response()->json([
                'status' => 0,
                'message' => 'Email is not found.',
                'error' => 401,
            ]);
        }
        if(!Hash::check($request->password, $user->password)){
            return response()->json([
                'status' => 2,
                'message' => 'Password does not match.',
                'error' => 401,
            ]);
        }
        return response()->json([
            'status' => 1,
            'message' => 'Login Successfully.',
            'auth_token' => $user->createToken($user->email)->plainTextToken,
            'role_level' => !empty($user->role_level) ? $user->role_level : 1,
        ]);

    }

    public function register(Request $request){
        if(User::where('email', $request->email)->first()){
            return response()->json([
                'status' => 0,
                'message' => 'Email is already used, please try a different one.',
            ]);
        }
        $data = new User();
        $data->role_level = 4;
        $data->email = $request->email;
        $data->code = $request->password;
        $data->password = Hash::make($request->password);
        $data->save();

        return response()->json([
            'status' => 1,
            'message' => 'Created Successfully.',
        ]);
    }

    public function update(Request $request){
        $user_id = Auth::user()->id;
        $user = User::where('id', '!=', $user_id)->where('email', $request->email)->first();
        if(isset($user)) {
            return response()->json([
                'status' => 0,
                'message' => 'Email is already registered, please ty a different one.'
            ]);
        }
        $data = User::find($user_id);
        $data->name = $request->name;
        $data->phone = $request->phone;
        $data->email = $request->email;
        $data->address = $request->address;
        $data->updated_at = now();
        $data->save();
        return response()->json([
            'status' => 1,
            'message' => 'Saved Successfully.',
            'data' => new UserResource($data)
        ]);
    }

    public function view(){
        $user_id = Auth::user()->id;
        $data = User::with(['role'])->find($user_id);
        return new UserResource($data);
    }

    public function logout(){
        Auth::user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Log out succesfully.',
        ]);
    }

}
