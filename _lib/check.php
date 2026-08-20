<?php

declare(strict_types=1);

namespace Course\Check;

// Спільна бібліотека для check.php у темах 01-43. Без залежностей —
// повинна працювати одразу після встановлення PHP, без Composer.
// Використання в темі:
//
//   require __DIR__ . '/../_lib/check.php';
//   use function Course\Check\{section, test, assertSame, summary};
//
//   require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';
//
//   section('Вимога 1: ...');
//   test('опис перевірки', fn() => assertSame(4, 2 + 2));
//
//   exit(summary());

function &state(): array
{
    static $state = [
        'passed' => 0,
        'failed' => 0,
        'skipped' => 0,
    ];

    return $state;
}

function section(string $title): void
{
    echo "\n\033[1m{$title}\033[0m\n";
}

function test(string $name, callable $fn): void
{
    $state = &state();

    try {
        $fn();
        $state['passed']++;
        echo "  \033[32m✓\033[0m {$name}\n";
    } catch (\Throwable $e) {
        $state['failed']++;
        echo "  \033[31m✗\033[0m {$name}\n";
        echo "      \033[31m" . $e->getMessage() . "\033[0m\n";
    }
}

// Використовуй, коли перевірка залежить від зовнішнього ресурсу (напр. БД),
// якого може не бути в оточенні студента — замість провалу з незрозумілою помилкою.
function skip(string $reason): void
{
    $state = &state();
    $state['skipped']++;
    echo "  \033[33m- skip\033[0m {$reason}\n";
}

function fail(string $message): never
{
    throw new \RuntimeException($message);
}

function assertTrue(bool $condition, string $message = 'Очікував true, отримав false'): void
{
    if ($condition !== true) {
        fail($message);
    }
}

function assertFalse(bool $condition, string $message = 'Очікував false, отримав true'): void
{
    if ($condition !== false) {
        fail($message);
    }
}

function assertNull(mixed $value, string $message = 'Очікував null'): void
{
    if ($value !== null) {
        fail($message . '. Отримав: ' . varExport($value));
    }
}

function assertSame(mixed $expected, mixed $actual, string $message = ''): void
{
    if ($expected !== $actual) {
        fail($message !== '' ? $message : sprintf(
            'Очікував %s, отримав %s',
            varExport($expected),
            varExport($actual)
        ));
    }
}

function assertEquals(mixed $expected, mixed $actual, string $message = ''): void
{
    if ($expected != $actual) {
        fail($message !== '' ? $message : sprintf(
            'Очікував %s (нестроге ==), отримав %s',
            varExport($expected),
            varExport($actual)
        ));
    }
}

function assertCount(int $expected, iterable $actual, string $message = ''): void
{
    $items = is_array($actual) ? $actual : iterator_to_array($actual);
    $count = count($items);
    if ($count !== $expected) {
        fail($message !== '' ? $message : "Очікував {$expected} елементів, отримав {$count}");
    }
}

function assertThrows(string $expectedClass, callable $fn, string $message = ''): void
{
    try {
        $fn();
    } catch (\Throwable $e) {
        if (!$e instanceof $expectedClass) {
            fail(sprintf(
                'Очікував виняток %s, отримав %s: %s',
                $expectedClass,
                get_class($e),
                $e->getMessage()
            ));
        }

        return;
    }

    fail($message !== '' ? $message : "Очікував виняток {$expectedClass}, але його не було кинуто");
}

function assertOutput(callable $fn, string $expected, string $message = ''): void
{
    ob_start();
    $fn();
    $actual = (string) ob_get_clean();

    if ($actual !== $expected) {
        fail($message !== '' ? $message : sprintf(
            'Очікував вивід %s, отримав %s',
            varExport($expected),
            varExport($actual)
        ));
    }
}

function assertFunctionExists(string $name): void
{
    if (!function_exists($name)) {
        fail("Функція {$name}() не знайдена — перевір назву й сигнатуру у task.md");
    }
}

function assertClassExists(string $name): void
{
    if (!class_exists($name) && !interface_exists($name) && !enum_exists($name)) {
        fail("Клас/інтерфейс/enum {$name} не знайдено — перевір назву у task.md");
    }
}

function assertMethodExists(string $class, string $method): void
{
    assertClassExists($class);
    if (!method_exists($class, $method)) {
        fail("Метод {$class}::{$method}() не знайдено");
    }
}

// Порівнює лише типи параметрів функції з контрактом із task.md (не назви змінних).
function assertSignature(string $function, array $paramTypes, ?string $returnType = null): void
{
    assertFunctionExists($function);
    $reflection = new \ReflectionFunction($function);

    $actualParams = array_map(
        static fn (\ReflectionParameter $p): string => (string) $p->getType(),
        $reflection->getParameters()
    );

    if ($actualParams !== $paramTypes) {
        fail(sprintf(
            'Сигнатура %s(%s) не збігається з контрактом (%s) із task.md',
            $function,
            implode(', ', $actualParams),
            implode(', ', $paramTypes)
        ));
    }

    if ($returnType !== null) {
        $actualReturn = (string) $reflection->getReturnType();
        if ($actualReturn !== $returnType) {
            fail("Тип повернення {$function}(): {$actualReturn}, очікував {$returnType}");
        }
    }
}

