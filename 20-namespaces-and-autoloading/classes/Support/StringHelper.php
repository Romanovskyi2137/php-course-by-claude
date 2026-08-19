<?php

declare(strict_types=1);

namespace App\Support;

// Клас для теми 20: підвантажується автозавантажувачем, який пишеш у starter.php.
// Не редагуй цей файл — завдання про сам автозавантажувач, не про цей клас.

class StringHelper
{
    public static function shout(string $text): string
    {
        return mb_strtoupper($text);
    }
}
