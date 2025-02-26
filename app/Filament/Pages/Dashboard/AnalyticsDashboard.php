<?php

namespace App\Filament\Pages\Dashboard;

use Filament\Pages\Dashboard;
use App\Filament\Widgets\PlatformStatsWidget;
use App\Filament\Widgets\CourseEnrollmentsChartWidget;
use App\Filament\Widgets\WebinarAttendanceChartWidget;
use App\Filament\Widgets\QuizAttemptsChartWidget;
use App\Filament\Widgets\TopCoursesWidget;
use App\Filament\Widgets\TopBlogsWidget;
use App\Filament\Widgets\TopWebinarsWidget;
use App\Filament\Widgets\UserGrowthChartWidget;

class AnalyticsDashboard extends Dashboard
{
    
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $title = 'Analytics Dashboard';
    protected static ?string $navigationLabel = 'Analytics';
    protected static string $routePath = 'analytics';

    public static function getNavigationGroup(): ?string
    {
        return 'Dashboards';
    }

    public function getWidgets(): array
    {
        return [
            PlatformStatsWidget::class,
            UserGrowthChartWidget::class,
            CourseEnrollmentsChartWidget::class,
            WebinarAttendanceChartWidget::class,
            QuizAttemptsChartWidget::class,
            TopCoursesWidget::class,
            TopBlogsWidget::class,
            TopWebinarsWidget::class,
        ];
    }
}