<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Repository\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        foreach($users as $user)
        {
            $roles = $user->getRoleNames();
            $new_role = [];
            foreach($roles as $role)
            {
                $new_role[] = $role;
            }

            $user->role = $new_role;
            unset($user->roles);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Success',
            'data' => $users
        ]);
    }

    public function create()
    {
        $postdata = request()->all();
        $validator = Validator::make($postdata, [
            'name' => 'required',
            'username' => ['required', Rule::unique('musers', 'username')->whereNull('deleted_at')],
            'email' => ['required', Rule::unique('musers', 'email')->whereNull('deleted_at')],
            'password' => 'required|confirmed|min:8',
            'role' => 'required'
        ]);


        if($validator->fails())
        {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()
            ], 400);
        }

        $roles = $postdata['role'];

        $user = User::create(array_merge(
            $validator->validated(),
            [
                'password' => Hash::make(request('password'))
            ]
        ));

        $user->assignRole($roles);

        return response()->json([
            'status' => 1,
            'message' => 'Successfully Created.',
        ]);
    }

    public function detail()
    {
        $user = User::findOrFail(auth()->user()->id);
        $roles = $user->getRoleNames();

        foreach($roles as $role)
        {
            $new_role[] = $role;
        }
        $user->role = $new_role;
        unset($user->roles);

        return response()->json([
            'status' => 1,
            'message' => 'Success',
            'data' => $user
        ]);
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = $user->getRoleNames();

        foreach($roles as $role)
        {
            $new_role[] = $role;
        }
        $user->role = $new_role;
        unset($user->roles);

        return response()->json([
            'status' => 1,
            'message' => 'Success',
            'data' => $user
        ]);
    }

    public function update($id)
    {
        $postdata = request()->all();

        $validator = Validator::make($postdata, [
            'name' => 'required',
            'username' => ['required', Rule::unique('musers', 'username')->ignore($id)->whereNull('deleted_at')],
            'email' => ['required', Rule::unique('musers', 'email')->ignore($id)->whereNull('deleted_at')],
            // 'password' => 'required|confirmed|min:6'
        ]);

        if($validator->fails())
        {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors(),
            ]);
        }

        if(isset($postdata['password']))
        {
            User::where('id', $id)->update(array_merge(
                $validator->validated(),
                [
                    'password' => Hash::make($postdata['password'])
                ]
            ));
        } else if(isset($postdata['role']))
        {
            User::where('id', $id)->update(array_merge(
                $validator->validated(),
                []
            ));
            $user = User::findOrFail($id);
            $user->syncRoles($postdata['role']);
        }
        else {
            User::where('id', $id)->update(array_merge(
                $validator->validated(),
                []
            ));
        }

        $user = User::findOrFail($id);
        if(isset($postdata['role']))
        {
            $user->syncRoles($postdata['role']);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Success'
        ]);

    }

    public function delete($id)
    {
        $user = User::where('id', $id)->delete();

        if(!$user)
        {
            return response()->json([
                'status' => 0,
                'message' => 'Failure'
            ]);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Success'
        ]);
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id)->restore();

        if(!$user)
        {
            return response()->json([
                'status' => 0,
                'message' => 'Failure'
            ]);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Success',
            'data' => User::findOrFail($id),
        ]);
    }

    public function permanent($id)
    {
        $user = User::withTrashed()->findOrFail($id)->delete();

        if(!$user)
        {
            return response()->json([
                'status' => 0,
                'message' => 'Failure'
            ]);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Success',
        ]);
    }

    public function roleList()
    {

        $data = (new UserRepository)->roleList();


        if(!$data)
        {
            return response()->json([
                'status' => 0,
                'message' => 'Data not found!'
            ], 404);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Success',
            'data' => $data
        ]);
    }
}
