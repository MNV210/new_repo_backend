<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LearnProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LearnProgressController extends Controller
{
    public function index()
    {
        $progress = LearnProgress::with(['user', 'course', 'lesson'])->get();
        return response()->json([
            'status' => 'success',
            'data' => $progress
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lesson_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
        $user = $request->user();


        $progress = LearnProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'lesson_id' => $request->lesson_id,
                'course_id' => $request->course_id
            ],
            ['progress' => 'completed']
        );

        return response()->json([
            'status' => 'success',
            'data' => $progress
        ], 201);
    }

    public function show($id)
    {
        $progress = LearnProgress::with(['user', 'course', 'lesson'])->find($id);
        
        if (!$progress) {
            return response()->json([
                'status' => 'error',
                'message' => 'Progress record not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $progress
        ]);
    }

    public function update(Request $request, $id)
    {
        $progress = LearnProgress::find($id);

        if (!$progress) {
            return response()->json([
                'status' => 'error',
                'message' => 'Progress record not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'progress' => 'required|integer|min:0|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $progress->update($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $progress
        ]);
    }

    public function destroy($id)
    {
        $progress = LearnProgress::find($id);

        if (!$progress) {
            return response()->json([
                'status' => 'error',
                'message' => 'Progress record not found'
            ], 404);
        }

        $progress->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Progress record deleted successfully'
        ]);
    }

    public function getUserProgress($userId)
    {
        $progress = LearnProgress::where('user_id', $userId)
            ->with(['course', 'lesson'])
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $progress
        ]);
    }

    public function getCourseProgress($courseId)
    {
        $progress = LearnProgress::where('course_id', $courseId)
            ->with(['user', 'lesson'])
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $progress
        ]);
    }

    public function getLessonProgress($lessonId)
    {
        $progress = LearnProgress::where('lesson_id', $lessonId)
            ->with(['user', 'course'])
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $progress
        ]);
    }

    public function getLessonProgressUser(Request $request) {
        $user = $request->user();
        $response = LearnProgress::where('user_id',$user->id)->where('course_id',$request->course_id)->get();

        return response()->json([
            'data' => $response
        ]);
    }
} 