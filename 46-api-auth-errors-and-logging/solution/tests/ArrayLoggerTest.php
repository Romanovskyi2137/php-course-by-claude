<?php

declare(strict_types=1);

namespace App\Tests;

use App\ArrayLogger;
use PHPUnit\Framework\TestCase;

final class ArrayLoggerTest extends TestCase
{
    public function testInfoAndErrorAreRecordedInOrder(): void
    {
        $logger = new ArrayLogger();
        $logger->info('Order created', ['orderId' => 1]);
        $logger->error('Payment failed', ['orderId' => 1, 'reason' => 'timeout']);

        $this->assertSame([
            ['level' => 'info', 'message' => 'Order created', 'context' => ['orderId' => 1]],
            ['level' => 'error', 'message' => 'Payment failed', 'context' => ['orderId' => 1, 'reason' => 'timeout']],
        ], $logger->entries());
    }

    public function testContextDefaultsToEmptyArray(): void
    {
        $logger = new ArrayLogger();
        $logger->info('No context here');

        $this->assertSame([], $logger->entries()[0]['context']);
    }
}
