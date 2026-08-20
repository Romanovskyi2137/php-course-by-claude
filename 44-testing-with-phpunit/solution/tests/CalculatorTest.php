<?php

declare(strict_types=1);

namespace App\Tests;

use App\Calculator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function testAdd(): void
    {
        $calculator = new Calculator();

        $this->assertSame(5, $calculator->add(2, 3));
    }

    #[DataProvider('divisionCases')]
    public function testDivide(int $a, int $b, float $expected): void
    {
        $calculator = new Calculator();

        $this->assertSame($expected, $calculator->divide($a, $b));
    }

    public static function divisionCases(): array
    {
        return [
            'десять на два' => [10, 2, 5.0],
            "дев'ять на три" => [9, 3, 3.0],
        ];
    }

    public function testDivideByZeroThrows(): void
    {
        $calculator = new Calculator();

        $this->expectException(\DivisionByZeroError::class);

        $calculator->divide(5, 0);
    }
}
