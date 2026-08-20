<?php

declare(strict_types=1);

// Checkpoint 5: front controller. Готовий код — не редагуй.
// Тестовий Bearer-токен для POST/DELETE (лише для локальної розробки): 'demo-token'.

require __DIR__ . '/../vendor/autoload.php';

use App\Api;
use App\Container;
use App\InMemoryBookmarkRepository;
use App\Router;

$container = new Container();

$container->set('repository', fn () => new InMemoryBookmarkRepository());
$container->set('api', fn (Container $c) => new Api($c->get('repository'), ['demo-token' => 'demo-user']));

/** @var Api $api */
$api = $container->get('api');

$router = new Router();
$router->register('GET', '/bookmarks', fn (array $params) => $api->list($params, $_SERVER, []));
$router->register('GET', '/bookmarks/{id}', fn (array $params) => $api->show($params, $_SERVER, []));
$router->register('POST', '/bookmarks', function (array $params) use ($api) {
    $body = json_decode((string) file_get_contents('php://input'), true) ?? [];

    return $api->create($params, $_SERVER, $body);
});
$router->register('DELETE', '/bookmarks/{id}', fn (array $params) => $api->delete($params, $_SERVER, []));

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$result = $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $path);

http_response_code($result['status']);
foreach ($result['headers'] ?? ['Content-Type' => 'application/json'] as $name => $value) {
    header("{$name}: {$value}");
}
echo is_string($result['body']) ? $result['body'] : json_encode($result['body']);
