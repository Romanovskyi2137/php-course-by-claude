<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\runPhpUnitOrSkip;

$target = getenv('CHECK_TARGET') ?: 'starter';

runPhpUnitOrSkip(__DIR__ . "/{$target}");
