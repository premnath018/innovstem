<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\CourseEnrollment;
use Carbon\Carbon;

class CourseEnrollmentsChartWidget extends ChartWidget
{



    protected static ?string $heading = 'Monthly Course Enrollments';
    protected static ?string $description = 'Enrollments over the last 12 months';

    protected function getData(): array
    {
        $data = CourseEnrollment::selectRaw('MONTH(enrolled_at) as month, COUNT(id) as count')
            ->where('enrolled_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->all();

        return [
            'datasets' => [
                [
                    'label' => 'Enrollments',
                    'data' => $this->fillMonthlyData($data),
                    'borderColor' => '#4CAF50',
                    'backgroundColor' => 'rgba(76, 175, 80, 0.2)',
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