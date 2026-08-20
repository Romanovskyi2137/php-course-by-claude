<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase;

use function App\classify_file;
use function App\count_files_by_layer;

final class FunctionsTest extends TestCase
{
    public function testClassifiesController(): void
    {
        $this->assertSame('controller', classify_file('app/Controllers/UserController.php'));
        $this->assertSame('controller', classify_file('src/UserController.php'));
    }

    public function testClassifiesModel(): void
    {
        $this->assertSame('model', classify_file('app/Models/User.php'));
        $this->assertSame('model', classify_file('src/UserModel.php'));
    }

    public function testClassifiesView(): void
    {
        $this->assertSame('view', classify_file('resources/Views/home.php'));
        $this->assertSame('view', classify_file('templates/home.blade.php'));
        $this->assertSame('view', classify_file('templates/home.twig'));
    }

    public function testClassifiesConfig(): void
    {
        $this->assertSame('config', classify_file('config/database.php'));
        $this->assertSame('config', classify_file('.env'));
    }

    public function testClassifiesPublic(): void
    {
        $this->assertSame('public', classify_file('public/index.php'));
        $this->assertSame('public', classify_file('public/assets/style.css'));
    }

    public function testClassifiesUnknown(): void
    {
        $this->assertSame('unknown', classify_file('README.md'));
        $this->assertSame('unknown', classify_file('composer.json'));
    }

    public function testCountFilesByLayer(): void
    {
        $counts = count_files_by_layer([
            'app/Controllers/UserController.php',
            'app/Controllers/OrderController.php',
            'app/Models/User.php',
            'README.md',
        ]);

        $this->assertSame(['controller' => 2, 'model' => 1, 'unknown' => 1], $counts);
    }

    public function testCountFilesByLayerOmitsZeroCounts(): void
    {
        $counts = count_files_by_layer(['app/Models/User.php']);

        $this->assertArrayNotHasKey('controller', $counts);
        $this->assertSame(1, $counts['model']);
    }
}
