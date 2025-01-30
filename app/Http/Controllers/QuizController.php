<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\QuizService;
use App\Helpers\ApiResponse;

class QuizController extends Controller
{
    protected $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }

    /**
     * Get quiz questions and options by type and slug.
     */
    public function show(Request $request, string $type, string $slug)
    {
        try {
            $modelType = $this->resolveModelType($type);

            if (!$modelType) {
                return ApiResponse::error('Invalid quiz type.', 400);
            }

            $quiz = $this->quizService->getQuizBySlug($modelType, $slug);

            if (!$quiz) {
                return ApiResponse::error('Quiz not found.', 404);
            }

            $quizData = $this->quizService->getQuizzesWithoutAnswers($quiz->id);

            $quizData = $this->transformQuiz($quizData);


            return ApiResponse::success($quizData, 'Quiz retrieved successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    /**
     * Submit quiz answers and check correctness.
     */
    public function submit(Request $request, string $type, string $slug)
    {
        try {
            $request->validate([
                'answers' => 'required|array',
            ]);

            $modelType = $this->resolveModelType($type);

            if (!$modelType) {
                return ApiResponse::error('Invalid quiz type.', 400);
            }

            $quiz = $this->quizService->getQuizBySlug($modelType, $slug);


            if (!$quiz) {
                return ApiResponse::error('Quiz not found.', 404);
            }

            $result = $this->quizService->checkQuizAnswers($quiz->id, $request->input('answers'));

            return ApiResponse::success($result, 'Quiz results processed successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    /**
     * Resolve the full model class name based on type.
     */
    protected function resolveModelType(string $type): ?string
    {
        $models = [
            'courses' => \App\Models\Course::class,
            'blogs' => \App\Models\Blog::class,
            'webinars' => \App\Models\Webinar::class,
            'resources' => \App\Models\Resource::class,
        ];

        return $models[$type] ?? null;
    }


    protected function transformQuiz($quiz)
    {
        return [
            'id' => $quiz->id,
            'title' => $quiz->title,
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
