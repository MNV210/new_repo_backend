<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\UserLessonProgress;
use Carbon\Carbon;
use App\Models\ActionHistory; 
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\UserRegisterCourse;

class AnalysController extends Controller
{
    //
    public function analys()
    {
        $totalUsers = User::count();
        $totalCourses = Course::count();

        $currentMonth = Carbon::now()->startOfMonth();
        $previousMonth = Carbon::now()->subMonth()->startOfMonth();

        // Count courses registered in the current month
        $currentMonthRegistrations = UserRegisterCourse::where('created_at', '>=', $currentMonth)
            ->distinct('course_id')
            ->count('course_id');   
        // Count courses registered in the previous month
        $previousMonthRegistrations = UserRegisterCourse::where('created_at', '>=', $previousMonth)
            ->where('created_at', '<', $currentMonth)
            ->distinct('course_id')
            ->count('course_id');


        return response()->json([
            'total_users' => $totalUsers,
            'total_courses' => $totalCourses,
            'registered_courses' => [
                'this_month' => $currentMonthRegistrations,
                'last_month' => $previousMonthRegistrations,
            ],
        ]);
    }

    public function getActionHistory()
    {
        $actionHistory = ActionHistory::with(['user', 'lesson', 'course'])
            ->orderBy('action_time', 'desc')
            ->get();

        return response()->json($actionHistory);
    }

    /**
     * Get the number of users who have completed a specific course.
     *
     * @param  int  $courseId
     * @return \Illuminate\Http\JsonResponse
     */
    public function completedCourseUsers($courseId)
    {
        // Get all lesson IDs for the given course
        $lessonIds = Lesson::where('course_id', $courseId)->pluck('id');

        // Count distinct users who have completed all lessons in the course
        $completedUsersCount = UserLessonProgress::whereIn('lesson_id', $lessonIds)
            ->where('completed', true)
            ->select('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(DISTINCT lesson_id) = ?', [$lessonIds->count()])
            ->get()
            ->count();

        return response()->json([
            'course_id' => $courseId,
            'completed_users' => $completedUsersCount,
        ]);
    }

    /**
     * Get the number of students who completed courses in the current month.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function completedCoursesThisMonth()
    {
        $currentMonth = Carbon::now()->startOfMonth();

        // Get all lessons completed in the current month
        $completedUsersCount = UserLessonProgress::where('completed', true)
            ->where('updated_at', '>=', $currentMonth)
            ->select('user_id', 'lesson_id')
            ->join('lessons', 'user_lesson_progress.lesson_id', '=', 'lessons.id')
            ->groupBy('user_id', 'lessons.course_id')
            ->havingRaw('COUNT(DISTINCT lessons.id) = (SELECT COUNT(*) FROM lessons WHERE lessons.course_id = lessons.course_id)')
            ->get()
            ->count();

        return response()->json([
            'completed_users_this_month' => $completedUsersCount,
        ]);
    }

    /**
     * Get the number of courses registered by users in the current and previous month.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function registeredCoursesByMonth()
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $previousMonth = Carbon::now()->subMonth()->startOfMonth();

        // Count courses registered in the current month
        $currentMonthRegistrations = UserRegisterCourse::where('created_at', '>=', $currentMonth)
            ->distinct('course_id')
            ->count('course_id');   
        // Count courses registered in the previous month
        $previousMonthRegistrations = UserRegisterCourse::where('created_at', '>=', $previousMonth)
            ->where('created_at', '<', $currentMonth)
            ->distinct('course_id')
            ->count('course_id');

        return response()->json([
            'current_month_registrations' => $currentMonthRegistrations,
            'previous_month_registrations' => $previousMonthRegistrations,
        ]);
    }

    /**
     * Get the number of users created each month for the last six months.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function usersCreatedLastSixMonths()
    {
        $startMonth = Carbon::now()->subMonths(6)->startOfMonth();
        $endMonth = Carbon::now()->endOfMonth();

        // Get user creation counts grouped by month for the last 6 months
        $userCounts = User::whereBetween('created_at', [$startMonth, $endMonth])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->month => $item->count];
            });

        // Ensure all months in the range are included, even if count is 0
        $months = collect();
        for ($i = 0; $i <= 6; $i++) {
            $month = Carbon::now()->subMonths(6 - $i)->format('Y-m');
            $months[$month] = $userCounts[$month] ?? 0;
        }

        return response()->json($months);
    }
}
