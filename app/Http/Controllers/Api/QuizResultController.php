<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QuizResult;

class QuizResultController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $quizResults = QuizResult::all();
        return response()->json($quizResults);
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
            'quiz_id' => 'required|integer',
            'score' => 'required|numeric',
        ]);

        // Assuming user_id is passed in the request
        $validatedData['user_id'] = $request->user()->id;
        $validatedData['total_questions'] = $request->input('total_questions', 0);
        // $validatedData['passed'] = $request->input('passed', false);
        $validatedData['answers_detail'] = $request->input('answers', []);        
        // Create the quiz result
        // You can also add additional logic here to calculate the score, etc.
        // For example, if you want to check if the score is above a certain threshold

        $quizResult = QuizResult::create($validatedData);
        return response()->json($quizResult, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $quizResult = QuizResult::find($id);

        if (!$quizResult) {
            return response()->json(['message' => 'Quiz result not found'], 404);
        }

        return response()->json($quizResult);
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
        $quizResult = QuizResult::find($id);

        if (!$quizResult) {
            return response()->json(['message' => 'Quiz result not found'], 404);
        }

        $validatedData = $request->validate([
            'user_id' => 'sometimes|integer',
            'quiz_id' => 'sometimes|integer',
            'score' => 'sometimes|numeric',
        ]);

        $quizResult->update($validatedData);
        return response()->json($quizResult);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $quizResult = QuizResult::find($id);

        if (!$quizResult) {
            return response()->json(['message' => 'Quiz result not found'], 404);
        }

        $quizResult->delete();
        return response()->json(['message' => 'Quiz result deleted successfully']);
    }
}
