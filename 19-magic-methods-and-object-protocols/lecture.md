# 19. Магічні методи та об'єктні протоколи

> Що ти вже вмієш: перевантаження операторів/protocols з іншої мови · Що додає
> ця тема: PHP-механізм `__get`/`__set`/`__toString` та вбудовані інтерфейси,
> що вмикають синтаксис масивів/рядків для об'єктів · ~25 хв

## 1. Якщо ти прийшов з JS / Python / Java

- Магічні методи PHP (`__get`, `__set`, `__toString`, `__invoke`) — концептуальний
  аналог Python dunder-методів (`__getattr__`, `__setattr__`, `__str__`,
  `__call__`). Та сама ідея: мова викликає спеціальний метод у певних ситуаціях
  автоматично.
- `ArrayAccess` дає обʼєкту синтаксис `$obj['key']` — схоже на перевантаження
  `[]` в C++/Python (`__getitem__`), але в PHP це формальний інтерфейс з
  чотирма обов'язковими методами, а не одна функція.
- JS має Proxy для перехоплення `obj.prop`/`obj[key]` — концептуально схоже
  на `__get`/`ArrayAccess`, але в PHP це вбудований, стандартизований
  механізм на рівні мови, не окрема обгортка.

## 2. Ключові терміни

- **Magic method (магічний метод)** — метод зі спеціальною назвою
  (`__get`, `__set`, `__call`, `__toString`, ...), який PHP викликає
  автоматично в певних ситуаціях, а не за прямим викликом.
- **Object protocol (об'єктний протокол)** — вбудований інтерфейс
  (`ArrayAccess`, `Countable`, `Stringable`, `IteratorAggregate`), що дає
  обʼєкту поводитись як масив/рядок/лічильна колекція в звичайному
  PHP-синтаксисі.

## 3. Теорія

### 3.1 `__get`/`__set`/`__isset`: динамічні властивості

```php
class DynamicBag
{
    private array $data = [];

    public function __get(string $name): mixed
    {
        return $this->data[$name] ?? null;
    }

    public function __set(string $name, mixed $value): void
    {
        $this->data[$name] = $value;
    }

    public function __isset(string $name): bool
    {
        return isset($this->data[$name]);
    }
}

$bag = new DynamicBag();
$bag->color = 'red';    // викликає __set('color', 'red') — немає властивості $color!
echo $bag->color;         // викликає __get('color') -> 'red'
isset($bag->color);         // викликає __isset('color') -> true
```

`__get`/`__set` спрацьовують **лише** для властивостей, яких немає (або
недоступні через видимість) — для звичайних `public`-властивостей PHP
використовує пряме звернення, магія не втручається.

### 3.2 `__toString` і `Stringable`

```php
class Money implements \Stringable
{
    public function __construct(private int $cents) {}

    public function __toString(): string
    {
        return number_format($this->cents / 100, 2);
    }
}

$price = new Money(1250);
echo $price;             // '12.50' — echo неявно викликає __toString()
echo "Ціна: {$price}";     // працює і в інтерполяції рядків
```

Клас з `__toString()` **автоматично** задовольняє інтерфейс `Stringable`
(PHP 8.0+ додає його неявно) — явний `implements \Stringable` необов'язковий,
але покращує читабельність сигнатур (`function f(string|Stringable $x)`).

### 3.3 `ArrayAccess`: `$obj[$key]`

```php
class Config implements \ArrayAccess, \Countable
{
    public function __construct(private array $data) {}

    public function offsetExists(mixed $offset): bool { return isset($this->data[$offset]); }
    public function offsetGet(mixed $offset): mixed { return $this->data[$offset] ?? null; }
    public function offsetSet(mixed $offset, mixed $value): void { $this->data[$offset] = $value; }
    public function offsetUnset(mixed $offset): void { unset($this->data[$offset]); }
    public function count(): int { return count($this->data); }
}

$config = new Config(['debug' => true]);
$config['debug'];       // true — offsetGet
$config['env'] = 'prod'; // offsetSet
count($config);            // count() — завдяки Countable
```

### 3.4 `__call`/`__callStatic`

```php
class ApiClient
{
    public function __call(string $method, array $args): mixed
    {
        // перехоплює виклик БУДЬ-ЯКОГО неіснуючого методу
        return "виклик {$method} з " . count($args) . ' аргументами';
    }
}

(new ApiClient())->getUsers(1, 2);   // 'виклик getUsers з 2 аргументами'
```

### 3.5 `__invoke`: обʼєкт як функція

```php
class Multiplier
{
    public function __construct(private int $factor) {}

    public function __invoke(int $x): int
    {
        return $x * $this->factor;
    }
}

$double = new Multiplier(2);
$double(5);   // 10 — обʼєкт викликається як функція!
array_map($double, [1, 2, 3]);   // обʼєкт можна передати як callable
```

## 4. Підводні камені

- Магічні методи роблять код **непрозорим для статичного аналізу й IDE** —
  автодоповнення не знає про "властивості", створені через `__get`/`__set`
  динамічно. Використовуй їх обережно, лише коли реальна вигода (динамічні
  дані, ArrayAccess-обгортки) переважує втрату прозорості.
- `__get`/`__set` спрацьовують тільки для **недоступних** властивостей —
  якщо забути зробити властивість `private`, магія просто не викличеться,
  і поведінка виглядатиме "не працює" без явної помилки.
- `ArrayAccess` вимагає реалізації **всіх чотирьох** методів інтерфейсу,
  навіть якщо, наприклад, `offsetUnset` не має сенсу для конкретного
  класу — тоді там просто кидають виняток або лишають порожнім.

## 5. Сучасний PHP 8.3+

`Stringable` (8.0) — інтерфейс, що PHP додає **автоматично** будь-якому
класу з `__toString()`, без явного `implements`. Це рідкісний випадок,
коли мова сама вставляє інтерфейс за тебе — корисно знати, щоб не дивуватись,
чому `$obj instanceof Stringable` повертає `true`, хоч ти його не писав.

## 6. Контекст екосистеми

`ArrayAccess` + `Countable` + `IteratorAggregate` (тема 22) разом — основа
Collection-класів у Laravel і подібних бібліотеках: обʼєкт поводиться як
масив (`$collection[0]`, `count($collection)`, `foreach`), але має додаткові
методи (`->map()`, `->filter()`) поверх звичайного масиву.

## 7. Документація

- https://www.php.net/manual/en/language.oop5.magic.php
- https://www.php.net/manual/en/class.arrayaccess.php
- https://www.php.net/manual/en/class.stringable.php

## 8. Питання для самоперевірки

1. Коли саме викликається `__get()` — завжди чи лише за певних умов?
2. Чому клас з `__toString()` автоматично стає `instanceof Stringable`?
3. Скільки методів вимагає інтерфейс `ArrayAccess` і які саме?
4. Для чого потрібен `__invoke()` і як його можна використати з `array_map()`?
