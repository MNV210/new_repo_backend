<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\LessonController;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\ChatbotConversationController;
use App\Http\Controllers\Api\LearnProgressController;
use App\Http\Controllers\Api\UserRegisterCourseController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\QuizResultController;
use App\Http\Controllers\Api\AnalysController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::get('auth/me', [UserController::class, 'getInfomationUser']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // Auth routes
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/profile', [AuthController::class, 'profile']);

        // User Management
        Route::apiResource('users', UserController::class);
        Route::prefix('users')->group(function () {
            Route::get('{id}/enrolled-courses', [UserController::class, 'enrolledCourses']);
            Route::get('{id}/teacher-courses', [UserController::class, 'teacherCourses']);
        });

        // Course Management
        Route::apiResource('courses', CourseController::class);
        Route::prefix('courses')->group(function () {
            Route::get('{id}/enrolled-students', [CourseController::class, 'enrolledStudents']);
            Route::post('{id}/enroll', [CourseController::class, 'enroll']);
            Route::post('{id}/unenroll', [CourseController::class, 'unenroll']);
            Route::post('user_register', [CourseController::class, 'getCourseUserRegister']);
            Route::post('check_register',[CourseController::class,'checkUserRegisterCourse']);
            Route::post('user_create',[CourseController::class,'getCourseUserCreate']);
        });

        //Quỉzet Result Management
        Route::apiResource('quiz-results', QuizResultController::class);

        // Lesson Management
        Route::apiResource('lessons', LessonController::class);
        Route::prefix('lessons')->group(function () {
            Route::get('{id}/progress', [LessonController::class, 'getProgress']);
            Route::post('{id}/progress', [LessonController::class, 'updateProgress']);
            Route::get('course/{courseId}', [LessonController::class, 'getLessonByCourse']);
        });

        // Quiz Management
        Route::apiResource('quizzes', QuizController::class);
        Route::prefix('quizzes')->group(function () {
            Route::post('{id}/submit', [QuizController::class, 'submitQuiz']);
            Route::get('{id}/results', [QuizController::class, 'getResults']);
        });
        Route::post('quizz/user_results', [QuizController::class, 'getResultsWithUser']);

        // Question Management
        Route::apiResource('questions', QuestionController::class);
        Route::get('quiz/{quizId}/questions', [QuestionController::class, 'getQuizQuestions']);

        // Chatbot Conversations
        Route::apiResource('chatbot-conversations', ChatbotConversationController::class);
        Route::prefix('chatbot')->group(function () {
            Route::get('users/{userId}/conversations', [ChatbotConversationController::class, 'getUserConversations']);
            Route::get('courses/{courseId}/conversations', [ChatbotConversationController::class, 'getCourseConversations']);
            Route::post('conversation-thread', [ChatbotConversationController::class, 'getConversationThread']);
        });

        // Learning Progress
        Route::apiResource('learn-progress', LearnProgressController::class);
        Route::prefix('progress')->group(function () {
            Route::get('users/{userId}', [LearnProgressController::class, 'getUserProgress']);
            Route::get('courses/{courseId}', [LearnProgressController::class, 'getCourseProgress']);
            Route::get('lessons/{lessonId}', [LearnProgressController::class, 'getLessonProgress']);
            Route::post('user_progress',[LearnProgressController::class,'getLessonProgressUser']);
        });

        Route::apiResource('/categories',CategoryController::class);
        Route::apiResource('/user_register_course',UserRegisterCourseController::class);
        Route::get('/not_student', [UserController::class, 'getUserNotStudent']);

        //Thống kê
        Route::get('/analys', [AnalysController::class, 'analys']);
        Route::get('/action-history', [AnalysController::class, 'getActionHistory']);
        Route::get('/register_course_in_month', [AnalysController::class, 'registeredCoursesByMonth']);
        Route::get('/user_in_6_month', [AnalysController::class, 'usersCreatedLastSixMonths']);


    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
