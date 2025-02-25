<?php

namespace App\Repositories;

use App\Models\Student;
use Illuminate\Support\Facades\DB;
use App\Models\Course;
use App\Models\Webinar;

class StudentRepository
{
    protected $student;

    public function __construct(Student $student)
    {
        $this->student = $student;
    }

    public function create(array $data)
    {
        $data['active'] = $data['active'] ?? true; // Ensure active is set by default
        return $this->student->create($data);
    }

    public function findByUserId(int $userId)
    {
        return $this->student->where('user_id', $userId)
            ->where('active', true)
            ->first();
    }

    public function getEnrolledCourses(int $studentId)
    {
        $student = $this->student->where('id', $studentId)->where('active', true)->first();
        return $student ? $student->enrolledCourses()->with('course')->get() : collect([]);
    }

    public function getAttendedWebinars(int $studentId)
    {
        $student = $this->student->where('id', $studentId)->where('active', true)->first();
        return $student ? $student->attendedWebinars()->with('webinar')->get() : collect([]);
    }

    public function getQuizAttempts(int $studentId)
    {
        $student = $this->student->where('id', $studentId)->where('active', true)->first();
        return $student ? $student->quizAttempts()->with('quiz')->get() : collect([]);
    }
    
    public function enrollInCourse(int $studentId, int $courseId)
    {
        return DB::transaction(function () use ($studentId, $courseId) {
            $student = $this->student->where('id', $studentId)->where('active', true)->first();
    
            if (!$student) {
                return null;
            }
    
            // Enroll the student in the course
            $enrollment = $student->enrolledCourses()->create(['course_id' => $courseId]);
    
            if ($enrollment) {
                // Increment the enrollment count for the course
                Course::where('id', $courseId)->increment('enrolment_count');
            }
    
            return $enrollment;
        });
    }
    
    public function attendWebinar(int $studentId, int $webinarId)
    {
        return DB::transaction(function () use ($studentId, $webinarId) {
            $student = $this->student->where('id', $studentId)->where('active', true)->first();
    
            if (!$student) {
                return null;
            }
    
            // Mark student attendance for the webinar
            $attendance = $student->attendedWebinars()->create(['webinar_id' => $webinarId]);
    
            if ($attendance) {
                // Increment the attendance count for the webinar
                Webinar::where('id', $webinarId)->increment('attendance_count');
            }
    
            return $attendance;
        });
    }
    
    public function attemptQuiz(int $studentId, int $quizId, int $score)
    {
        $student = $this->student->where('id', $studentId)->where('active', true)->first();
        return $student ? $student->quizAttempts()->create([
            'quiz_id' => $quizId,
            'score' => $score
        ]) : null;
    }
}
