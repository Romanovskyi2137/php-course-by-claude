<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertSame, assertThrows, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1: grade_letter()');
test('90 -> A', fn () => assertSame('A', grade_letter(90)));
test('75 -> B', fn () => assertSame('B', grade_letter(75)));
test('60 -> C', fn () => assertSame('C', grade_letter(60)));
test('59 -> F', fn () => assertSame('F', grade_letter(59)));
test('100 -> A', fn () => assertSame('A', grade_letter(100)));

section('Вимога 2: describe_weekday()');
test('1 (понеділок) -> будній день', fn () => assertSame('будній день', describe_weekday(1)));
test('5 (п\'ятниця) -> будній день', fn () => assertSame('будній день', describe_weekday(5)));
test('6 (субота) -> вихідний', fn () => assertSame('вихідний', describe_weekday(6)));
test('7 (неділя) -> вихідний', fn () => assertSame('вихідний', describe_weekday(7)));
test('0 кидає InvalidArgumentException', function (): void {
    assertThrows(\InvalidArgumentException::class, fn () => describe_weekday(0));
});
test('8 кидає InvalidArgumentException', function (): void {
    assertThrows(\InvalidArgumentException::class, fn () => describe_weekday(8));
});

section('Вимога 3: sum_positive()');
test('сумує лише додатні', fn () => assertSame(6, sum_positive([1, -2, 3, 0, 2])));
test('порожній масив -> 0', fn () => assertSame(0, sum_positive([])));
test('усі від\'ємні -> 0', fn () => assertSame(0, sum_positive([-1, -2, -3])));

section('Вимога 4: doubled()');
test('подвоює кожен елемент', fn () => assertSame([2, 4, 6], doubled([1, 2, 3])));
test('не змінює оригінальний масив (value semantics)', function (): void {
    $original = [1, 2, 3];
    doubled($original);
    assertSame([1, 2, 3], $original);
});
test('функція без прихованого стану — однакові виклики дають однаковий результат', function (): void {
    assertSame([2, 4, 6], doubled([1, 2, 3]));
    assertSame([10, 20], doubled([5, 10]));
    assertSame([2, 4, 6], doubled([1, 2, 3]));
});

exit(summary());
