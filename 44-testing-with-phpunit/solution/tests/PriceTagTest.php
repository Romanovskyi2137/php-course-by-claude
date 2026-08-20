<?php

declare(strict_types=1);

namespace App\Tests;

use App\PriceFormatter;
use App\PriceTag;
use PHPUnit\Framework\TestCase;

final class PriceTagTest extends TestCase
{
    public function testRenderUsesFormatter(): void
    {
        $formatter = $this->createMock(PriceFormatter::class);
        $formatter->expects($this->once())
            ->method('format')
            ->with(99.0)
            ->willReturn('99.00 UAH');

        $tag = new PriceTag($formatter);

        $this->assertSame('[99.00 UAH]', $tag->render(99.0));
    }
}
