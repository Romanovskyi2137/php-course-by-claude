<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase;

use function App\error_response;

final class ErrorResponseTest extends TestCase
{
    public function testBuildsUnifiedErrorShape(): void
    {
        $response = error_response('Не авторизовано', 401);

        $this->assertSame(401, $response['status']);
        $this->assertSame(['Content-Type' => 'application/json'], $response['headers']);
        $this->assertSame('{"error":"Не авторизовано"}', $response['body']);
    }
}
