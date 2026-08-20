# 25. JSON та серіалізація

> Що ти вже вмієш: `JSON.stringify`/`json.dumps`/Jackson з іншої мови ·
> Що додає ця тема: чому PHP-масив — водночас і список, і об'єкт (і як
> це впливає на JSON), і як зробити свій клас серіалізовним · ~20 хв

## 1. Якщо ти прийшов з JS / Python / Java

- `json_encode()`/`json_decode()` — прямі аналоги `JSON.stringify`/
  `JSON.parse` (JS) чи `json.dumps`/`json.loads` (Python). Головна
  різниця в самому PHP, а не в JSON API: PHP-масив — один тип для й
  списку, й асоціативного масиву (тема 09), тож `json_encode()` мусить
  **вгадувати**, серіалізувати масив як JSON-масив `[...]` чи як
  JSON-об'єкт `{...}` (розділ 3.2).
- На відміну від Java (де `Jackson`/`Gson` серіалізують поля класу через
  рефлексію автоматично), у PHP звичайні `public`-властивості теж
  серіалізуються автоматично — але для контролю над форматом є
  `\JsonSerializable` (розділ 3.3), схоже на `toJSON()` в JS-об'єктах.
- За замовчуванням PHP **мовчить** при помилці парсингу — `json_decode()`
  без прапорця повертає `null`, а не кидає виняток, як `JSON.parse()` в
  JS. `JSON_THROW_ON_ERROR` (розділ 3.4) вмикає звичну для інших мов
  поведінку.

## 2. Ключові терміни

- **`JSON_THROW_ON_ERROR`** — прапорець, що змушує `json_encode()`/
  `json_decode()` кидати `\JsonException` замість мовчазного `false`/`null`.
- **`JsonSerializable`** — інтерфейс з одним методом `jsonSerialize()`;
  якщо об'єкт його реалізує, `json_encode()` викликає цей метод замість
  серіалізації публічних властивостей "як є".
- **`JSON_PRETTY_PRINT`** — прапорець форматування з відступами й
  переносами рядків (для читабельного виводу, не для продакшн-API).
- **`JSON_UNESCAPED_UNICODE`** — без цього прапорця кирилиця й інші
  не-ASCII символи кодуються як `\uXXXX`-послідовності замість "живих" символів.

## 3. Теорія

### 3.1 Базове кодування й декодування

```php
$data = ['name' => 'Іван', 'age' => 30];

$json = json_encode($data, JSON_THROW_ON_ERROR);
// '{"name":"Іван","age":30}' — кирилиця "заекранована"

json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
// '{"name":"Іван","age":30}'

$decoded = json_decode($json, associative: true, flags: JSON_THROW_ON_ERROR);
// ['name' => 'Іван', 'age' => 30] — асоціативний масив, не stdClass
```

Другий параметр `json_decode()` (`associative`) — ключовий: `true`
повертає звичайний асоціативний `array`, `false` (типове значення)
повертає `\stdClass` з властивостями через `->`. Для роботи в стилі
"масиви скрізь" (тема 09-10) майже завжди хочеться `true`.

### 3.2 Масив: список чи об'єкт?

```php
json_encode(['a', 'b', 'c']);              // '["a","b","c"]' — послідовні числові ключі → JSON-масив
json_encode([0 => 'a', 2 => 'b']);         // '{"0":"a","2":"b"}' — "дірка" в ключах → JSON-об'єкт
json_encode(['name' => 'Іван']);           // '{"name":"..."}' — рядкові ключі → JSON-об'єкт
json_encode([], flags: JSON_FORCE_OBJECT); // '{}' замість типового '[]' для порожнього масиву
```

PHP визначає це автоматично за формою ключів масиву — послідовні `0, 1,
2, ...` без пропусків стають JSON-масивом, будь-що інше — JSON-об'єктом.

### 3.3 `JsonSerializable`: контроль над форматом

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

json_encode(new Money(1500, 'UAH'), JSON_THROW_ON_ERROR);
// '{"cents":1500,"currency":"UAH"}' — саме те, що повернув jsonSerialize(),
// а не "сирі" private-властивості (які інакше взагалі не потрапили б у вивід)
```

Без `JsonSerializable` `json_encode()` серіалізує лише **публічні**
властивості об'єкта "як є" — `private $cents` без інтерфейсу просто
зникне з результату.

### 3.4 Обробка помилок: `JSON_THROW_ON_ERROR`

```php
json_decode('{invalid json'); // null, і жодного сигналу, що саме пішло не так

try {
    json_decode('{invalid json', flags: JSON_THROW_ON_ERROR);
} catch (\JsonException $e) {
    echo $e->getMessage(); // 'Syntax error'
}
```

## 4. Підводні камені

- `json_decode($json)` **без** `JSON_THROW_ON_ERROR` поверне `null`
  однаково і для вхідного `"null"` (валідний JSON), і для зламаного
  JSON — доводиться окремо перевіряти `json_last_error()`. З прапорцем
  ця неоднозначність зникає — помилка стає винятком.
- Порожній PHP-масив `[]` завжди кодується як JSON `[]`, **навіть якщо**
  логічно малось на увазі "порожній об'єкт" — типова причина, чому API
  на іншому кінці (JS/Python) раптом отримує `[]` там, де очікував `{}`.
  Рятує `JSON_FORCE_OBJECT` або явний `(object) []`.
- `json_encode()` за замовчуванням **не** серіалізує `private`/
  `protected`-властивості без `JsonSerializable` — і не попереджає про
  це, результат просто "тихо" не містить очікуваних полів.

## 5. Сучасний PHP 8.3+

Аргументи `json_encode()`/`json_decode()` з PHP 8.0+ мають іменовані
версії (`associative:`, `flags:`, `depth:`) — зручно для читабельності,
коли передаєш не всі параметри підряд (приклад у розділі 3.1). Сам
формат і поведінка кодека в 8.3 не змінились.

## 6. Контекст екосистеми

JSON — де-факто стандартний формат для REST API (теми 45-46) і файлів
конфігурації/фікстур. `JsonSerializable` — стандартний спосіб керувати
тим, що саме віддає API у відповідь (наприклад, приховати `passwordHash`
з DTO користувача), не покладаючись на випадковий набір публічних
властивостей.

## 7. Документація

- https://www.php.net/manual/en/function.json-encode.php
- https://www.php.net/manual/en/function.json-decode.php
- https://www.php.net/manual/en/class.jsonserializable.php

## 8. Питання для самоперевірки

1. Як PHP вирішує, серіалізувати масив у JSON-масив `[...]` чи в
   JSON-об'єкт `{...}`?
2. Що змінює прапорець `JSON_THROW_ON_ERROR` у поведінці `json_decode()`?
3. Навіщо потрібен `JsonSerializable`, якщо `json_encode()` вже вміє
   серіалізувати об'єкти "з коробки"?
4. Що станеться з `private`-властивістю об'єкта без `JsonSerializable`
   при `json_encode()`?
