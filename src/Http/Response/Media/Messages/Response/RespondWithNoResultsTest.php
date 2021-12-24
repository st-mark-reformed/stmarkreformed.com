<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages\Response;

use App\Http\Components\Hero\MockHeroFactoryForTesting;
use App\Http\Entities\Meta;
use App\Http\Pagination\Pagination;
use App\Http\Response\Media\Messages\Breadcrumbs\MockBreadcrumbBuilderForTesting;
use App\Http\Response\Media\Messages\Params;
use App\Http\Response\Media\Messages\SearchForm\MockSearchFormBuilderForTesting;
use App\Http\Response\Media\Messages\Sidebar\MockMessagesSidebarForTesting;
use App\Messages\RetrieveMessages\MessagesResult;
use App\Shared\Testing\MockResponseFactoryForTesting;
use App\Shared\Testing\MockTwigForTesting;
use App\Shared\Testing\TestCase;

use function assert;
use function is_array;

class RespondWithNoResultsTest extends TestCase
{
    use MockTwigForTesting;
    use MockHeroFactoryForTesting;
    use MockMessagesSidebarForTesting;
    use MockBreadcrumbBuilderForTesting;
    use MockSearchFormBuilderForTesting;
    use MockResponseFactoryForTesting;

    private RespondWithNoResults $responder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responder = new RespondWithNoResults(
            twig: $this->mockTwig(),
            heroFactory: $this->mockHeroFactory(),
            messagesSidebar: $this->mockMessagesSidebar(),
            breadcrumbBuilder: $this->mockBreadcrumbBuilder(),
            searchFormBuilder: $this->mockSearchFormBuilder(),
            responseFactory: $this->mockResponseFactory(),
        );
    }

    public function testRespond(): void
    {
        $params = $this->createMock(Params::class);

        $result = $this->createMock(MessagesResult::class);

        $pagination = new Pagination();

        self::assertSame(
            $this->response,
            $this->responder->respond(
                params: $params,
                result: $result,
                pagination: $pagination,
            ),
        );

        self::assertCount(7, $this->calls);

        $call1 = $this->calls[0];

        assert(is_array($call1));

        self::assertSame(
            [
                'object' => 'ResponseFactoryInterface',
                'method' => 'createResponse',
                'args' => [],
            ],
            $call1
        );

        $call2 = $this->calls[1];

        assert(is_array($call2));

        self::assertSame(
            [
                'object' => 'HeroFactory',
                'method' => 'createFromDefaults',
                'args' => [
                    0,
                    '',
                    null,
                    'Messages from St. Mark',
                ],
            ],
            $call2
        );

        $call3 = $this->calls[2];

        assert(is_array($call3));

        self::assertSame(
            [
                'object' => 'MessagesSidebar',
                'method' => 'render',
                'args' => [],
            ],
            $call3
        );

        $call4 = $this->calls[3];

        assert(is_array($call4));

        self::assertSame(
            [
                'object' => 'BreadcrumbBuilder',
                'method' => 'fromParams',
                'args' => [$params],
            ],
            $call4
        );

        $call5 = $this->calls[4];

        assert(is_array($call5));

        self::assertSame(
            [
                'object' => 'SearchFormBuilder',
                'method' => 'fromParams',
                'args' => [$params],
            ],
            $call5
        );

        $call6 = $this->calls[5];

        assert(is_array($call6));

        self::assertSame(
            'TwigEnvironment',
            $call6['object'],
        );

        self::assertSame(
            'render',
            $call6['method'],
        );

        $call6Args = $call6['args'];

        assert(is_array($call6Args));

        self::assertCount(2, $call6Args);

        self::assertSame(
            '@app/Http/Response/Media/Messages/Response/RespondWithNoResults.twig',
            $call6Args[0],
        );

        $twigContext = $call6Args[1];

        assert(is_array($twigContext));

        self::assertCount(5, $twigContext);

        self::assertSame(
            $this->hero,
            $twigContext['hero'],
        );

        $meta = $twigContext['meta'];

        assert($meta instanceof Meta);

        self::assertSame(
            'Messages from St. Mark',
            $meta->metaTitle(),
        );

        self::assertSame(
            'MessagesSidebarReturn',
            (string) $twigContext['sideBarMarkup'],
        );

        self::assertSame(
            'BreadcrumbBuilderReturn',
            (string) $twigContext['breadcrumbs'],
        );

        self::assertSame(
            'SearchFormBuilderReturn',
            (string) $twigContext['searchForm'],
        );

        $call7 = $this->calls[6];

        assert(is_array($call7));

        self::assertSame(
            [
                'object' => 'StreamInterface',
                'method' => 'write',
                'args' => ['TwigRender'],
            ],
            $call7,
        );
    }
}
