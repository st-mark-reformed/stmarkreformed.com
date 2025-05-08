<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages\Sidebar;

use App\Http\Components\Link\Link;
use App\Shared\Testing\MockTwigForTesting;
use App\Shared\Testing\TestCase;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

use function array_map;
use function assert;
use function is_array;

class MessagesSidebarTest extends TestCase
{
    use MockTwigForTesting;
    use MockRetrieveMostRecentSeriesForTesting;
    use MockRetrieveLeadersWithMessagesForTesting;

    private MessagesSidebar $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new MessagesSidebar(
            twig: $this->mockTwig(),
            retrieveMostRecentSeries: $this->mockRetrieveMostRecentSeries(),
            retrieveLeadersWithMessages: $this->mockRetrieveLeadersWithMessages(),
        );
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function testRender(): void
    {
        self::assertSame(
            'TwigRender',
            (string) $this->service->render(),
        );

        self::assertCount(3, $this->calls);

        self::assertSame(
            [
                'object' => 'RetrieveLeadersWithMessages',
                'method' => 'retrieve',
                'args' => [],
            ],
            $this->calls[0],
        );

        self::assertSame(
            [
                'object' => 'RetrieveMostRecentSeries',
                'method' => 'retrieve',
                'args' => [],
            ],
            $this->calls[1],
        );

        $call3 = $this->calls[2];

        self::assertSame(
            'TwigEnvironment',
            $call3['object'],
        );

        self::assertSame(
            'render',
            $call3['method'],
        );

        $call3Args = $call3['args'];

        assert(is_array($call3Args));

        self::assertCount(2, $call3Args);

        self::assertSame(
            '@app/Http/Response/Media/Messages/Sidebar/MessagesSidebar.twig',
            $call3Args[0],
        );

        $twigContext = $call3Args[1];

        assert(is_array($twigContext));

        $leaders = $twigContext['leaders'];

        assert(is_array($leaders));

        self::assertCount(2, $leaders);

        self::assertSame(
            [
                [
                    'isEmpty' => false,
                    'content' => 'Test Leader 1',
                    'href' => 'test-leader-1',
                    'newWindow' => false,
                ],
                [
                    'isEmpty' => false,
                    'content' => 'Test Leader 2',
                    'href' => 'test-leader-2',
                    'newWindow' => false,
                ],
            ],
            array_map(
                static fn (Link $link) => [
                    'isEmpty' => $link->isEmpty(),
                    'content' => $link->content(),
                    'href' => $link->href(),
                    'newWindow' => $link->newWindow(),
                ],
                $leaders,
            ),
        );

        $series = $twigContext['series'];

        assert(is_array($series));

        self::assertCount(2, $series);

        self::assertSame(
            [
                [
                    'isEmpty' => false,
                    'content' => 'Test Series 1',
                    'href' => 'test-series-1',
                    'newWindow' => false,
                ],
                [
                    'isEmpty' => false,
                    'content' => 'Test Series 2',
                    'href' => 'test-series-2',
                    'newWindow' => false,
                ],
            ],
            array_map(
                static fn (Link $link) => [
                    'isEmpty' => $link->isEmpty(),
                    'content' => $link->content(),
                    'href' => $link->href(),
                    'newWindow' => $link->newWindow(),
                ],
                $series,
            ),
        );
    }
}
