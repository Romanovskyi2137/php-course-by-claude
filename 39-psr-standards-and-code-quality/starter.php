<?php

declare(strict_types=1);

// Тема 39: PSR-стандарти та якість коду. Опис завдання — у task.md.

// TODO (вимога 1): шлях до файлу за PSR-4-мапінгом.
function psr4_file_path(string $fqcn, string $namespacePrefix, string $baseDir): string
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 2): StudlyCaps-перевірка імені класу.
function is_valid_psr1_class_name(string $name): bool
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 3): camelCase-перевірка імені методу.
function is_valid_psr1_method_name(string $name): bool
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 4): перейменуй report_generator -> ReportGenerator,
// generate_summary -> generateSummary(array $items): string, перевідформатуй
// під 4 пробіли (без табуляції) — поведінку (суму) не міняй.
class report_generator
{
	public function generate_summary($items)
	{
		$total = 0;
		foreach ($items as $item) {
			$total += $item;
		}
		return "Total: {$total}";
	}
}
