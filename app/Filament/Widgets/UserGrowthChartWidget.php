<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;

class UserGrowthChartWidget extends ChartWidget
{
    use HasWidgetShield;

    protected static ?string $heading = 'User Growth Over Time';

    protected static ?string $description = 'Number of users registered over the last 6 months';

    protected function getData(): array
    {
        // Fetch last 6 months
        $months = collect(range(5, 0))->map(fn ($i) => Carbon::now()->subMonths($i)->format('M Y'));

        // Fetch user count per month
        $userRegistrations = User::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->pluck('count', 'month');

        return [
            'datasets' => [
                [
                    'label' => 'New Users',
                    'data' => $months->map(fn ($m) => $userRegistrations->get(Carbon::parse($m)->format('n'), 0)),
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'fill' => true,
                ],
            ],
            'labels' => $months->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
