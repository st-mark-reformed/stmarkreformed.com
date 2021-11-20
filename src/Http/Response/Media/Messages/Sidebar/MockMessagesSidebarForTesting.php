<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages\Sidebar;

use App\Shared\Testing\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Twig\Markup;

use function assert;

trait MockMessagesSidebarForTesting
{
    /**
     * @return MessagesSidebar&MockObject
     */
    protected function mockMessagesSidebar(): mixed
    {
        assert($this instanceof TestCase);

        $mock = $this->createMock(
            MessagesSidebar::class,
        );

        $mock->method($this::anything())->willReturnCallback(
            function (): Markup {
                assert($this instanceof TestCase);

                return $this->genericCall(
                    'MessagesSidebar',
                    new Markup(
                        'MessagesSidebarReturn',
                        'UTF-8',
                    ),
                );
            }
        );

        return $mock;
    }
}
