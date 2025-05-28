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
     * Get all active quizzes.
     */
    public function getAll()
    {
        return $this->quiz->where('is_active', true)
            ->with('questions.options')
            ->get();
    }

    /**
     * Find an active quiz by its ID.
     */
    public function findById(int $id)
    {
        return $this->quiz->where('is_active', true)
            ->with('questions.options')
            ->find($id);
    }

    /**
     * Find an active quiz by slug and type.
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
            ->where('is_active', true)
            ->whereHasMorph('quizable', [$type], function ($query) use ($slugColumn, $slug) {
                $query->where($slugColumn, '=', $slug);
            })
            ->with('questions.options')
            ->first();
    }

    /**
     * Get an active quiz without revealing correct answers, with optional question shuffling.
     */
    public function getQuizzesWithoutAnswers(int $id)
    {
        $quiz = $this->findById($id);

        if (!$quiz) {
            return null;
        }

        // Shuffle questions if mix is true
        if ($quiz->mix) {
            $quiz->questions = $quiz->questions->shuffle();
        }

        // Remove the `is_correct` attribute
        $quiz->questions->each(function ($question) {
            $question->options->each(function ($option) {
                unset($option->is_correct);
            });
        });

        return $this->transformQuiz($quiz);
    }

    /**
     * Check answers for a quiz and store attempt details.
     */
    public function checkAnswers(int $quizId, array $answers): array
    {
        $quiz = $this->findById($quizId);

        if (!$quiz) {
            return [
                'message' => 'Quiz not found.',
                'success' => false,
            ];
        }

        if (!$quiz->is_active) {
            return [
                'message' => 'This quiz is currently inactive.',
                'success' => false,
            ];
        }

        // Get the logged-in user and their student ID
        $user = auth()->user();
        $studentId = $user?->student?->id;

        if (!$studentId) {
            return [
                'message' => 'Student profile not found.',
                'success' => false,
            ];
        }

        $correctAnswers = 0;
        $totalQuestions = $quiz->questions->count();
        $incorrectAnswers = 0;

        // Calculate the score and count correct/incorrect answers
        foreach ($quiz->questions as $question) {
            if (isset($answers[$question->id])) {
                $correctOption = $question->options->firstWhere('is_correct', true);

                if ($correctOption && $correctOption->id == (int)$answers[$question->id]) {
                    $correctAnswers++;
                } else {
                    $incorrectAnswers++;
                }
            } else {
                $incorrectAnswers++; // Unanswered questions count as incorrect
            }
        }

        // Calculate score as percentage
        $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;

        // Update or create quiz attempt
        $quizAttempt = QuizAttempt::where('student_id', $studentId)
            ->where('quiz_id', $quizId)
            ->first();

        if ($quizAttempt) {
            if (!$quiz->retry) {
                return [
                    'message' => 'Retries are not allowed for this quiz.',
                    'success' => false,
                    'score' => $quizAttempt->score,
                    'correct_answers' => $quizAttempt->correct_answers,
                    'incorrect_answers' => $quizAttempt->incorrect_answers,
                    'total_questions' => $totalQuestions,
                    'retry_allowed' => $quiz->retry,
                ];
            }
            $quizAttempt->update([
                'score' => $score,
                'correct_answers' => $correctAnswers,
                'incorrect_answers' => $incorrectAnswers,
                'attempted_at' => now(),
            ]);
        } else {
            $quizAttempt = QuizAttempt::create([
                'student_id' => $studentId,
                'quiz_id' => $quizId,
                'score' => $score,
                'correct_answers' => $correctAnswers,
                'incorrect_answers' => $incorrectAnswers,
                'attempted_at' => now(),
            ]);
        }

        return [
            'success' => true,
            'score' => $score,
            'correct_answers' => $correctAnswers,
            'incorrect_answers' => $incorrectAnswers,
            'total_questions' => $totalQuestions,
            'retry_allowed' => $quiz->retry,
            'message' => 'Quiz attempt recorded successfully.',
        ];
    }

    /**
     * Transform quiz data for API response.
     */
    protected function transformQuiz($quiz)
    {
        return [
            'id' => $quiz->id,
            'title' => $quiz->title,
            'number_of_questions' => $quiz->questions->count(),
            'quizable_id' => $quiz->quizable_id,
            'retry' => $quiz->retry,
            'mix' => $quiz->mix,
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