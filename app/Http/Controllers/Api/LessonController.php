<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LessonController extends Controller
{
    public function index()
    {
        $lessons = Lesson::with(['course'])->get();
        return response()->json([
            'status' => 'success',
            'data' => $lessons
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'file_url' => 'nullable|string',
            'type' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $lesson = Lesson::create($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $lesson
        ], 201);
    }

    public function show($id)
    {
        $lesson = Lesson::with(['course', 'learnProgress'])->find($id);
        
        if (!$lesson) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lesson not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $lesson
        ]);
    }

    public function update(Request $request, $id)
    {
        $lesson = Lesson::find($id);

        if (!$lesson) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lesson not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'course_id' => 'exists:courses,id',
            'title' => 'string|max:255',
            'content' => 'string',
            'file_url' => 'nullable|string',
            'type' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $lesson->update($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $lesson
        ]);
    }

    public function destroy($id)
    {
        $lesson = Lesson::find($id);

        if (!$lesson) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lesson not found'
            ], 404);
        }

        $lesson->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Lesson deleted successfully'
        ]);
    }

    public function getProgress(Request $request, $id)
    {
        $lesson = Lesson::find($id);
        $user_id = $request->user_id;

        if (!$lesson) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lesson not found'
            ], 404);
        }

        $progress = $lesson->learnProgress()
            ->where('user_id', $user_id)
            ->first();

        return response()->json([
            'status' => 'success',
            'data' => $progress
        ]);
    }

    public function updateProgress(Request $request, $id)
    {
        $lesson = Lesson::find($id);
        
        if (!$lesson) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lesson not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'progress' => 'required|integer|min:0|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $progress = $lesson->learnProgress()->updateOrCreate(
            [
                'user_id' => $request->user_id,
                'course_id' => $lesson->course_id
            ],
            ['progress' => $request->progress]
        );

        return response()->json([
            'status' => 'success',
            'data' => $progress
        ]);
    }

    public function getLessonByCourse($course_id)
    {
        $lessons = Lesson::where('course_id', $course_id)->get();
        return response()->json([
            'status' => 'success',
            'data' => $lessons
        ]);
    }
} 