<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use App\Models\User;
use App\Models\Student;
use App\Models\Course;
use App\Models\Webinar;
use App\Models\Blog;
use App\Models\Quiz;
use App\Models\CounselingPackage;
use App\Models\Slot;
use App\Models\Appointment;

class PlatformStatsWidget extends StatsOverviewWidget
{
    protected static ?string $pollingInterval = null;
    protected ?string $heading = 'Platform Statistics';
    protected ?string $description = 'Key metrics overview';

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
                ->icon('heroicon-o-book-open')
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
            StatsOverviewWidget\Stat::make('Total Packages', CounselingPackage::where('active', true)->count())
                ->description('Active counseling packages')
                ->icon('heroicon-o-briefcase')
                ->color('primary'),
            StatsOverviewWidget\Stat::make('Total Slots', Slot::where('is_active', true)->count())
                ->description('Available counseling slots')
                ->icon('heroicon-o-calendar')
                ->color('success'),
            StatsOverviewWidget\Stat::make('Total Appointments', Appointment::where('active', true)->count())
                ->description('Booked appointments')
                ->icon('heroicon-o-calendar-days')
                ->color('info'),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }
}