<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register()
    {
        $postdata = request()->all();
        $validator = Validator::make($postdata, [
            'name' => 'required',
            'username' => ['required', Rule::unique('musers', 'username')->whereNull('deleted_at')],
            'email' => ['required', Rule::unique('musers', 'email')->whereNull('deleted_at')],
            'password' => 'required|confirmed|min:8',
            'role' => 'required'
        ]);

        $roles = $postdata['role'];

        if($validator->fails())
        {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()
            ], 400);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            [
                'password' => Hash::make(request('password'))
            ]
        ));

        $user->assignRole($roles);

        $data = User::findOrFail($user->id);

        return response()->json([
            'status' => 1,
            'message' => 'Success',
            'data' => $data
        ]);
    }

    public function login()
    {
        $postdata = request()->all();
        $validator = Validator::make($postdata, [
            'username' => 'required',
            'password' => 'required',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()
            ], 400);
        }

        if(!$token = auth()->attempt($validator->validated()))
        {
            return response()->json(['status' => 0, 'message' => 'Unauthorize'], 401);
        }

        $user = User::findOrFail(auth()->user()->id);
        $roles = $user->getRoleNames();

        foreach($roles as $role)
        {
            $new_role[] = $role;
        }
        $user->role = $new_role;
        unset($user->roles);

        return $this->createNewToken($token, $user);
    }

    private function createNewToken($token, $user)
    {
        return response()->json([
            'status' => 1,
            'message' => 'Success',
            'access_token' => $token,
            'expired_in' => JWTAuth::factory()->getTTL(), // 60 minutes 24 hours => 1 Day
            'data' => $user,
        ]);
    }

    public function logout()
    {
        auth()->logout();
        return response()->json([
            'status' => 1,
            'message' => 'Logout Successfull'
        ]);
    }
}
