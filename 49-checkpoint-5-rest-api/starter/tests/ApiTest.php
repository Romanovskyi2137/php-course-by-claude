<?php

declare(strict_types=1);

namespace App\Tests;

use App\Api;
use App\InMemoryBookmarkRepository;
use PHPUnit\Framework\TestCase;

final class ApiTest extends TestCase
{
    private const VALID_TOKENS = ['good-token' => 'user-1'];

    public function testListReturnsAllBookmarks(): void
    {
        $repo = new InMemoryBookmarkRepository();
        $repo->add('PHP.net', 'https://php.net');
        $api = new Api($repo, self::VALID_TOKENS);

        $result = $api->list([], [], []);

        $this->assertSame(200, $result['status']);
        $this->assertSame('[{"id":1,"title":"PHP.net","url":"https:\/\/php.net"}]', $result['body']);
    }

    public function testShowReturns200ForExistingBookmark(): void
    {
        $repo = new InMemoryBookmarkRepository();
        $bookmark = $repo->add('PHP.net', 'https://php.net');
        $api = new Api($repo, self::VALID_TOKENS);

        $result = $api->show(['id' => (string) $bookmark->id], [], []);

        $this->assertSame(200, $result['status']);
    }

    public function testShowReturns404ForMissingBookmark(): void
    {
        $api = new Api(new InMemoryBookmarkRepository(), self::VALID_TOKENS);

        $result = $api->show(['id' => '999'], [], []);

        $this->assertSame(404, $result['status']);
    }

    public function testCreateReturns401WithoutValidToken(): void
    {
        $api = new Api(new InMemoryBookmarkRepository(), self::VALID_TOKENS);

        $result = $api->create([], [], ['title' => 'PHP.net', 'url' => 'https://php.net']);

        $this->assertSame(401, $result['status']);
    }

    public function testCreateReturns400ForMissingFields(): void
    {
        $api = new Api(new InMemoryBookmarkRepository(), self::VALID_TOKENS);
        $server = ['HTTP_AUTHORIZATION' => 'Bearer good-token'];

        $result = $api->create([], $server, ['title' => 'PHP.net']);

        $this->assertSame(400, $result['status']);
    }

    public function testCreateReturns201AndPersistsBookmark(): void
    {
        $repo = new InMemoryBookmarkRepository();
        $api = new Api($repo, self::VALID_TOKENS);
        $server = ['HTTP_AUTHORIZATION' => 'Bearer good-token'];

        $result = $api->create([], $server, ['title' => 'PHP.net', 'url' => 'https://php.net']);

        $this->assertSame(201, $result['status']);
        $this->assertCount(1, $repo->all());
    }

    public function testDeleteReturns401WithoutValidToken(): void
    {
        $repo = new InMemoryBookmarkRepository();
        $bookmark = $repo->add('PHP.net', 'https://php.net');
        $api = new Api($repo, self::VALID_TOKENS);

        $result = $api->delete(['id' => (string) $bookmark->id], [], []);

        $this->assertSame(401, $result['status']);
        $this->assertNotNull($repo->find($bookmark->id));
    }

    public function testDeleteReturns404ForMissingBookmark(): void
    {
        $api = new Api(new InMemoryBookmarkRepository(), self::VALID_TOKENS);
        $server = ['HTTP_AUTHORIZATION' => 'Bearer good-token'];

        $result = $api->delete(['id' => '999'], $server, []);

        $this->assertSame(404, $result['status']);
    }

    public function testDeleteReturns200AndRemovesBookmark(): void
    {
        $repo = new InMemoryBookmarkRepository();
        $bookmark = $repo->add('PHP.net', 'https://php.net');
        $api = new Api($repo, self::VALID_TOKENS);
        $server = ['HTTP_AUTHORIZATION' => 'Bearer good-token'];

        $result = $api->delete(['id' => (string) $bookmark->id], $server, []);

        $this->assertSame(200, $result['status']);
        $this->assertNull($repo->find($bookmark->id));
    }
}
