<?php

declare(strict_types=1);

namespace App\Tests;

use App\Container;
use PHPUnit\Framework\TestCase;

final class ContainerTest extends TestCase
{
    public function testGetReturnsFactoryResult(): void
    {
        $container = new Container();
        $container->set('greeting', fn () => 'hello');

        $this->assertSame('hello', $container->get('greeting'));
    }

    public function testGetReturnsSameInstanceOnRepeatedCalls(): void
    {
        $container = new Container();
        $container->set('service', fn () => new \stdClass());

        $this->assertSame($container->get('service'), $container->get('service'));
    }

    public function testFactoryIsCalledLazilyAndOnlyOnce(): void
    {
        $calls = 0;
        $container = new Container();
        $container->set('service', function () use (&$calls) {
            $calls++;
            return new \stdClass();
        });

        $this->assertSame(0, $calls);
        $container->get('service');
        $container->get('service');
        $this->assertSame(1, $calls);
    }

    public function testHasReflectsRegisteredIds(): void
    {
        $container = new Container();
        $container->set('service', fn () => 'x');

        $this->assertTrue($container->has('service'));
        $this->assertFalse($container->has('unknown'));
    }

    public function testGetThrowsForUnregisteredId(): void
    {
        $this->expectException(\OutOfBoundsException::class);

        (new Container())->get('unknown');
    }

    public function testFactoryReceivesContainerItself(): void
    {
        $container = new Container();
        $container->set('a', fn () => 'value-a');
        $container->set('b', fn (Container $c) => $c->get('a') . '-b');

        $this->assertSame('value-a-b', $container->get('b'));
    }
}
