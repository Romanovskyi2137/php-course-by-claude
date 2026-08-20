<?php

declare(strict_types=1);

namespace App\Tests;

use App\Calculator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    // TODO (вимога 1): перевір, що add(2, 3) === 5.
    public function testAdd(): void
    {
        $this->fail('Не реалізовано (вимога 1)');
    }

    // TODO (вимога 2): перевір divide($a, $b) === $expected для кожного набору divisionCases().
    #[DataProvider('divisionCases')]
    public function testDivide(int $a, int $b, float $expected): void
    {
        $this->fail('Не реалізовано (вимога 2)');
    }

    public static function divisionCases(): array
    {
        return [
            'десять на два' => [10, 2, 5.0],
            "дев'ять на три" => [9, 3, 3.0],
        ];
    }

    // TODO (вимога 2): перевір, що divide(5, 0) кидає \DivisionByZeroError.
    public function testDivideByZeroThrows(): void
    {
        $this->fail('Не реалізовано (вимога 2)');
    }
}
