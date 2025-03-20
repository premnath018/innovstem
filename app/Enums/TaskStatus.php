<?php

namespace App\Enums;

enum TaskStatus: string
{
    case ToDo = 'to_do';
    case InProgress = 'in_progress';
    case SentForReview = 'sent_for_review';
    case Done = 'done';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}