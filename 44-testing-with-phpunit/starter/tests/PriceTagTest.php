<?php

declare(strict_types=1);

namespace App\Tests;

use App\PriceFormatter;
use App\PriceTag;
use PHPUnit\Framework\TestCase;

final class PriceTagTest extends TestCase
{
    // TODO (вимога 3): createMock(PriceFormatter::class), format() повертає '99.00 UAH'
    // рівно один раз при виклику з 99.0; render(99.0) === '[99.00 UAH]'.
    public function testRenderUsesFormatter(): void
    {
        $this->fail('Не реалізовано (вимога 3)');
    }
}
