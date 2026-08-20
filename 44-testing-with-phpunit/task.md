# Завдання 44: тестування з PHPUnit

## Підготовка

```bash
cd 44-testing-with-phpunit/starter && composer install && cd ..
```

Якщо `vendor/bin/phpunit` не знайдено — `check.php` виведе `[skip]` з
підказкою, а не провалиться.

## Мета

На відміну від попередніх тем, тут **не** реалізовуються нові функції —
`src/` уже повністю робочий. Твоя робота — написати самі **тести**
для нього в `tests/`.

## Що дано (готове, не редагуй)

- `src/Calculator.php` — `add(int, int): int`, `divide(int, int): float`
  (кидає `\DivisionByZeroError` при діленні на нуль).
- `src/PriceFormatter.php` — інтерфейс `format(float $amount): string`.
- `src/PriceTag.php` — `PriceTag` приймає `PriceFormatter` через
  конструктор (DI, тема 41); `render(float $amount): string` повертає
  `"[" . $formatter->format($amount) . "]"`.

## Вимоги

1. У `tests/CalculatorTest.php`, метод `testAdd()`: перевір, що
   `add(2, 3) === 5`.
2. У тому самому файлі, метод `testDivide()` з `#[DataProvider('divisionCases')]`
   (провайдер уже написаний): перевір, що `divide($a, $b) === $expected`
   для кожного набору. Додай ще один метод `testDivideByZeroThrows()`,
   що перевіряє: `divide(5, 0)` кидає `\DivisionByZeroError`
   (`$this->expectException(...)`).
3. У `tests/PriceTagTest.php`, метод `testRenderUsesFormatter()`:
   створи мок `PriceFormatter` через `$this->createMock(...)`, налаштуй
   його `format()` повертати `'99.00 UAH'` при виклику з `99.0`,
   перевір, що `format()` викликано **рівно один раз**
   (`$this->once()`), і що `render(99.0) === '[99.00 UAH]'`.

## Обмеження

- Не редагуй нічого в `src/` — лише файли в `tests/`.
- Кожен тест-метод містить хоча б одне реальне `assert*()`-твердження
  (не залишай порожні тести чи лише `$this->fail(...)`).

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] `vendor/bin/phpunit` (через `php check.php`) зелений
- [ ] можеш пояснити, чому `testRenderUsesFormatter()` неможливо було б
      написати так само зручно, якби `PriceTag` створював `PriceFormatter`
      сам усередині себе, а не приймав його через конструктор

## Бонус (не перевіряється автоматично)

Додай ще один набір даних у `divisionCases()` — від'ємні числа
(наприклад, `divide(-10, 2)` → `-5.0`) — і перевір, що `testDivide()`
проходить і для нього.

<details>
<summary>Підказка 1</summary>

```php
public function testAdd(): void
{
    $calculator = new Calculator();

    $this->assertSame(5, $calculator->add(2, 3));
}
```
</details>

<details>
<summary>Підказка 2</summary>

```php
public function testDivideByZeroThrows(): void
{
    $calculator = new Calculator();

    $this->expectException(\DivisionByZeroError::class);

    $calculator->divide(5, 0);
}
```
</details>

<details>
<summary>Підказка 3</summary>

```php
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
```
</details>
