<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatbotConversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatbotConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $conversations = ChatbotConversation::with(['user', 'course', 'lesson'])->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $conversations
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string',
            'message' => 'required|string',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
        $user = $request->user();
        $request->merge(['user_id' => $user->id]);

        $conversation = ChatbotConversation::create($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $conversation
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $conversation = ChatbotConversation::with(['user', 'course', 'lesson'])->find($id);
        
        if (!$conversation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Conversation not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $conversation
        ]);
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
        $conversation = ChatbotConversation::find($id);

        if (!$conversation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Conversation not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'exists:users,id',
            'type' => 'string',
            'message' => 'string',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $conversation->update($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $conversation
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $conversation = ChatbotConversation::find($id);

        if (!$conversation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Conversation not found'
            ], 404);
        }

        $conversation->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Conversation deleted successfully'
        ]);
    }

    /**
     * Get conversations for a specific user.
     *
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function getUserConversations($userId)
    {
        $conversations = ChatbotConversation::with(['course', 'lesson'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $conversations
        ]);
    }

    /**
     * Get conversations for a specific course.
     *
     * @param  int  $courseId
     * @return \Illuminate\Http\Response
     */
    public function getCourseConversations($courseId)
    {
        $conversations = ChatbotConversation::with(['user', 'lesson'])
            ->where('course_id', $courseId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $conversations
        ]);
    }

    /**
     * Get conversation thread between user and bot.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getConversationThread(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'nullable|exists:courses,id',
        ]);

        $user = $request->user();

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = ChatbotConversation::where('user_id', $user->id);
        
        if ($request->has('course_id')) {
            $query->where('course_id', $request->course_id);
        }
        
        $conversations = $query->orderBy('created_at', 'asc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $conversations
        ]);
    }
} 