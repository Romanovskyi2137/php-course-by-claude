<?php

declare(strict_types=1);

// Тема 20: простори імен та автозавантаження — референсний розв'язок.

function register_autoloader(string $baseDir): void
{
    spl_autoload_register(function (string $class) use ($baseDir): void {
        if (!str_starts_with($class, 'App\\')) {
            return;
        }

        $relative = substr($class, strlen('App\\'));
        $path = $baseDir . '/' . str_replace('\\', '/', $relative) . '.php';

        if (is_file($path)) {
            require $path;
        }
    });
}

function make_product(string $name, float $price): \App\Domain\Product
{
    return new \App\Domain\Product($name, $price);
}

function shout_via_autoload(string $text): string
{
    return \App\Support\StringHelper::shout($text);
}
