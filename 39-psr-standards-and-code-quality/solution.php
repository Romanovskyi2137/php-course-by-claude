<?php

declare(strict_types=1);

// Тема 39: PSR-стандарти та якість коду — референсний розв'язок.

function psr4_file_path(string $fqcn, string $namespacePrefix, string $baseDir): string
{
    $relative = substr($fqcn, strlen($namespacePrefix));
    $relativePath = str_replace('\\', '/', $relative);

    return rtrim($baseDir, '/') . '/' . $relativePath . '.php';
}

function is_valid_psr1_class_name(string $name): bool
{
    return preg_match('/^[A-Z][A-Za-z0-9]*$/', $name) === 1;
}

function is_valid_psr1_method_name(string $name): bool
{
    return preg_match('/^[a-z][A-Za-z0-9]*$/', $name) === 1;
}

final class ReportGenerator
{
    public function generateSummary(array $items): string
    {
        $total = 0;
        foreach ($items as $item) {
            $total += $item;
        }

        return "Total: {$total}";
    }
}
