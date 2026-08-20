# 26. Файли та потоки

> Що ти вже вмієш: `fs`/`open()`/`java.io` з іншої мови · Що додає ця
> тема: коли брати прості функції "все за раз" (`file_get_contents`), а
> коли — потокове читання рядок за рядком, і як парсити CSV без ручного
> `explode(',')` · ~25 хв

## 1. Якщо ти прийшов з JS / Python / Java

- `file_get_contents()`/`file_put_contents()` — прямі аналоги Python
  `Path.read_text()`/`write_text()` чи Node `fs.readFileSync`/
  `writeFileSync`: весь файл за раз, в пам'ять.
- `fopen()`/`fgets()`/`fclose()` — низькорівневий стрімінг-API, схожий
  на Python `open(path) as f: for line in f` чи Java
  `BufferedReader`/`FileReader`: читаєш файл по шматках, не завантажуючи
  все одразу — важливо для великих файлів.
- `\SplFileObject` — об'єктна обгортка над файлом з вбудованою підтримкою
  CSV, схожа на `csv.reader` в Python, тільки як клас, а не генератор
  функції.

## 2. Ключові терміни

- **Handle (дескриптор потоку)** — значення, яке повертає `fopen()`;
  "покажчик" на відкритий файл/потік, через який читаєш/пишеш і який
  обов'язково закриваєш `fclose()`.
- **Stream (потік)** — узагальнений PHP-механізм вводу/виводу: той самий
  `fopen()`/`fgets()`-API працює не лише для файлів на диску, а й для
  `php://stdin`, HTTP-з'єднань, `php://memory` тощо.
- **`FILE_APPEND`** — прапорець `file_put_contents()`, що дописує в
  кінець файлу замість перезапису.
- **CSV (comma-separated values)** — текстовий формат таблиці, де
  стовпці розділені комами (чи іншим символом), а рядки — переносами.

## 3. Теорія

### 3.1 "Все за раз": `file_get_contents`/`file_put_contents`

```php
$content = file_get_contents('data.txt');       // весь файл як string
$lines = file('data.txt', FILE_IGNORE_NEW_LINES); // весь файл як array рядків, без \n

file_put_contents('out.txt', "Привіт\n");              // перезаписує файл
file_put_contents('out.txt', "Ще рядок\n", FILE_APPEND); // дописує в кінець
```

Обидві функції повертають `false` при помилці (файл не існує, немає
прав) — а не кидають виняток; для передбачуваного коду перевіряй `false`
явно (розділ 4).

### 3.2 Потокове читання: `fopen`/`fgets`/`fclose`

```php
$handle = fopen('big-file.log', 'r');
if ($handle === false) {
    throw new \RuntimeException('Не вдалось відкрити файл');
}

while (($line = fgets($handle)) !== false) {
    // обробка одного рядка за раз — файл ніколи повністю не в пам'яті
    echo $line;
}

fclose($handle);
```

Порівняно з `file_get_contents()`, потокове читання не завантажує весь
файл у пам'ять одразу — критично для файлів у гігабайти, де `file()`
просто вичерпає доступну пам'ять.

### 3.3 CSV: `fgetcsv()` і `\SplFileObject`

```php
$handle = fopen('data.csv', 'r');
$headers = fgetcsv($handle);              // перший рядок — заголовки
while (($row = fgetcsv($handle)) !== false) {
    $assoc = array_combine($headers, $row); // ['id' => '1', 'name' => 'Anna']
}
fclose($handle);

// Об'єктний варіант — SplFileObject:
$file = new \SplFileObject('data.csv');
$file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY);
foreach ($file as $row) {
    // $row — вже масив колонок цього рядка, без ручного fgetcsv()
}
```

`\SplFileObject` реалізує `\Iterator` (тема 22) — тому працює прямо в
`foreach`, без ручного циклу `while`.

### 3.4 `php://`-потоки

```php
$input = file_get_contents('php://stdin');   // весь стандартний ввід
$memory = fopen('php://memory', 'r+');       // тимчасовий "файл" лише в пам'яті,
                                                // зручно для тестів без диска
```

## 4. Підводні камені

- `file_get_contents()`/`fopen()` повертають `false` при помилці — **не**
  кидають виняток. Код, що ігнорує це (`$content = file_get_contents(...)`
  без перевірки), при відсутньому файлі отримає `false`, і подальші
  рядкові операції над ним впадуть із незрозумілим `TypeError` десь
  далі, а не там, де насправді сталась помилка.
- Забутий `fclose()` не "падає" одразу, але тримає файловий дескриптор
  відкритим, поки скрипт не завершиться (або GC не забере об'єкт) — у
  довгоживучих процесах (черги, демони) це витік ресурсів.
- `fgetcsv()`/`SplFileObject::READ_CSV` за замовчуванням не мають
  спеціальної обробки UTF-8 BOM (byte order mark) на початку файлу —
  якщо CSV експортовано з Excel, перший заголовок може мати "невидимий"
  префікс, і порівняння `$headers[0] === 'id'` несподівано провалиться.

## 5. Сучасний PHP 8.3+

`\SplFileObject` і функції сімейства `fgetcsv()` не мали кардинальних
змін у 8.3. Загальна рекомендація лишається: прості функції
(`file_get_contents`/`file()`) для файлів, які точно влізуть у пам'ять і
де важлива простота коду; потокове читання (`fopen`/`fgets`) — коли файл
потенційно великий або дані приходять з зовнішнього джерела (мережа,
stdin).

## 6. Контекст екосистеми

Реальні застосунки рідко пишуть у файли напряму на постійній основі —
частіше це БД (блок E) чи спеціалізовані сервіси логування. Але прямі
файлові операції залишаються базовими для: читання конфігів, імпорту/
експорту CSV, тимчасових файлів під час обробки завантажень (тема 30),
і простого файлового логування там, де повноцінний PSR-3-логер (тема 46)
— надмірність.

## 7. Документація

- https://www.php.net/manual/en/function.file-get-contents.php
- https://www.php.net/manual/en/function.fgetcsv.php
- https://www.php.net/manual/en/class.splfileobject.php
- https://www.php.net/manual/en/wrappers.php.php — `php://`-потоки

## 8. Питання для самоперевірки

1. Коли варто брати `file_get_contents()`, а коли — потокове читання
   через `fopen`/`fgets`?
2. Що поверне `file_get_contents()`, якщо файл не існує — і чому це
   небезпечно ігнорувати?
3. Чим `\SplFileObject::READ_CSV` зручніший за ручний цикл з `fgetcsv()`?
4. Що станеться, якщо забути `fclose()` в довгоживучому процесі?
