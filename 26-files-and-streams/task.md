# Завдання 26: файли та потоки

## Мета

1. Читати й дописувати файли простими функціями "все за раз".
2. Розпарсити CSV-файл в асоціативні рядки.
3. Порахувати щось у файлі потоковим читанням, рядок за рядком.

Кожна перевірка створює власний тимчасовий файл, працює з ним і видаляє —
без окремої папки з фікстурами.

## Вимоги

1. `read_lines(string $path)` — повертає масив рядків файлу без символів
   переносу рядка в кінці кожного елемента.
2. `append_log(string $path, string $message)` — дописує `$message` в
   кінець файлу з переносом рядка після, **не** перезаписуючи наявний вміст.
3. `read_csv_as_assoc(string $path)` — читає CSV-файл, де перший рядок —
   заголовки колонок, повертає масив асоціативних рядків
   (`[['id' => '1', 'name' => 'Anna'], ...]`).
4. `count_words_in_stream(string $path)` — рахує загальну кількість слів
   у файлі, читаючи його **потоково** (`fopen`/`fgets`), рядок за рядком
   — не через `file_get_contents()` цілком.

## Контракт (сигнатури)

```php
function read_lines(string $path): array
function append_log(string $path, string $message): void
function read_csv_as_assoc(string $path): array
function count_words_in_stream(string $path): int
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- `count_words_in_stream()` використовує `fopen()`/`fgets()`/`fclose()`,
  не `file_get_contents()` і не `file()`.
- `read_csv_as_assoc()` бере ключі асоціативних рядків саме з першого
  рядка файлу (заголовків), а не хардкодить їх.

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені
- [ ] можеш пояснити, чому `count_words_in_stream()` навмисно вимагає
      потокового читання, а не `file_get_contents()` + `str_word_count()`

## Бонус (не перевіряється автоматично)

Додай `write_csv(string $path, array $rows): void`, що приймає масив
асоціативних рядків (як повертає `read_csv_as_assoc()`) і записує їх у
CSV-файл із заголовком з ключів першого рядка — операція, симетрична до
`read_csv_as_assoc()`.

<details>
<summary>Підказка 1</summary>

```php
function read_lines(string $path): array
{
    $lines = file($path, FILE_IGNORE_NEW_LINES);
    if ($lines === false) {
        throw new \RuntimeException("Не вдалось прочитати {$path}");
    }

    return $lines;
}

function append_log(string $path, string $message): void
{
    file_put_contents($path, $message . PHP_EOL, FILE_APPEND);
}
```
</details>

<details>
<summary>Підказка 2</summary>

```php
function read_csv_as_assoc(string $path): array
{
    $handle = fopen($path, 'r');
    $headers = fgetcsv($handle);
    $rows = [];
    while (($row = fgetcsv($handle)) !== false) {
        $rows[] = array_combine($headers, $row);
    }
    fclose($handle);

    return $rows;
}
```
</details>

<details>
<summary>Підказка 3</summary>

```php
function count_words_in_stream(string $path): int
{
    $handle = fopen($path, 'r');
    $count = 0;
    while (($line = fgets($handle)) !== false) {
        $count += str_word_count($line);
    }
    fclose($handle);

    return $count;
}
```
</details>
