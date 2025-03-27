<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $question = Question::query();
        if($request->has('quiz_id')){
            $question->where('quiz_id', $request->quiz_id)->with('quiz');
        }
        $questions = $question->get();
        return response()->json([
            'status' => 'success',
            'data' => $questions
        ]);
    }
   
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'quiz_id' => 'required|exists:quizzes,id',
            'question' => 'required|string',
            'option1' => 'required|string',
            'option2' => 'required|string',
            'option3' => 'required|string',
            'option4' => 'required|string',
            'correct_answer' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $question = Question::create($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $question
        ], 201);
    }

    public function show($id)
    {
        $question = Question::with('quiz')->find($id);
        
        if (!$question) {
            return response()->json([
                'status' => 'error',
                'message' => 'Question not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $question
        ]);
    }

    public function update(Request $request, $id)
    {
        $question = Question::find($id);

        if (!$question) {
            return response()->json([
                'status' => 'error',
                'message' => 'Question not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'quiz_id' => 'exists:quizzes,id',
            'question' => 'string',
            'option1' => 'string',
            'option2' => 'string',
            'option3' => 'string',
            'option4' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $question->update($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $question
        ]);
    }

    public function destroy($id)
    {
        $question = Question::find($id);

        if (!$question) {
            return response()->json([
                'status' => 'error',
                'message' => 'Question not found'
            ], 404);
        }

        $question->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Question deleted successfully'
        ]);
    }

    public function getQuizQuestions($quizId)
    {
        $questions = Question::where('quiz_id', $quizId)->get();

        return response()->json([
            'status' => 'success',
            'data' => $questions
        ]);
    }
} 