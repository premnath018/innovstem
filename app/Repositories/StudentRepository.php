<?php

namespace App\Repositories;

use App\Models\Student;

class StudentRepository
{
    protected $student;

    public function __construct(Student $student)
    {
        $this->student = $student;
    }

    public function create(array $data)
    {
        return $this->student->create($data);
    }

    public function findByUserId(int $userId)
    {
        return $this->student->where('user_id', $userId)->first();
    }

    public function getEnrolledCourses(int $studentId)
    {
        return $this->student->find($studentId)->enrolledCourses()->with('course')->get();
    }

    public function getAttendedWebinars(int $studentId)
    {
        return $this->student->find($studentId)->attendedWebinars()->with('webinar')->get();
    }

    public function getQuizAttempts(int $studentId)
    {
        return $this->student->find($studentId)->quizAttempts()->with('quiz')->get();
    }

    public function enrollInCourse(int $studentId, int $courseId)
    {
        return $this->student->find($studentId)->enrolledCourses()->create(['course_id' => $courseId]);
    }

    public function attendWebinar(int $studentId, int $webinarId)
    {
        return $this->student->find($studentId)->attendedWebinars()->create(['webinar_id' => $webinarId]);
    }

    public function attemptQuiz(int $studentId, int $quizId, int $score)
    {
        return $this->student->find($studentId)->quizAttempts()->create([
            'quiz_id' => $quizId,
            'score' => $score
        ]);
    }
}
