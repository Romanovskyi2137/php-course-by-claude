# 13. Constructor promotion, readonly і клонування

> Що ти вже вмієш: клас із конструктором (тема 12) · Що додає ця тема: сучасний
> короткий синтаксис PHP 8 і незмінність (immutability) · ~20 хв

## 1. Якщо ти прийшов з JS / Python / Java

- Constructor property promotion нагадує стислий синтаксис Kotlin
  (`class Point(val x: Int)`) чи C# primary constructors — оголошення
  властивості прямо в сигнатурі конструктора, без дублювання `$this->x = $x;`.
- `readonly` — концептуально те саме, що `readonly` C#, `final` (для полів)
  Java чи `Object.freeze` в JS, але **перевіряється на рівні мови й точно в
  момент спроби другого запису**, а не лише угода/конвенція.
- `clone` — так само, як Python `copy.copy()`, робить **поверхневу** копію
  за замовчуванням: вкладені об'єкти лишаються спільними між оригіналом і
  копією, доки не напишеш `__clone()`.

## 2. Ключові терміни

- **Constructor property promotion** — оголошення й ініціалізація властивості
  прямо в списку параметрів конструктора.
- **`readonly`** — властивість, яку можна записати **рівно один раз** (з
  конструктора або звідки завгодно, але лише один раз за весь час життя
  об'єкта), після цього будь-який запис кидає `Error`.
- **Shallow clone (поверхневе клонування)** — `clone $obj` копіює сам об'єкт,
  але властивості-обʼєкти всередині лишаються тими самими посиланнями.
- **Wither method / with-метод** — метод, що повертає **новий** об'єкт зі
  зміненим одним полем, замість мутації існуючого — стандартний патерн для
  незмінних (`readonly`) об'єктів.

## 3. Теорія

### 3.1 Constructor property promotion

Порівняй з класичним підходом теми 12:

```php
// До PHP 8.0 (тема 12):
class Point
{
    private float $x;
    private float $y;

    public function __construct(float $x, float $y)
    {
        $this->x = $x;
        $this->y = $y;
    }
}

// PHP 8.0+: promotion — те саме, вдвічі коротше
class Point
{
    public function __construct(
        private float $x,
        private float $y,
    ) {}
}
```

Модифікатор видимості (`private`/`protected`/`public`) прямо перед параметром
перетворює його на властивість класу з тим самим іменем — окремо оголошувати
не потрібно.

### 3.2 `readonly`

```php
class Point
{
    public function __construct(
        public readonly float $x,
        public readonly float $y,
    ) {}
}

$p = new Point(1.0, 2.0);
echo $p->x;      // 1.0 — readonly властивості типово public, читати можна напряму
$p->x = 5.0;      // Error: Cannot modify readonly property Point::$x
```

`readonly` властивість обов'язково типізована (не можна `mixed` без явного
типу) і не може мати дефолтне значення в оголошенні — лише присвоєння
рівно один раз, зазвичай у конструкторі.

### 3.3 Immutability і "wither"-патерн

Оскільки `readonly` не можна змінити, для "зміни" пишуть метод, що повертає
**новий** обʼєкт:

```php
class Point
{
    public function __construct(
        public readonly float $x,
        public readonly float $y,
    ) {}

    public function withX(float $x): self
    {
        return new self($x, $this->y);   // новий обʼєкт, оригінал не змінюється
    }
}

$p1 = new Point(1.0, 2.0);
$p2 = $p1->withX(99.0);
// $p1->x все ще 1.0, $p2 — новий, незалежний обʼєкт з x = 99.0
```

Порівняй з темою 12, де `withX()` мутував `$this` напряму — тут навпаки,
і саме тому такий метод називають "wither" ("with" + "-er"), а не "setter".

### 3.4 Клонування: поверхневе за замовчуванням

```php
class Tag { public function __construct(public string $name) {} }
class Box { public function __construct(public Tag $tag) {} }

$original = new Box(new Tag('fragile'));
$copy = clone $original;

$copy->tag->name = 'changed';
echo $original->tag->name;   // 'changed'! Тег — той самий обʼєкт для обох Box
```

`clone` копіює сам `Box`, але властивість `$tag` — це посилання на обʼєкт
`Tag`, і воно копіюється як значення посилання (тема 12, reference semantics),
а не як новий `Tag`.

### 3.5 `__clone()`: глибоке клонування вручну

```php
class Box
{
    public function __construct(public Tag $tag) {}

    public function __clone(): void
    {
        $this->tag = clone $this->tag;   // явно клонуємо й вкладений обʼєкт
    }
}

$copy = clone $original;
$copy->tag->name = 'changed';
echo $original->tag->name;   // 'fragile' — тепер незалежні
```

`__clone()` викликається автоматично одразу після поверхневого копіювання,
і всередині нього `$this` — це вже нова (поверхнева) копія.

## 4. Підводні камені

- `readonly` можна записати лише один раз **за весь час життя обʼєкта**, не
  лише "не в конструкторі" — повторний запис навіть із того ж класу кидає
  `Error`.
- Забутий `__clone()` для обʼєктів із вкладеними обʼєктами-властивостями —
  джерело важко знаходжуваних багів: зміна "копії" несподівано впливає на
  оригінал.
- `readonly` не робить **вміст** масивів/обʼєктів усередині незмінним —
  `readonly array $items` забороняє переприсвоєння `$this->items = [...]`,
  але не забороняє `$this->items[] = 'x'`, якщо масив передали як посилання
  назовні (рідкісний, але реальний нюанс).

## 5. Сучасний PHP 8.3+

`readonly` (8.1) + promotion (8.0) — стандартна пара для DTO (Data Transfer
Object) у сучасному PHP: короткий, незмінний, явний контракт даних. У PHP 8.2
з'явились ще й **readonly-класи** (`readonly class Point {}` — усі властивості
класу readonly одразу, без повторення модифікатора для кожної).

## 6. Контекст екосистеми

DTO з promotion+readonly — стандартний спосіб передавати дані між шарами
застосунку (наприклад, з HTTP-запиту в сервіс) у Laravel/Symfony-проєктах:
незмінність гарантує, що дані не "мутують" непередбачувано десь по дорозі.

## 7. Документація

- https://www.php.net/manual/en/language.oop5.decon.php#language.oop5.decon.constructor.promotion
- https://www.php.net/manual/en/language.oop5.cloning.php
- https://www.php.net/manual/en/migration81.new-features.php — readonly properties

## 8. Питання для самоперевірки

1. Що саме робить модифікатор видимості перед параметром конструктора?
2. Скільки разів можна записати `readonly`-властивість і звідки?
3. Чому `clone $box` не захищає вкладений обʼєкт `$box->tag` від зміни, якщо
   немає `__clone()`?
4. Чим "wither"-метод відрізняється від звичайного сеттера?
