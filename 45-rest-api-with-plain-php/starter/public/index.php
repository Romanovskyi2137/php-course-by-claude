<?php

declare(strict_types=1);

// Тема 45: реальний REST API на власному Router. Готовий код — не редагуй.
// Запусти: php -S localhost:8000 -t public

require __DIR__ . '/../vendor/autoload.php';

use App\Router;

use function App\json_response;

$router = new Router();

$router->register('GET', '/users/{id}', function (array $params) {
    return json_response(['id' => $params['id'], 'name' => "User #{$params['id']}"]);
});

$router->register('GET', '/users', function (array $params) {
    return json_response([
        ['id' => '1', 'name' => 'User #1'],
        ['id' => '2', 'name' => 'User #2'],
    ]);
});

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$result = $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $path);

http_response_code($result['status']);
foreach ($result['headers'] ?? ['Content-Type' => 'application/json'] as $name => $value) {
    header("{$name}: {$value}");
}
// dispatch() сам повертає готовий JSON-рядок лише коли handler викликав json_response();
// 404/405 від самого Router — це ще "сирий" масив, кодуємо його тут.
echo is_string($result['body']) ? $result['body'] : json_encode($result['body']);
