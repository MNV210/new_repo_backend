<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserRegisterCourse; // Assuming this model exists
use App\Models\ActionHistory; // Ensure this model is imported

class UserRegisterCourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses = UserRegisterCourse::all();
        return response()->json($courses);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'course_id' => 'required|integer',
        ]);

        $user = $request->user();

        $validatedData['user_id'] = $user->id;

        $course = UserRegisterCourse::create($validatedData);

        ActionHistory::create([
            'user_id' => $user->id,
            'course_id' => $request->course_id,
            'action_details' => "Đăng ký khóa học"
        ]);

        return response()->json($course, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $course = UserRegisterCourse::find($id);

        if (!$course) {
            return response()->json(['message' => 'Resource not found'], 404);
        }

        return response()->json($course);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $course = UserRegisterCourse::find($id);

        if (!$course) {
            return response()->json(['message' => 'Resource not found'], 404);
        }

        $validatedData = $request->validate([
            'user_id' => 'sometimes|integer',
            'course_id' => 'sometimes|integer',
        ]);

        $course->update($validatedData);
        return response()->json($course);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $course = UserRegisterCourse::find($id);

        if (!$course) {
            return response()->json(['message' => 'Resource not found'], 404);
        }

        $course->delete();
        return response()->json(['message' => 'Resource deleted successfully']);
    }
}
