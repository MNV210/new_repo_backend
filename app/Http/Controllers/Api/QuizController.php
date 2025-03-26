<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $quizzes = Quiz::query();
        if($request->has('course_id')){
            $quizzes->where('course_id', $request->course_id)->with('course')->with('questions');
        }
        $quizzes = $quizzes->get();
        // $quizzes = Quiz::with(['course', 'questions'])->get();
        return response()->json([
            'status' => 'success',
            'data' => $quizzes
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'time_limit' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $quiz = Quiz::create($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $quiz
        ], 201);
    }

    public function show($id)
    {
        $quiz = Quiz::with(['course', 'questions'])->find($id);
        
        if (!$quiz) {
            return response()->json([
                'status' => 'error',
                'message' => 'Quiz not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $quiz
        ]);
    }

    public function update(Request $request, $id)
    {
        $quiz = Quiz::find($id);

        if (!$quiz) {
            return response()->json([
                'status' => 'error',
                'message' => 'Quiz not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'course_id' => 'exists:courses,id',
            'title' => 'string|max:255',
            'description' => 'string',
            'time_limit' => 'integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $quiz->update($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $quiz
        ]);
    }

    public function destroy($id)
    {
        $quiz = Quiz::find($id);

        if (!$quiz) {
            return response()->json([
                'status' => 'error',
                'message' => 'Quiz not found'
            ], 404);
        }

        $quiz->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Quiz deleted successfully'
        ]);
    }

    public function submitQuiz(Request $request)
    {
        $quiz = Quiz::with('questions')->find($request->quiz_id);
        
        if (!$quiz) {
            return response()->json([
                'status' => 'error',
                'message' => 'Quiz not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'quiz_id' => 'required',
            // 'answers' => 'required|array',
            // 'time_spent' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Calculate score
        $totalQuestions = $quiz->questions->count();
        $correctAnswers = 0;
        $answers = collect($request->answers);

        foreach ($quiz->questions as $question) {
            if ($answers->has($question->id) && 
                $answers->get($question->id) == $question->correct_answer) {
                $correctAnswers++;
            }
        }

        $score = ($correctAnswers / $totalQuestions) * 100;
        $passed = $score >= 70; // Assuming 70% is passing score

        // Create quiz result
        $result = QuizResult::create([
            'user_id' => $request->user_id,
            'quiz_id' => $quiz->id,
            // 'score' => $score,
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctAnswers,
            'time_spent' => $request->time_spent,
            'passed' => $passed,
            'answers_detail' => $request->answers
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $result
        ]);
    }

    public function getResults($id)
    {
        $quiz = Quiz::find($id);
        
        if (!$quiz) {
            return response()->json([
                'status' => 'error',
                'message' => 'Quiz not found'
            ], 404);
        }

        $results = $quiz->results()->with('user')->get();

        return response()->json([
            'status' => 'success',
            'data' => $results
        ]);
    }

    //tu viet
    public function getResultsWithUser(Request $request)
    {

        $user = $request->user();
        $results = QuizResult::where('user_id',$user->id)->with('user', 'quiz')->get();

        return response()->json([
            'status' => 'success',
            'data' => $results
        ]);
    }
} 