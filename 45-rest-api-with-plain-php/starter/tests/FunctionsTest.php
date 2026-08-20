<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase;

use function App\json_response;
use function App\negotiate_content_type;

final class FunctionsTest extends TestCase
{
    public function testNegotiatesExactMatch(): void
    {
        $result = negotiate_content_type('application/json, text/html;q=0.9', ['application/json', 'text/html']);

        $this->assertSame('application/json', $result);
    }

    public function testNegotiatesWildcard(): void
    {
        $result = negotiate_content_type('*/*', ['application/json', 'text/html']);

        $this->assertSame('application/json', $result);
    }

    public function testFallsBackToFirstAvailableWhenNoMatch(): void
    {
        $result = negotiate_content_type('application/xml', ['application/json', 'text/html']);

        $this->assertSame('application/json', $result);
    }

    public function testJsonResponseBuildsStatusHeadersAndBody(): void
    {
        $response = json_response(['ok' => true], 201);

        $this->assertSame(201, $response['status']);
        $this->assertSame(['Content-Type' => 'application/json'], $response['headers']);
        $this->assertSame('{"ok":true}', $response['body']);
    }

    public function testJsonResponseDefaultsToStatus200(): void
    {
        $response = json_response(['ok' => true]);

        $this->assertSame(200, $response['status']);
    }
}
