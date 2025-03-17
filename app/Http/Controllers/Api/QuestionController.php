<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = Question::with('quiz')->get();
        return response()->json([
            'status' => 'success',
            'data' => $questions
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'quiz_id' => 'required|exists:quizzes,id',
            'question_text' => 'required|string',
            'question1' => 'required|string',
            'question2' => 'required|string',
            'question3' => 'required|string',
            'question4' => 'required|string',
            'correct_answer' => 'required|integer|min:1|max:4'
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
            'question_text' => 'string',
            'question1' => 'string',
            'question2' => 'string',
            'question3' => 'string',
            'question4' => 'string',
            'correct_answer' => 'integer|min:1|max:4'
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