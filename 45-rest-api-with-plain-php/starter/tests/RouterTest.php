<?php

declare(strict_types=1);

namespace App\Tests;

use App\Router;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    public function testDispatchesToRegisteredHandler(): void
    {
        $router = new Router();
        $router->register('GET', '/hello', fn (array $params) => ['status' => 200, 'body' => ['hi' => true]]);

        $result = $router->dispatch('GET', '/hello');

        $this->assertSame(200, $result['status']);
        $this->assertSame(['hi' => true], $result['body']);
    }

    public function testCapturesPathParameters(): void
    {
        $router = new Router();
        $router->register('GET', '/users/{id}', fn (array $params) => ['status' => 200, 'body' => $params]);

        $result = $router->dispatch('GET', '/users/42');

        $this->assertSame('42', $result['body']['id']);
    }

    public function testCapturesMultiplePathParameters(): void
    {
        $router = new Router();
        $router->register(
            'GET',
            '/users/{userId}/orders/{orderId}',
            fn (array $params) => ['status' => 200, 'body' => $params]
        );

        $result = $router->dispatch('GET', '/users/7/orders/99');

        $this->assertSame(['userId' => '7', 'orderId' => '99'], $result['body']);
    }

    public function testUnknownPathReturns404(): void
    {
        $router = new Router();
        $router->register('GET', '/hello', fn (array $params) => ['status' => 200, 'body' => []]);

        $result = $router->dispatch('GET', '/does-not-exist');

        $this->assertSame(404, $result['status']);
    }

    public function testKnownPathWrongMethodReturns405(): void
    {
        $router = new Router();
        $router->register('GET', '/hello', fn (array $params) => ['status' => 200, 'body' => []]);

        $result = $router->dispatch('POST', '/hello');

        $this->assertSame(405, $result['status']);
    }

    public function testMethodIsCaseInsensitive(): void
    {
        $router = new Router();
        $router->register('GET', '/hello', fn (array $params) => ['status' => 200, 'body' => []]);

        $result = $router->dispatch('get', '/hello');

        $this->assertSame(200, $result['status']);
    }

    public function testFirstMatchingRouteWins(): void
    {
        $router = new Router();
        $router->register('GET', '/users/new', fn (array $params) => ['status' => 200, 'body' => ['page' => 'new-form']]);
        $router->register('GET', '/users/{id}', fn (array $params) => ['status' => 200, 'body' => ['id' => $params['id']]]);

        $result = $router->dispatch('GET', '/users/new');

        $this->assertSame(['page' => 'new-form'], $result['body']);
    }
}
