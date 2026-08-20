<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertNull, assertSame, assertThrows, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1: encode_pretty()');
test('форматує з відступами й не екранує кирилицю', function (): void {
    $json = encode_pretty(['name' => 'Іван']);
    assertSame(true, str_contains($json, "\n"));
    assertSame(true, str_contains($json, 'Іван'));
    assertSame(false, str_contains($json, '\\u'));
});

section('Вимога 2: decode_strict()');
test('декодує в асоціативний масив', function (): void {
    assertSame(['a' => 1, 'b' => 2], decode_strict('{"a":1,"b":2}'));
});
test('кидає JsonException на некоректний JSON', function (): void {
    assertThrows(\JsonException::class, fn () => decode_strict('{invalid'));
});

section('Вимога 3: Money');
test('jsonSerialize() повертає cents і currency', function (): void {
    assertSame(
        '{"cents":1500,"currency":"UAH"}',
        json_encode(new Money(1500, 'UAH'), JSON_THROW_ON_ERROR)
    );
});
test('приватні властивості не витікають напряму в JSON без jsonSerialize-формату', function (): void {
    $json = json_encode(new Money(100, 'USD'), JSON_THROW_ON_ERROR);
    $decoded = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
    assertSame(['cents', 'currency'], array_keys($decoded));
});

section('Вимога 4: safe_json_decode()');
test('повертає масив для коректного JSON', fn () => assertSame(['a' => 1], safe_json_decode('{"a":1}')));
test('повертає null для некоректного JSON', fn () => assertNull(safe_json_decode('not json')));
test('повертає null, якщо декодований результат — не масив', fn () => assertNull(safe_json_decode('42')));

exit(summary());
