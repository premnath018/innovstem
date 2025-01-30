<?php

namespace App\Services;

use App\Repositories\QuizRepository;

class QuizService
{
    protected $quizRepository;

    public function __construct(QuizRepository $quizRepository)
    {
        $this->quizRepository = $quizRepository;
    }

    /**
     * Get all quizzes.
     */
    public function getAllQuizzes()
    {
        return $this->quizRepository->getAll();
    }

    /**
     * Find a quiz by ID.
     */
    public function getQuizById(int $id)
    {
        $quiz = $this->quizRepository->findById($id);

        if (!$quiz) {
            throw new \Exception('Quiz not found.');
        }

        return $quiz;
    }

    /**
     * Find a quiz by slug and type.
     */
    public function getQuizBySlug(string $type, string $slug)
    {
        $quiz = $this->quizRepository->findBySlug($type, $slug);

        if (!$quiz) {
            throw new \Exception('Quiz not found.');
        }

        return $quiz;
    }

    /**
     * Get quizzes without revealing correct answers.
     */
    public function getQuizzesWithoutAnswers(int $id)
    {
        $quiz = $this->quizRepository->getQuizzesWithoutAnswers($id);

        if (!$quiz) {
            throw new \Exception('Quiz not found or unable to process.');
        }

        return $quiz;
    }

    /**
     * Check answers for a quiz.
     */
    public function checkQuizAnswers(int $quizId, array $answers)
    {
        $result = $this->quizRepository->checkAnswers($quizId, $answers);

        return $result;
    }


   
}