function assertPropertyIsReadonly(string $class, string $property): void
{
    $reflection = new \ReflectionProperty($class, $property);
    assertTrue(
        $reflection->isReadOnly(),
        "Властивість {$class}::\${$property} має бути readonly"
    );
}

function assertMethodIsStatic(string $class, string $method): void
{
    assertMethodExists($class, $method);
    $reflection = new \ReflectionMethod($class, $method);
    assertTrue(
        $reflection->isStatic(),
        "Метод {$class}::{$method}() має бути static"
    );
}

function assertEnumIsBacked(string $enum, string $backingType): void
{
    $reflection = new \ReflectionEnum($enum);
    assertTrue($reflection->isBacked(), "Enum {$enum} має бути backed enum (: {$backingType})");
    assertSame(
        $backingType,
        (string) $reflection->getBackingType(),
        "Enum {$enum} має бути backed типом {$backingType}"
    );
}

// Скануємо перші токени файлу, а не робимо simple string search по всьому вмісту,
// щоб рядок 'strict_types=1' у коментарі чи докстрінгу не давав хибний позитив.
function assertStrictTypesDeclared(string $file): void
{
    $contents = file_get_contents($file);
    if ($contents === false) {
        fail("Не вдалось прочитати файл {$file}");
    }

    $tokens = token_get_all($contents);
    $head = implode('', array_map(
        static fn ($t) => is_array($t) ? $t[1] : $t,
        array_slice($tokens, 0, 12)
    ));

    assertTrue(
        str_contains($head, 'strict_types') && str_contains($head, '1'),
        basename($file) . ' має починатись з declare(strict_types=1);'
    );
}

function varExport(mixed $value): string
{
    return var_export($value, true);
}

// Для тем блоку E/F (реальний MySQL через _env/docker-compose.yml). Повертає null
// і виводить skip() замість падіння з незрозумілою помилкою з'єднання, якщо конфіг
// не скопійований чи контейнер не піднятий — виклич і одразу exit(summary()), якщо null.
function connectTestDatabase(): ?\PDO
{
    $configPath = __DIR__ . '/../_env/config.local.php';
    if (!file_exists($configPath)) {
        skip('БД не налаштована: скопіюй _env/config.example.php у _env/config.local.php (див. _env/README.md)');

        return null;
    }

    $db = (require $configPath)['db'];
    $dsn = "mysql:host={$db['host']};port={$db['port']};dbname={$db['database']};charset={$db['charset']}";

    try {
        return new \PDO($dsn, $db['username'], $db['password'], [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);
    } catch (\PDOException $e) {
        skip("MySQL недоступний ({$e->getMessage()}): підніми контейнер — "
            . 'docker compose -f _env/docker-compose.yml up -d');

        return null;
    }
}

// Для тем блоку E, що потребують Composer (з 38-composer-and-psr4). skip() замість
// падіння, якщо студент ще не виконав composer install/dump-autoload у папці теми.
function requireComposerAutoload(string $vendorAutoloadPath): bool
{
    if (!file_exists($vendorAutoloadPath)) {
        skip("vendor/autoload.php не знайдено: виконай composer install (або composer dump-autoload) "
            . 'у відповідній папці — дивись task.md');

        return false;
    }

    require $vendorAutoloadPath;

    return true;
}

// Для тем з 44-testing-with-phpunit: check.php стає тонким запускачем реального
// PHPUnit замість власних test()/assert*() — саме так, як описано в README.
// Ніколи не повертається: або skip() + exit(summary()), або exit(код виходу PHPUnit).
function runPhpUnitOrSkip(string $projectDir): never
{
    $phpunitBin = $projectDir . '/vendor/bin/phpunit';
    if (!file_exists($phpunitBin)) {
        skip('vendor/bin/phpunit не знайдено: виконай composer install у '
            . basename($projectDir) . '/ — дивись task.md');

        exit(summary());
    }

    passthru(escapeshellarg($phpunitBin) . ' --colors=always --configuration ' . escapeshellarg($projectDir), $exitCode);

    exit($exitCode);
}

function summary(): int
{
    $state = &state();
    $total = $state['passed'] + $state['failed'];

    echo "\n";
    if ($state['failed'] === 0) {
        echo "\033[1;32mУсі перевірки пройдені: {$state['passed']}/{$total}\033[0m";
    } else {
        echo "\033[1;31mПровалено: {$state['failed']}/{$total}\033[0m";
    }

    if ($state['skipped'] > 0) {
        echo " \033[33m({$state['skipped']} пропущено)\033[0m";
    }
    echo "\n";

    return $state['failed'] === 0 ? 0 : 1;
}
