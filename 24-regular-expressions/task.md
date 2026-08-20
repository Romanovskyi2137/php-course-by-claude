# Завдання 24: регулярні вирази

## Мета

1. Дістати частину рядка через групу захоплення (`preg_match`).
2. Нормалізувати текст через `preg_replace`.
3. Розпарсити напівструктурований рядок через іменовані групи.
4. Коректно розбити кириличний текст на слова — з модифікатором `u`.

## Вимоги

1. `extract_email_domain(string $email)` — повертає домен після `@`, або
   `null`, якщо рядок не схожий на email (немає `@` з чимось після).
2. `normalize_whitespace(string $text)` — схлопує будь-яку послідовність
   пробільних символів (пробіли, табуляції, переноси рядків) в один
   пробіл і обрізає пробіли з країв.
3. `parse_log_line(string $line)` — парсить рядок логу формату
   `[YYYY-MM-DD HH:MM:SS] LEVEL: message` в асоціативний масив
   `['date' => ..., 'level' => ..., 'message' => ...]` через іменовані
   групи; `null`, якщо рядок не відповідає формату.
4. `split_ukrainian_words(string $text)` — повертає масив слів із тексту
   (послідовності літер), коректно для кирилиці; розділові знаки й пробіли
   в результат не потрапляють.

## Контракт (сигнатури)

```php
function extract_email_domain(string $email): ?string
function normalize_whitespace(string $text): string
function parse_log_line(string $line): ?array
function split_ukrainian_words(string $text): array
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- `parse_log_line()` повертає ключі саме `date`, `level`, `message` —
  без зайвих числових індексів у результуючому масиві.
- `split_ukrainian_words()` використовує модифікатор `u` — без нього
  кириличні слова ламаються.

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені
- [ ] можеш пояснити, що станеться в `split_ukrainian_words()`, якщо
      прибрати модифікатор `u` з патерну

## Бонус (не перевіряється автоматично)

Додай `mask_phone_number(string $phone): string`, що замінює всі цифри
номера телефону, крім останніх 4, на `*` (наприклад,
`+380671234567` → `+38067******4567`... точний формат — на твій розсуд,
головне — щоб останні 4 цифри лишались видимими).

<details>
<summary>Підказка 1</summary>

```php
function extract_email_domain(string $email): ?string
{
    if (preg_match('/@([a-zA-Z0-9.\-]+)$/', $email, $matches) === 1) {
        return $matches[1];
    }

    return null;
}
```
</details>

<details>
<summary>Підказка 2</summary>

```php
function parse_log_line(string $line): ?array
{
    $pattern = '/^\[(?<date>\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]\s+(?<level>[A-Z]+):\s+(?<message>.+)$/u';
    if (preg_match($pattern, $line, $m) !== 1) {
        return null;
    }

    return ['date' => $m['date'], 'level' => $m['level'], 'message' => $m['message']];
}
```
`preg_match` з іменованими групами кладе в `$m` **і** іменовані, **і**
числові ключі — відфільтруй лише потрібні.
</details>

<details>
<summary>Підказка 3</summary>

```php
function split_ukrainian_words(string $text): array
{
    return preg_split('/[^\p{L}]+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
}
```
`\p{L}` — будь-яка "буква" в сенсі Unicode (кирилиця, латиниця, будь-яка
мова), а не лише `a-zA-Z`.
</details>
