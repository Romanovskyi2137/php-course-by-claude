# 22. Ітератори, генератори та SPL

> Що ти вже вмієш: `for`/`foreach` з іншої мови, можливо генератори з
> Python/JS · Що додає ця тема: як `foreach` насправді працює під капотом
> у PHP, і коли писати `Iterator` вручну, а коли достатньо `yield` · ~30 хв

## 1. Якщо ти прийшов з JS / Python / Java

- PHP-генератори — це майже точно те саме, що генератори Python (`yield`)
  чи JS (`function*`/`yield`): функція, яка призупиняється й повертає
  значення по одному замість того, щоб будувати весь масив одразу.
- На відміну від Python, де ітератор — це протокол з двох методів
  (`__iter__`/`__next__`), у PHP `\Iterator` — формальний інтерфейс із
  **п'ятьма** методами (розділ 3.1). Генератор — це синтаксичний цукор,
  який реалізує цей інтерфейс за тебе.
- `SplStack`/`SplQueue`/`SplObjectStorage` — аналоги того, що в Java є
  `Deque`/`IdentityHashMap`, а в Python зазвичай роблять на голому `list`
  чи `collections.deque`. У PHP це вбудовані класи стандартної бібліотеки
  (SPL — Standard PHP Library), а не окремий пакет.

## 2. Ключові терміни

- **`Iterator` (ітератор)** — вбудований інтерфейс з методами
  `current()`, `key()`, `next()`, `rewind()`, `valid()`; будь-який об'єкт,
  що його реалізує, можна перебирати через `foreach`.
- **`Iterable` (iterable)** — вбудований тип-псевдонім для параметрів/
  повернення: приймає або масив, або будь-що, що реалізує `Traversable`
  (тобто `Iterator` чи `Generator`).
- **Generator (генератор)** — об'єкт, який автоматично створює PHP, коли
  функція містить `yield`; реалізує `Iterator` за тебе, без ручного
  керування станом.
- **`yield from`** — делегування ітерації іншому `iterable` (масиву,
  генератору) зсередини генератора — "влий сюди всі значення звідти".
- **SPL (Standard PHP Library)** — набір вбудованих структур даних і
  інтерфейсів (`SplStack`, `SplQueue`, `SplObjectStorage`, `ArrayObject`
  тощо), доступний без будь-яких `require`/Composer.

## 3. Теорія

### 3.1 Інтерфейс `Iterator` вручну

`foreach ($obj as $k => $v)` над об'єктом, що реалізує `\Iterator`,
розгортається приблизно в такий виклик методів:

```php
class NumberRange implements \Iterator
{
    private int $current;

    public function __construct(private int $start, private int $end) {
        $this->current = $start;
    }

    public function current(): int { return $this->current; }
    public function key(): int { return $this->current - $this->start; }
    public function next(): void { $this->current++; }
    public function rewind(): void { $this->current = $this->start; }
    public function valid(): bool { return $this->current <= $this->end; }
}

foreach (new NumberRange(1, 3) as $k => $v) {
    echo "{$k} => {$v}\n"; // 0 => 1, 1 => 2, 2 => 3
}
```

`foreach` викликає `rewind()` один раз, тоді в циклі — `valid()`,
`current()`, `key()`, `next()`, поки `valid()` не поверне `false`.

### 3.2 Генератори: той самий результат без стану вручну

```php
function numberRange(int $start, int $end): \Generator
{
    for ($i = $start; $i <= $end; $i++) {
        yield $i;
    }
}

foreach (numberRange(1, 3) as $v) {
    echo $v; // 123
}
```

`yield` призупиняє виконання функції й "віддає" значення назовні; при
наступній ітерації `foreach` виконання продовжується з того самого місця
(разом з усіма локальними змінними) — не з початку функції. Генератор
обчислює значення **ліниво (lazily)**, одне за раз, а не будує весь масив
у пам'яті наперед.

### 3.3 `yield` з ключем і `yield from`

```php
function withKeys(): \Generator
{
    yield 'a' => 1;
    yield 'b' => 2;
}

function flatten(array $nested): \Generator
{
    foreach ($nested as $item) {
        if (is_array($item)) {
            yield from flatten($item);   // делегування — рекурсивно спускаємось углиб
        } else {
            yield $item;
        }
    }
}

iterator_to_array(flatten([1, [2, 3, [4]], 5]), false); // [1, 2, 3, 4, 5]
```

`yield from` приймає масив, `Iterator` або інший `Generator` — і "вливає"
всі його значення в поточний генератор, немов вони були `yield`-нуті тут.

### 3.4 SPL-структури даних

```php
$stack = new \SplStack();       // LIFO — push()/pop()
$stack->push('a');
$stack->push('b');
$stack->pop(); // 'b'

$queue = new \SplQueue();       // FIFO — enqueue()/dequeue()
$queue->enqueue('a');
$queue->enqueue('b');
$queue->dequeue(); // 'a'

$storage = new \SplObjectStorage(); // мапа об'єкт → довільні дані,
$storage[$someObject] = ['visits' => 3]; // ключ — сам об'єкт (identity, не value)
```

`SplStack`/`SplQueue` — обидва насправді `SplDoublyLinkedList` з різним
напрямком обходу; на відміну від звичайного `array`, `push`/`pop` на
кінцях і `shift`/`unshift` на початку в них — операції з передбачуваною
складністю O(1) без внутрішнього перевиділення індексів масиву.

## 4. Підводні камені

- Генератор можна перебрати **лише один раз**. Другий `foreach` над тим
  самим об'єктом `\Generator` кине `\Exception` ("Cannot rewind a
  generator that was already run"). Якщо потрібно перебирати кілька
  разів — виклич функцію-генератор заново, вона поверне новий об'єкт.
- `iterator_to_array($generator)` за замовчуванням зберігає **ключі**
  генератора — якщо генератор кілька разів робить `yield` без явного
  ключа (ключі 0, 1, 2, ... для кожного виклику), значення з однаковими
  ключами з різних `yield from` перезапишуть одне одного. Другий
  аргумент `false` каже "ігноруй ключі, просто зроби послідовний масив".
- `iterable` — це тип-параметр, не клас: не можна написати
  `new iterable()` чи `instanceof iterable`; для перевірки типу
  використовуй `is_iterable($value)`.

## 5. Сучасний PHP 8.3+

Генератори — стабільна частина мови з PHP 5.5, тож у 8.3 тут нічого
кардинально нового. Що варто знати: генератори добре поєднуються з
типізацією повернення `\Generator` (розділ 3.2) і з `iterable` як типом
параметра — обидва підтримуються нативно, без анотацій у докблоках.

## 6. Контекст екосистеми

Генератори — стандартний спосіб обробляти великі набори даних без
завантаження всього в пам'ять: читання мільйонів рядків CSV (тема 26),
пагінація результатів БД (тема 37), стрімінг HTTP-відповіді. SPL-структури
рідше зустрічаються у прикладному коді фреймворків напряму, але часто
лежать "під капотом" у їхніх колекціях і чергах.

## 7. Документація

- https://www.php.net/manual/en/language.generators.php
- https://www.php.net/manual/en/class.iterator.php
- https://www.php.net/manual/en/book.spl.php

## 8. Питання для самоперевірки

1. Які п'ять методів має реалізувати клас, щоб працювати з `foreach` без
   генератора?
2. Чим `yield from` відрізняється від звичайного `yield` у циклі?
3. Чому генератор не можна перебрати двічі, а масив, повернутий функцією,
   можна?
4. У чому практична різниця між `SplStack` і `SplQueue`?
