<?php

namespace App\Enums;

enum Role: string
{
    case TEACHER = 'teacher';
    case ASSISTANT = 'assistant';
    case STUDENT = 'student';

    public static function values() : array
    {
        return array_column(self::cases(), 'value');
    }
}
