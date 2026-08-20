# Завдання 25: JSON та серіалізація

## Мета

1. Кодувати й декодувати JSON з явною обробкою помилок
   (`JSON_THROW_ON_ERROR`), а не мовчазним `null`/`false`.
2. Керувати форматом серіалізації власного класу через `JsonSerializable`.
3. Обгорнути "сувору" версію в безпечну, що повертає `null` замість
   винятку — застосування теми 21 (винятки) до тем 25.

## Вимоги

1. `encode_pretty(array $data)` — кодує масив у читабельний JSON:
   з відступами (`JSON_PRETTY_PRINT`), без екранування кирилиці
   (`JSON_UNESCAPED_UNICODE`), з винятком при помилці (`JSON_THROW_ON_ERROR`).
2. `decode_strict(string $json)` — декодує JSON в асоціативний масив
   (`associative: true`), кидає `\JsonException` при некоректному JSON
   замість повернення `null`.
3. Клас `Money implements \JsonSerializable` з приватними властивостями
   `cents` (int) і `currency` (string); `jsonSerialize()` повертає масив
   `['cents' => ..., 'currency' => ...]`.
4. `safe_json_decode(string $json)` — викликає `decode_strict()`, ловить
   `\JsonException` і повертає `null` замість того, щоб дати винятку
   "просочитись" далі; якщо результат декодування — не масив
   (наприклад, вхід був просто числом чи рядком), теж повертає `null`.

## Контракт (сигнатури)

```php
function encode_pretty(array $data): string
function decode_strict(string $json): mixed

class Money implements \JsonSerializable
{
    public function __construct(int $cents, string $currency)
    public function jsonSerialize(): array
}

function safe_json_decode(string $json): ?array
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- `decode_strict()` не приховує помилку парсингу — вона мусить дійти до
  викликача як `\JsonException`.
- `safe_json_decode()` ловить саме `\JsonException`, не `\Throwable`
  загалом.

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені
- [ ] можеш пояснити, чому `Money::jsonSerialize()` потрібен, якщо
      властивості `cents`/`currency` вже існують у класі

## Бонус (не перевіряється автоматично)

Додай статичний фабричний метод `Money::fromJson(string $json): self`,
що парсить JSON у форматі `{"cents": ..., "currency": ...}` назад в
об'єкт `Money` (симетрична операція до `jsonSerialize()`).

<details>
<summary>Підказка 1</summary>

```php
function encode_pretty(array $data): string
{
    return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
}
```
</details>

<details>
<summary>Підказка 2</summary>

```php
final class Money implements \JsonSerializable
{
    public function __construct(
        private int $cents,
        private string $currency,
    ) {
    }

    public function jsonSerialize(): array
    {
        return ['cents' => $this->cents, 'currency' => $this->currency];
    }
}
```
</details>

<details>
<summary>Підказка 3</summary>

```php
function safe_json_decode(string $json): ?array
{
    try {
        $result = decode_strict($json);
    } catch (\JsonException) {
        return null;
    }

    return is_array($result) ? $result : null;
}
```
</details>
