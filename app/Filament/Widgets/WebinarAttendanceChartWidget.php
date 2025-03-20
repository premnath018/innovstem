<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\WebinarAttendance;
use Carbon\Carbon;

class WebinarAttendanceChartWidget extends ChartWidget
{

    protected static ?string $pollingInterval = null;


    protected static ?string $heading = 'Monthly Webinar Attendance';
    protected static ?string $description = 'Attendance over the last 12 months';

    protected function getData(): array
    {
        $data = WebinarAttendance::selectRaw('MONTH(attended_at) as month, COUNT(id) as count')
            ->where('attended_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->all();

        return [
            'datasets' => [
                [
                    'label' => 'Attendees',
                    'data' => $this->fillMonthlyData($data),
                    'backgroundColor' => '#2196F3',
                ],
            ],
            'labels' => $this->getMonthLabels(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
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