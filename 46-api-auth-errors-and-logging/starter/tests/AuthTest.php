<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase;

use function App\authenticate;
use function App\extract_bearer_token;

final class AuthTest extends TestCase
{
    public function testExtractsTokenFromAuthorizationHeader(): void
    {
        $token = extract_bearer_token(['HTTP_AUTHORIZATION' => 'Bearer abc123']);

        $this->assertSame('abc123', $token);
    }

    public function testReturnsNullWithoutAuthorizationHeader(): void
    {
        $this->assertNull(extract_bearer_token([]));
    }

    public function testReturnsNullForNonBearerScheme(): void
    {
        $this->assertNull(extract_bearer_token(['HTTP_AUTHORIZATION' => 'Basic dXNlcjpwYXNz']));
    }

    public function testAuthenticateReturnsUserIdForValidToken(): void
    {
        $userId = authenticate(
            ['HTTP_AUTHORIZATION' => 'Bearer valid-token'],
            ['valid-token' => 'user-42']
        );

        $this->assertSame('user-42', $userId);
    }

    public function testAuthenticateReturnsNullForInvalidToken(): void
    {
        $userId = authenticate(
            ['HTTP_AUTHORIZATION' => 'Bearer wrong-token'],
            ['valid-token' => 'user-42']
        );

        $this->assertNull($userId);
    }

    public function testAuthenticateReturnsNullWithoutHeader(): void
    {
        $this->assertNull(authenticate([], ['valid-token' => 'user-42']));
    }
}
