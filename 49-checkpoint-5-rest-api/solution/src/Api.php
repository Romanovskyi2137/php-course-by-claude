<?php

declare(strict_types=1);

namespace App;

// Checkpoint 5: REST API закладок — референсний розв'язок.

final class Api
{
    public function __construct(private BookmarkRepository $repository, private array $validTokens)
    {
    }

    public function list(array $params, array $server, array $body): array
    {
        $bookmarks = array_map(static fn (Bookmark $b): array => $b->toArray(), $this->repository->all());

        return json_response($bookmarks);
    }

    public function show(array $params, array $server, array $body): array
    {
        $bookmark = $this->repository->find((int) $params['id']);

        return $bookmark === null
            ? error_response('Закладку не знайдено', 404)
            : json_response($bookmark->toArray());
    }

    public function create(array $params, array $server, array $body): array
    {
        if (authenticate($server, $this->validTokens) === null) {
            return error_response('Не авторизовано', 401);
        }
        if (empty($body['title']) || empty($body['url'])) {
            return error_response('Потрібні поля title і url', 400);
        }

        $bookmark = $this->repository->add($body['title'], $body['url']);

        return json_response($bookmark->toArray(), 201);
    }

    public function delete(array $params, array $server, array $body): array
    {
        if (authenticate($server, $this->validTokens) === null) {
            return error_response('Не авторизовано', 401);
        }

        $deleted = $this->repository->delete((int) $params['id']);

        return $deleted
            ? json_response(['deleted' => true])
            : error_response('Закладку не знайдено', 404);
    }
}
