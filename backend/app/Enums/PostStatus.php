<?php

declare(strict_types=1);

namespace App\Enums;

enum PostStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PUBLISHED => 'Published',
            self::ARCHIVED => 'Archived',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
