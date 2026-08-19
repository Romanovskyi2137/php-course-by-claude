<?php

declare(strict_types=1);

// Тема 18: enums — референсний розв'язок.

enum Status: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Чернетка',
            self::Published => 'Опубліковано',
            self::Archived => 'Архів',
        };
    }
}

function next_status(Status $status): Status
{
    return match ($status) {
        Status::Draft => Status::Published,
        Status::Published => Status::Archived,
        Status::Archived => Status::Archived,
    };
}

function parse_status(string $value): ?Status
{
    return Status::tryFrom($value);
}
