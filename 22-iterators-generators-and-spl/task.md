# Завдання 22: ітератори, генератори та SPL

## Мета

1. Написати генератор з `yield` і генератор з `yield from`.
2. Реалізувати `\Iterator` вручну, щоб побачити, що саме автоматизує `yield`.
3. Використати `SplQueue` для реалістичної задачі — обмежена історія.

## Вимоги

1. `count_up(int $from, int $to)` — генератор, що видає (`yield`) цілі
   числа від `$from` до `$to` включно.
2. `flatten(array $nested)` — генератор, що видає скалярні значення з
   довільно вкладеного масиву "у плоскому вигляді", використовуючи
   `yield from` для рекурсивного спуску в підмасиви.
3. Клас `Playlist implements \Iterator, \Countable` — обгортка над
   масивом назв треків (`string[]`), що вручну реалізує протокол
   `Iterator` (без `yield`) плюс `count()`.
4. Клас `RecentlyPlayed` з обмеженою ємністю: `push(string $track)`
   додає трек, а якщо кількість перевищує ємність — витісняє
   найстаріший; `toArray()` повертає поточний вміст від найстарішого до
   найновішого. Використай `\SplQueue` як внутрішнє сховище.

## Контракт (сигнатури)

```php
function count_up(int $from, int $to): \Generator
function flatten(array $nested): \Generator

class Playlist implements \Iterator, \Countable
{
    public function __construct(array $tracks)
    public function current(): string
    public function key(): int
    public function next(): void
    public function rewind(): void
    public function valid(): bool
    public function count(): int
}

class RecentlyPlayed
{
    public function __construct(int $capacity)
    public function push(string $track): void
    public function toArray(): array
}
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- `count_up()` і `flatten()` — саме генератори (використовують `yield`),
  не звичайні функції, що повертають готовий `array`.
- `RecentlyPlayed` тримає елементи внутрішньо саме в `\SplQueue`, не в
  звичайному `array`.

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені
- [ ] можеш пояснити, чим `foreach` над `Playlist` (вимога 3) відрізняється
      "під капотом" від `foreach` над результатом `count_up()`

## Бонус (не перевіряється автоматично)

Додай до `Playlist` метод `shuffled(): \Generator`, що видає треки в
випадковому порядку (`array_rand`/`shuffle`), не змінюючи оригінальний
порядок у самому об'єкті.

<details>
<summary>Підказка 1</summary>

```php
function count_up(int $from, int $to): \Generator
{
    for ($i = $from; $i <= $to; $i++) {
        yield $i;
    }
}
```
</details>

<details>
<summary>Підказка 2</summary>

```php
function flatten(array $nested): \Generator
{
    foreach ($nested as $item) {
        if (is_array($item)) {
            yield from flatten($item);
        } else {
            yield $item;
        }
    }
}
```
</details>

<details>
<summary>Підказка 3</summary>

```php
class RecentlyPlayed
{
    private \SplQueue $queue;

    public function __construct(private int $capacity)
    {
        $this->queue = new \SplQueue();
    }

    public function push(string $track): void
    {
        $this->queue->enqueue($track);
        if ($this->queue->count() > $this->capacity) {
            $this->queue->dequeue();
        }
    }

    public function toArray(): array
    {
        return iterator_to_array($this->queue, false);
    }
}
```
</details>
