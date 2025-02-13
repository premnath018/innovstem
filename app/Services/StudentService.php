<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Webinar;
use App\Repositories\StudentRepository;

class StudentService
{
    protected $studentRepository;

    public function __construct(StudentRepository $studentRepository)
    {
        $this->studentRepository = $studentRepository;
    }

    public function getEnrolledCourses(int $userId)
    {
        $student = $this->studentRepository->findByUserId($userId);
        return $this->transformEnrolledCourses($this->studentRepository->getEnrolledCourses($student->id));
    }

    public function getAttendedWebinars(int $userId)
    {
        $student = $this->studentRepository->findByUserId($userId);
        return $this->transformEnrolledWebinars($this->studentRepository->getAttendedWebinars($student->id));
    }

    public function getQuizAttempts(int $userId)
    {
        $student = $this->studentRepository->findByUserId($userId);
        return $this->studentRepository->getQuizAttempts($student->id);
    }

    public function enrollInCourse(int $userId, int $courseId)
    {
        $student = $this->studentRepository->findByUserId($userId);
        return $this->studentRepository->enrollInCourse($student->id, $courseId);
    }

    public function attendWebinar(int $userId, int $webinarId)
    {
        $student = $this->studentRepository->findByUserId($userId);
        return $this->studentRepository->attendWebinar($student->id, $webinarId);
    }

    public function attemptQuiz(int $userId, int $quizId, int $score)
    {
        $student = $this->studentRepository->findByUserId($userId);
        return $this->studentRepository->attemptQuiz($student->id, $quizId, $score);
    }

    protected function transformEnrolledWebinars($enrolledWebinars): array
    {
        $transformedWebinars = [];
    
        foreach ($enrolledWebinars as $enrollment) {
            $transformedWebinars[] = $this->transformWebinar(Webinar::find($enrollment->webinar_id));
        }
    
        return $transformedWebinars;
    }

    protected function transformWebinar($webinar)
    {
        return [
            'id' => $webinar->id,
            'webinar_slug' => $webinar->webinar_slug,
            'title' => $webinar->title,
            'webinar_description' => $webinar->webinar_description,
            'webinar_thumbnail' => $webinar->webinar_thumbnail,
            'category_name' => $webinar->category->name ?? null,
            'webinar_date_time' => $webinar->webinar_date_time,
            'view_count' => $webinar->view_count,
            'created_by' => $webinar->created_by,
            'view_count' => $webinar->view_count,
            'created_at' => $webinar->created_at->toIso8601String(),
            'updated_at'=> $webinar->updated_at->toIso8601String(),
        ];
    }
    
    protected function transformEnrolledCourses($enrolledCourses): array
    {
        $transformedCourses = [];
    
        foreach ($enrolledCourses as $enrollment) {
            $transformedCourses[] = $this->transformCourse(Course::find($enrollment->course_id),$enrollment->student_id);
        }
    
        return $transformedCourses;
    }
    

    protected function transformCourse($course, ?int $studentId = null)
    {
        return [
            'id' => $course->id,
            'course_slug' => $course->course_slug,
            'title' => $course->title,
            'content_short_description' => $course->content_short_description,
            'course_thumbnail' => $course->course_thumbnail,
            'category_name' => $course->category->name ?? null,
            'created_by' => $course->created_by,
            'class_level_name' => $course->classLevel->name ?? null,
            'view_count' => $course->view_count,
            'enrolment_count' => $course->enrolment_count,
            'created_at' => $course->created_at->toIso8601String(),
            'updated_at'=> $course->updated_at->toIso8601String(),
            'user_registered' => true,
            'quiz_score' => $this->getQuizScore($studentId, $course->id) ?? null,
        ];
    }

    protected function getQuizScore(int $studentId, int $courseId)
    {
        $quizAttempt = \App\Models\QuizAttempt::whereHas('quiz.quizable', function ($query) use ($courseId) {
            $query->where('id', $courseId);
        })->where('student_id', $studentId)->first();

        return $quizAttempt?->score ?? null;
    }


}
