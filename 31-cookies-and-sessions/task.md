# Завдання 31: куки та сесії

## Мета

1. Побудувати безпечні опції для сесійної куки.
2. Написати чисту логіку таймауту неактивності.
3. Змоделювати, що саме мають зробити дані сесії при логіні/логауті —
   без виклику реальних `session_*`-функцій (вони не працюють у CLI,
   `lecture.md` розділ 4).

Усі функції працюють із масивом, що **імітує** `$_SESSION`, а не з
реальною суперглобальною змінною — так їх можна перевірити в CLI. Живий
`session_start()` побачиш у `public/`.

## Вимоги

1. `build_session_cookie_options(bool $secure)` — повертає масив опцій
   для `session_set_cookie_params()`/`setcookie()`:
   `['lifetime' => 0, 'path' => '/', 'httponly' => true, 'samesite' =>
   'Lax', 'secure' => $secure]` (`lifetime: 0` — кука живе, поки не
   закриють браузер).
2. `is_session_expired(int $lastActivity, int $now, int $maxIdleSeconds)`
   — `true`, якщо з моменту `$lastActivity` минуло **більше**
   `$maxIdleSeconds`.
3. `mark_authenticated(array $session, int $userId)` — повертає **новий**
   масив сесії з `'user_id' => $userId` і `'authenticated_at' => true`
   доданими до вже наявних ключів `$session` (не змінює вхідний масив).
4. `logout(array $session)` — повертає новий масив сесії, з якого
   видалені ключі `user_id` і `authenticated_at`; решта ключів (наприклад,
   `theme` — налаштування, не повʼязані з автентифікацією) лишається.

## Контракт (сигнатури)

```php
function build_session_cookie_options(bool $secure): array
function is_session_expired(int $lastActivity, int $now, int $maxIdleSeconds): bool
function mark_authenticated(array $session, int $userId): array
function logout(array $session): array
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- Жодна з функцій не викликає `session_start()`/`setcookie()`/не читає
  `$_SESSION` напряму — увесь стан приходить і повертається через параметри.
- `mark_authenticated()`/`logout()` не змінюють переданий `$session` "на
  місці" — масиви в PHP копіюються за значенням (тема 09), тож повернення
  нового масиву тут природне, не спеціальний трюк.

## Як перевірити

```bash
php check.php
```

Далі — реальні сесії й куки, поза автоматичною перевіркою:

```bash
php -S localhost:8000 -t public
```

Відкрий `http://localhost:8000/` — увійди, онови сторінку (сесія
переживає), вийди. Подивись у DevTools → Application → Cookies, що
`PHPSESSID` має `HttpOnly`.

## Definition of Done

- [ ] усі перевірки check.php зелені
- [ ] у браузері реально видно, що сесія переживає оновлення сторінки, а
      логаут її скидає
- [ ] можеш пояснити, чому `session_regenerate_id(true)` варто викликати
      саме в момент логіну, а не будь-коли

## Бонус (не перевіряється автоматично)

Додай `session_summary(array $session): string` — повертає `'anonymous'`
якщо немає `user_id`, інакше `"user #{$id}"`.

<details>
<summary>Підказка 1</summary>

```php
function build_session_cookie_options(bool $secure): array
{
    return [
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => $secure,
    ];
}

function is_session_expired(int $lastActivity, int $now, int $maxIdleSeconds): bool
{
    return ($now - $lastActivity) > $maxIdleSeconds;
}
```
</details>

<details>
<summary>Підказка 2</summary>

```php
function mark_authenticated(array $session, int $userId): array
{
    $session['user_id'] = $userId;
    $session['authenticated_at'] = true;

    return $session;
}
```
</details>

<details>
<summary>Підказка 3</summary>

```php
function logout(array $session): array
{
    unset($session['user_id'], $session['authenticated_at']);

    return $session;
}
```
</details>
