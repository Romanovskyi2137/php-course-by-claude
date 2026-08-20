<?php

declare(strict_types=1);

namespace App;

// Checkpoint 5: REST API закладок. Опис вимог — у task.md.

// TODO (вимога 4): REST-обробники — list/show/create/delete.
final class Api
{
    public function __construct(private BookmarkRepository $repository, private array $validTokens)
    {
    }

    public function list(array $params, array $server, array $body): array
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function show(array $params, array $server, array $body): array
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function create(array $params, array $server, array $body): array
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function delete(array $params, array $server, array $body): array
    {
        throw new \RuntimeException('Не реалізовано');
    }
}
