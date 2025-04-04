<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required',
            'avatar' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $avatarUrl = $request->avatar;

        if ($request->has('avatar')) {
            $response = Http::post('http://localhost:8001/upload', [
                'file' => $request->avatar
            ]);

            if ($response->successful()) {
                $avatarUrl = $response->json('url');
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to upload avatar'
                ], 500);
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            // 'avatar' => $avatarUrl
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $user
        ], 201);
    }

    public function show($id)
    {
        $user = User::with(['teacherCourses', 'enrolledCourses', 'quizResults'])->find($id);
        
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'avatar' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->has('password')) {
            $request->merge([
                'password' => Hash::make($request->password)
            ]);
        }

        $user->update($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }
        

        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully'
        ]);
    }

    public function enrolledCourses($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $user->enrolledCourses
        ]);
    }

    public function teacherCourses($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        if ($user->role !== 'instructor') {
            return response()->json([
                'status' => 'error',
                'message' => 'User is not an instructor'
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $user->teacherCourses
        ]);
    }

    public function getUserNotStudent()
    {
        $users = User::where('role', '!=', 'student')->get();
        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }

    public function getInfomationUser(Request $request)
    {
        $id = $request->user()->id;
        $user = User::with(['teacherCourses', 'enrolledCourses', 'quizResults'])->find($id);
        
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }
}