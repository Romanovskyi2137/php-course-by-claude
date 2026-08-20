<?php

declare(strict_types=1);

// Тема 25: JSON та серіалізація — референсний розв'язок.

function encode_pretty(array $data): string
{
    return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
}

function decode_strict(string $json): mixed
{
    return json_decode($json, associative: true, flags: JSON_THROW_ON_ERROR);
}

class Money implements \JsonSerializable
{
    public function __construct(
        private int $cents,
        private string $currency,
    ) {
    }

    public function jsonSerialize(): array
    {
        return ['cents' => $this->cents, 'currency' => $this->currency];
    }
}

function safe_json_decode(string $json): ?array
{
    try {
        $result = decode_strict($json);
    } catch (\JsonException) {
        return null;
    }

    return is_array($result) ? $result : null;
}
