<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Services\StudentService;

class StudentController extends Controller
{
    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    public function enrolledCourses(Request $request)
    {
        try {
            $userId = $request->user()->id;
            $courses = $this->studentService->getEnrolledCourses($userId);
            return ApiResponse::success($courses, 'Enrolled courses retrieved successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    public function attendedWebinars(Request $request)
    {
        try {
            $userId = $request->user()->id;
            $webinars = $this->studentService->getAttendedWebinars($userId);
            return ApiResponse::success($webinars, 'Attended webinars retrieved successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    public function quizAttempts(Request $request)
    {
        try {
            $userId = $request->user()->id;
            $quizzes = $this->studentService->getQuizAttempts($userId);
            return ApiResponse::success($quizzes, 'Quiz attempts retrieved successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    public function enrollCourse(Request $request)
    {
        try {
            $userId = $request->user()->id;
            $courseId = $request->input('course_id');
            $this->studentService->enrollInCourse($userId, $courseId);
            return ApiResponse::success([], 'Enrolled in course successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    public function attendWebinar(Request $request)
    {
        try {
            $userId = $request->user()->id;
            $webinarId = $request->input('webinar_id');
            $this->studentService->attendWebinar($userId, $webinarId);
            return ApiResponse::success([], 'Webinar attended successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    public function attemptQuiz(Request $request)
    {
        try {
            $userId = $request->user()->id;
            $quizId = $request->input('quiz_id');
            $score = $request->input('score');
            $this->studentService->attemptQuiz($userId, $quizId, $score);
            return ApiResponse::success([], 'Quiz attempt recorded successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }
}
