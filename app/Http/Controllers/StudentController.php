<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Mail\CourseRegistrationMail;
use App\Mail\WebinarRegistrationMail;
use App\Models\Course;
use App\Models\Webinar;
use App\Services\StudentService;
use Illuminate\Support\Facades\Mail;

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
            $user = $request->user();
            $courseId = $request->input('course_id');
            $this->studentService->enrollInCourse($user->id, $courseId);
            $course_name = Course::where('id', $courseId)->pluck('title')->first();
            Mail::to($user->email)->queue(new CourseRegistrationMail($user,$course_name));
            return ApiResponse::success([], 'Enrolled in course successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    public function attendWebinar(Request $request)
    {
        try {
            $user = $request->user();
            $webinarId = $request->input('webinar_id');
            $this->studentService->attendWebinar($user->id, $webinarId);
            $webinar = Webinar::where('id', $webinarId)->first();
            Mail::to($user->email)->queue(new WebinarRegistrationMail($user,$webinar));
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
