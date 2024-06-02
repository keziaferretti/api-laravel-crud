<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::orderBy('id', 'DESC')->paginate(2);

        return response()->json([
            'status' => true,
            'users' => $users,
            'message' => 'Users retrieved successfully.'
        ]);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json([
            'status' => true,
            'user' => $user,
            'message' => 'User retrieved successfully.'
        ]);
    }

    public function store(CreateUserRequest $request ): JsonResponse
    {
       DB::beginTransaction();

        try {
            $data = $request->only(['name', 'email', 'password']);
            $data['password'] = Hash::make($data['password']);
            $user = User::create($data);

            DB::commit();

            return response()->json([
                'status' => true,
                'token' => $user->createToken('Register_token')->plainTextToken,
                'user'=> $user,
                'message' => 'User created successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'User creation failed.'
            ]);
        }
    }

    public function update(UpdateUserRequest $request, User $user) : JsonResponse
    {
        DB::beginTransaction();
    
        try {
            $data = $request->only(['name', 'email', 'password']);
            
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }
    
            $user->update($data);
    
            DB::commit();
    
            return response()->json([
                'status' => true,
                'user' => $user,
                'message' => 'User updated successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'status' => false,
                'message' => 'User update failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(User $user): JsonResponse
    {
        DB::beginTransaction();

        try {
            $user->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'User deleted successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'User deletion failed.'
            ]);
        }
    }
}
