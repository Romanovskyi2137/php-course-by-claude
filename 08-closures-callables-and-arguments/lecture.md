# 08. Замикання, callable й аргументи

> Що ти вже вмієш: анонімні функції/лямбди з іншої мови · Що додає ця тема:
> PHP-специфіку `use`, arrow functions, variadic/named arguments · ~25 хв

## 1. Якщо ти прийшов з JS / Python / Java

- JS-замикання автоматично захоплюють зовнішні змінні. PHP `Closure` (через
  `function() {}`) **нічого не захоплює автоматично** — потрібен явний `use (...)`.
  Це різниця, яка справді ламає інтуїцію новачків з JS.
- PHP arrow functions `fn($x) => $x * 2` (7.4+) захоплюють зовнішні змінні
  **автоматично за значенням** (як JS-стрілки), але тільки для читання —
  ближче до JS, ніж класичний `function() use (...)`.
- Named arguments (`foo(name: 'Ada')`) — як у Python (`foo(name='Ada')') і Kotlin,
  на відміну від JS, де такого немає взагалі (емулюється об'єктом).

## 2. Ключові терміни

- **Closure (замикання)** — анонімна функція, яка може захоплювати змінні
  з навколишньої області видимості.
- **Variadic parameter (варіативний параметр)** — `...$args` збирає довільну
  кількість аргументів в масив.
- **Named arguments (іменовані аргументи)** — передача аргументів за назвою
  параметра, а не позицією: `f(b: 2, a: 1)`.
- **First-class callable syntax** — `strlen(...)` створює `Closure` з наявної
  функції/методу без `Closure::fromCallable()` (PHP 8.1+).

## 3. Теорія

### 3.1 Анонімні функції та `use`

```php
$factor = 3;

$multiply = function (int $x) use ($factor): int {
    return $x * $factor;
};

$multiply(5);   // 15

$factor = 100;
$multiply(5);    // все ще 15! use ($factor) захопив значення 3 В МОМЕНТ СТВОРЕННЯ,
                   // а не посилання — зміна $factor після не впливає
```

Щоб захопити за посиланням (і бачити подальші зміни):

```php
$counter = 0;
$increment = function () use (&$counter): void {
    $counter++;
};
$increment();
$increment();
echo $counter;   // 2 — тепер бачить зміни, бо &$counter
```

### 3.2 Arrow functions (`fn`)

```php
$factor = 3;
$multiply = fn (int $x): int => $x * $factor;   // автозахоплення $factor, без use!
$multiply(5);   // 15
```

Обмеження arrow functions: тільки одне вираз-повернення (немає `{}` з кількома
інструкціями), і автозахоплення завжди **за значенням** (немає еквіваленту
`use (&$x)` для стрілок).

### 3.3 Variadic параметри та spread

```php
function sum(int ...$numbers): int
{
    return array_sum($numbers);   // $numbers тут звичайний масив [1, 2, 3]
}

sum(1, 2, 3);          // 6

$values = [1, 2, 3];
sum(...$values);         // 6 — spread розпаковує масив в окремі аргументи
```

### 3.4 Named arguments

```php
function createUser(string $name, string $role = 'user', bool $active = true): array
{
    return compact('name', 'role', 'active');
}

createUser(name: 'Ada', active: false);   // role лишається дефолтним 'user',
                                            // порядок іменованих аргументів не важливий
```

Особливо корисно для функцій з багатьма опційними параметрами — не треба
передавати всі попередні дефолти, щоб дістатись до потрібного.

### 3.5 `callable`: три форми

```php
function apply(callable $fn, int $x): int
{
    return $fn($x);
}

apply('strlen', 12345);                 // рядок з назвою функції (застаріліше)
apply(fn ($x) => $x * 2, 5);              // Closure
apply([$object, 'method'], 5);              // масив [об'єкт, 'назва методу']
apply(SomeClass::method(...), 5);            // first-class callable syntax (8.1+)
```

### 3.6 `Closure::fromCallable` і first-class callable syntax

```php
$strlenClosure = strlen(...);          // PHP 8.1+, найкоротший спосіб
$strlenClosure2 = Closure::fromCallable('strlen');   // старий спосіб, теж працює
```

## 4. Підводні камені

- Забутий `use ($var)` — найчастіша причина "чому замикання не бачить мою
  зовнішню змінну" в PHP (на відміну від JS, де це працює само).
- `use ($var)` захоплює **значення на момент створення** замикання, не на момент
  виклику — якщо потрібна "жива" зовнішня змінна, обов'язково `use (&$var)`.
- Arrow functions автозахоплюють **все**, що використовується в тілі, — це
  зручно, але легко випадково захопити важкий обʼєкт, якщо він просто
  згадується у виразі.

## 5. Сучасний PHP 8.3+

First-class callable syntax (`foo(...)`, 8.1) — сучасний стандарт створення
`Closure` з наявної функції/методу, коротший і типобезпечніший за рядкові
callable (`'foo'`), які IDE не завжди вміє перевірити на існування.

## 6. Контекст екосистеми

Named arguments масово використовуються в конфігураційних білдерах Laravel/Symfony
(`Route::get(uri: '/users', action: [UserController::class, 'index'])`) —
підвищує читабельність викликів з багатьма опційними параметрами.

## 7. Документація

- https://www.php.net/manual/en/functions.anonymous.php
- https://www.php.net/manual/en/functions.arrow.php
- https://www.php.net/manual/en/functions.arguments.php#functions.variable-arg-list
- https://www.php.net/manual/en/functions.first_class_callable_syntax.php

## 8. Питання для самоперевірки

1. Чому `use ($factor)` не бачить зміни `$factor` після створення замикання,
   а `use (&$factor)` бачить?
2. Чим arrow function (`fn`) відрізняється від `function() use (...) {}` за
   способом захоплення зовнішніх змінних?
3. Що робить `...` у `sum(int ...$numbers)` і що робить `...` у `sum(...$values)`
   — це те саме чи різні речі?
4. Що таке first-class callable syntax і яку проблему рядкових callable вона вирішує?
