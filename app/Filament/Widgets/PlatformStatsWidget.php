<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use App\Models\User;
use App\Models\Student;
use App\Models\Course;
use App\Models\Webinar;
use App\Models\Blog;
use App\Models\Quiz;

class PlatformStatsWidget extends StatsOverviewWidget
{


    protected static ?string $pollingInterval = null;
    protected  ?string $heading = 'Platform Statistics';
    protected  ?string $description = 'Key metrics overview';

    protected function getStats(): array
    {
        return [
            StatsOverviewWidget\Stat::make('Total Users', User::count())
                ->description('Registered users')
                ->icon('heroicon-o-user-group')
                ->color('primary'),
            StatsOverviewWidget\Stat::make('Total Students', Student::count())
                ->description('Active students')
                ->icon('heroicon-o-academic-cap')
                ->color('success'),
            StatsOverviewWidget\Stat::make('Total Courses', Course::count())
                ->description('Available courses')
                ->icon('heroicon-o-user-group')
                ->color('warning'),
            StatsOverviewWidget\Stat::make('Total Webinars', Webinar::count())
                ->description('Scheduled webinars')
                ->icon('heroicon-o-video-camera')
                ->color('info'),
            StatsOverviewWidget\Stat::make('Total Blogs', Blog::count())
                ->description('Published articles')
                ->icon('heroicon-o-document-text')
                ->color('gray'),
            StatsOverviewWidget\Stat::make('Total Quizzes', Quiz::count())
                ->description('Created quizzes')
                ->icon('heroicon-o-queue-list')
                ->color('danger'),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }
}