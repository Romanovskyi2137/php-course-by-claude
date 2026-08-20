<?php

declare(strict_types=1);

// Тема 25: JSON та серіалізація. Опис завдання — у task.md.

// TODO (вимога 1): читабельний JSON без екранування юнікоду, з винятком на помилку.
function encode_pretty(array $data): string
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 2): декодування в асоціативний масив, JsonException на помилку.
function decode_strict(string $json): mixed
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 3): контрольована серіалізація через JsonSerializable.
class Money implements \JsonSerializable
{
    public function __construct(int $cents, string $currency)
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function jsonSerialize(): array
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

// TODO (вимога 4): decode_strict() без винятку назовні — null замість падіння.
function safe_json_decode(string $json): ?array
{
    throw new \RuntimeException('Не реалізовано');
}
