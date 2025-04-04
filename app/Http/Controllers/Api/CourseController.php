<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\UserRegisterCourse;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with(['teacher', 'lessons','category'])->get();
        return response()->json([
            'status' => 'success',
            'data' => $courses
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'thumbnail' => 'nullable|string',
            'file_url' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $course = Course::create([
            'teacher_id' => $request->teacher_id,
            'title' => $request->title,
            'description' => $request->description,
            'thumbnail' => $request->thumbnail,
            'file_url' => $request->file_url,
            'slug' => Str::slug($request->title),
            'level' => $request->level,
            'category_id' => $request->category_id
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $course
        ], 201);
    }

    public function show($id)
    {
        $course = Course::with(['teacher', 'lessons', 'quizzes'])->find($id);
        
        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'Course not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $course
        ]);
    }

    public function update(Request $request, $id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'Course not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'teacher_id' => 'exists:users,id',
            'title' => 'string|max:255',
            'description' => 'string',
            'thumbnail' => 'nullable|string',
            'file_url' => 'nullable|string',
            'level' => 'in:beginner,intermediate,advanced'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->has('title')) {
            $request->merge(['slug' => Str::slug($request->title)]);
        }

        $course->update($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $course
        ]);
    }

    public function destroy($id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'Course not found'
            ], 404);
        }

        // Delete related user registrations
        UserRegisterCourse::where('course_id', $id)->delete();

        //delete all lessons of this course
        $course->lessons()->delete();
        // Delete related quizzes and their questions
        $course->quizzes()->each(function ($quiz) {
            $quiz->questions()->delete();
            $quiz->results()->delete(); // Delete related quiz results
            $quiz->delete();
        });

        // Delete related learn progress
        $course->learnProgress()->delete();

        // Delete the course
        $course->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Course and related data deleted successfully'
        ]);
    }


    public function enroll(Request $request, $id)
    {
        $course = Course::find($id);
        $user_id = $request->user_id;

        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'Course not found'
            ], 404);
        }

        if ($course->students()->where('user_id', $user_id)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'User already enrolled in this course'
            ], 422);
        }

        $course->students()->attach($user_id, ['enrolled_at' => now()]);

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully enrolled in course'
        ]);
    }

    public function getCourseUserRegister(Request $requets)
    {
        $user = $requets->user();
        $courseRegister = UserRegisterCourse::where('user_id', $user->id)->with('course')->get();
        $courseIds = UserRegisterCourse::where('user_id', $user->id)
                        ->pluck('course_id');
        $courses = Course::whereIn('id', $courseIds)->with('lessons')->with('learnProgress')->get();
        return response()->json([
            'status' => 'success',
            'data' => [
                'courseUserRegister'=>$courseRegister,
                'course'=>$courses
            ]
        ]);
    }

    public function checkUserRegisterCourse(Request $request) {
        $user = $request->user();

        $checkRegister  = UserRegisterCourse::where('user_id',$user->id)->where('course_id',$request->course_id)->first();

        return response()->json([
            'data' => $checkRegister,
        ]);
    }

    public function getCourseUserCreate(Request $request) {
        $user = $request->user();

        if ($user->role === 'admin') {
            $courses = Course::with('lessons')->with('teacher')->get();
        } else {
            $courses = Course::where('teacher_id', $user->id)->with('lessons')->with('teacher')->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $courses
        ]);
    }
}