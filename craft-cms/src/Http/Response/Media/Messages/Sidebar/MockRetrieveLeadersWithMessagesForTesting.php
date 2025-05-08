<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages\Sidebar;

use App\Http\Components\Link\Link;
use App\Shared\Testing\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

use function assert;

trait MockRetrieveLeadersWithMessagesForTesting
{
    /**
     * @return RetrieveLeadersWithMessages&MockObject
     */
    protected function mockRetrieveLeadersWithMessages(): mixed
    {
        assert($this instanceof TestCase);

        $mock = $this->createMock(
            RetrieveLeadersWithMessages::class,
        );

        $mock->method($this::anything())->willReturnCallback(
            function (): array {
                assert($this instanceof TestCase);

                return $this->genericCall(
                    object: 'RetrieveLeadersWithMessages',
                    return: [
                        new Link(
                            isEmpty: false,
                            content: 'Test Leader 1',
                            href: 'test-leader-1',
                        ),
                        new Link(
                            isEmpty: false,
                            content: 'Test Leader 2',
                            href: 'test-leader-2',
                        ),
                    ],
                );
            }
        );

        return $mock;
    }
}
