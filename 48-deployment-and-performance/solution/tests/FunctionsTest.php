<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase;

use function App\format_uptime;
use function App\health_check;
use function App\opcache_hit_rate;

final class FunctionsTest extends TestCase
{
    public function testHealthCheckOkWhenAllPass(): void
    {
        $result = health_check([
            'database' => fn () => true,
            'disk_space' => fn () => true,
        ]);

        $this->assertSame('ok', $result['status']);
        $this->assertSame(['database' => 'ok', 'disk_space' => 'ok'], $result['checks']);
    }

    public function testHealthCheckDegradedWhenOneFails(): void
    {
        $result = health_check([
            'database' => fn () => true,
            'disk_space' => fn () => false,
        ]);

        $this->assertSame('degraded', $result['status']);
        $this->assertSame(['database' => 'ok', 'disk_space' => 'fail'], $result['checks']);
    }

    public function testHealthCheckCallsEachCheckExactlyOnce(): void
    {
        $calls = 0;
        health_check(['database' => function () use (&$calls) {
            $calls++;
            return true;
        }]);

        $this->assertSame(1, $calls);
    }

    public function testOpcacheHitRateComputesPercentage(): void
    {
        $this->assertSame(80.0, opcache_hit_rate(80, 20));
    }

    public function testOpcacheHitRateRoundsToTwoDecimals(): void
    {
        $this->assertSame(66.67, opcache_hit_rate(2, 1));
    }

    public function testOpcacheHitRateZeroWhenNoData(): void
    {
        $this->assertSame(0.0, opcache_hit_rate(0, 0));
    }

    public function testFormatUptimeSecondsOnly(): void
    {
        $this->assertSame('45s', format_uptime(45));
    }

    public function testFormatUptimeMinutesAndSeconds(): void
    {
        $this->assertSame('2m 5s', format_uptime(125));
    }

    public function testFormatUptimeHoursAndMinutes(): void
    {
        $this->assertSame('1h 2m', format_uptime(3725));
    }

    public function testFormatUptimeDaysAndHours(): void
    {
        $this->assertSame('1d 1h', format_uptime(90061));
    }
}
