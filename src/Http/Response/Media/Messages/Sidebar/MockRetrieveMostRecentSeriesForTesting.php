<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages\Sidebar;

use App\Http\Components\Link\Link;
use App\Shared\Testing\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

use function assert;

trait MockRetrieveMostRecentSeriesForTesting
{
    /**
     * @return RetrieveMostRecentSeries&MockObject
     */
    protected function mockRetrieveMostRecentSeries(): mixed
    {
        assert($this instanceof TestCase);

        $mock = $this->createMock(
            RetrieveMostRecentSeries::class,
        );

        $mock->method($this::anything())->willReturnCallback(
            function (): array {
                assert($this instanceof TestCase);

                return $this->genericCall(
                    object: 'RetrieveMostRecentSeries',
                    return: [
                        new Link(
                            isEmpty: false,
                            content: 'Test Series 1',
                            href: 'test-series-1',
                        ),
                        new Link(
                            isEmpty: false,
                            content: 'Test Series 2',
                            href: 'test-series-2',
                        ),
                    ],
                );
            }
        );

        return $mock;
    }
}
