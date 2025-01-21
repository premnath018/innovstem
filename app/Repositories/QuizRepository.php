<?php

namespace App\Repositories;

use App\Models\Quiz;

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
        return $this->quiz
            ->whereHasMorph('quizable', [$type], function ($query) use ($slug) {
                $query->where('slug', '=', $slug);
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
                'success' => false,
                'message' => 'Quiz not found.',
            ];
        }

        $score = 0;
        $totalQuestions = $quiz->questions->count();

        foreach ($quiz->questions as $question) {
            if (isset($answers[$question->id])) {
                $correctOption = $question->options->firstWhere('is_correct', true);

                if ($correctOption && $correctOption->id == $answers[$question->id]) {
                    $score++;
                }
            }
        }

        return [
            'success' => true,
            'message' => 'Quiz answers evaluated.',
            'score' => $score,
            'total' => $totalQuestions,
        ];
    }
}
