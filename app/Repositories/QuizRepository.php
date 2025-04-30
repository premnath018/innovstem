<?php

namespace App\Repositories;

use App\Models\Quiz;
use App\Models\QuizAttempt;

class QuizRepository
{
    protected $quiz;

    public function __construct(Quiz $quiz)
    {
        $this->quiz = $quiz;
    }

    /**
     * Get all quizzes.
     */
    public function getAll()
    {
        return $this->quiz->with('questions.options')->get();
    }

    /**
     * Find a quiz by its ID.
     */
    public function findById(int $id)
    {
        return $this->quiz->with('questions.options')->find($id);
    }

    /**
     * Find a quiz by slug and type.
     */
    public function findBySlug(string $type, string $slug)
    {
        $model = app($type);

        // Determine the correct slug column name dynamically
        $slugColumn = match ($type) {
            \App\Models\Course::class => 'course_slug',
            \App\Models\Blog::class => 'blog_slug',
            \App\Models\Webinar::class => 'webinar_slug',
            \App\Models\Resource::class => 'resource_slug',
            default => 'slug'
        };

        return $this->quiz
            ->whereHasMorph('quizable', [$type], function ($query) use ($slugColumn, $slug) {
                $query->where($slugColumn, '=', $slug);
            })
            ->with('questions.options')
            ->first();
    }


    /**
     * Get quizzes without revealing correct answers.
     */
    public function getQuizzesWithoutAnswers(int $id)
    {
        $quiz = $this->findById($id);

        if (!$quiz) {
            return null;
        }

        // Remove the `is_correct` attribute
        $quiz->questions->each(function ($question) {
            $question->options->each(function ($option) {
                unset($option->is_correct);
            });
        });

        return $quiz;
    }

    /**
     * Check answers for a quiz.
     */
    public function checkAnswers(int $quizId, array $answers): array
    {
        $quiz = $this->findById($quizId);
    
        if (!$quiz) {
            return [
                'message' => 'Quiz not found.',
            ];
        }
    
        // ✅ Get the logged-in user and their student ID
        $user = auth()->user();
      //  dd($user);
        $studentId = $user?->student?->id;
    
        if (!$studentId) {
            return [
                'message' => 'Student profile not found.',
            ];
        }
    
        $score = 0;
        $totalQuestions = $quiz->questions->count();
    
        // ✅ Calculate the score
        foreach ($quiz->questions as $question) {
            if (isset($answers[$question->id])) {
                $correctOption = $question->options->firstWhere('is_correct', true);
    
                if ($correctOption && $correctOption->id == $answers[$question->id]) {
                    $score++;
                }
            }
        }
    
        $score = round(($score / $totalQuestions) * 100);
        $quizAttempt = QuizAttempt::where('student_id', $studentId)
            ->where('quiz_id', $quizId)
            ->first();
    
        if ($quizAttempt) {
            $quizAttempt->update(['score' => $score]);
        } else {
            QuizAttempt::create([
                'student_id' => $studentId,
                'quiz_id' => $quizId,
                'score' => $score
            ]);
        }
    
        return [
            'score' => $score,
            'total' => $totalQuestions,
        ];
    }
    

    protected function transformQuiz($quiz)
    {
        return [
            'id' => $quiz->id,
            'title' => $quiz->title,
            'number_of_questions' => $quiz->questions->count(),
            'quizable_id' => $quiz->quizable_id,
            'questions' => $quiz->questions->map(function ($question) {
                return [
                    'id' => $question->id,
                    'question_text' => $question->question_text,
                    'options' => $question->options->map(function ($option) {
                        return [
                            'id' => $option->id,
                            'option_text' => $option->option_text,
                        ];
                    }),
                ];
            }),
        ];
    }

}
