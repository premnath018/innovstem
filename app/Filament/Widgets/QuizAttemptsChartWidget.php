<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\QuizAttempt;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;

class QuizAttemptsChartWidget extends ChartWidget
{
    use HasWidgetShield;

    protected static ?string $heading = 'Monthly Quiz Attempts';
    protected static ?string $description = 'Number of quiz attempts over the last 12 months';

    protected function getData(): array
    {
        $data = QuizAttempt::selectRaw('MONTH(attempted_at) as month, COUNT(id) as attempt_count')
            ->where('attempted_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('attempt_count', 'month')
            ->all();

        return [
            'datasets' => [
                [
                    'label' => 'Quiz Attempts',
                    'data' => $this->fillMonthlyData($data),
                    'borderColor' => '#FF9800',
                    'backgroundColor' => 'rgba(255, 152, 0, 0.2)',
                ],
            ],
            'labels' => $this->getMonthLabels(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }

    protected function getMonthLabels(): array
    {
        return collect(range(11, 0))
            ->map(fn ($i) => Carbon::now()->subMonths($i)->format('M Y'))
            ->all();
    }

    protected function fillMonthlyData(array $data): array
    {
        $filled = [];
        $months = $this->getMonthLabels();
        foreach ($months as $index => $month) {
            $monthNum = Carbon::parse($month)->month;
            $filled[] = $data[$monthNum] ?? 0;
        }
        return $filled;
    }
}