<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages\Response;

use App\Http\Components\Hero\MockHeroFactoryForTesting;
use App\Http\Entities\Meta;
use App\Http\PageBuilder\Shared\AudioPlayer\MockAudioPlayerContentModelFactoryForTesting;
use App\Http\PageBuilder\Shared\AudioPlayer\MockRenderAudioPlayerFromContentModelForTesting;
use App\Http\Pagination\MockRenderPaginationForTesting;
use App\Http\Pagination\Pagination;
use App\Http\Response\Media\Messages\Breadcrumbs\MockBreadcrumbBuilderForTesting;
use App\Http\Response\Media\Messages\Params;
use App\Http\Response\Media\Messages\SearchForm\MockSearchFormBuilderForTesting;
use App\Http\Response\Media\Messages\Sidebar\MockMessagesSidebarForTesting;
use App\Messages\RetrieveMessages\MessagesResult;
use App\Shared\Testing\MockResponseFactoryForTesting;
use App\Shared\Testing\MockTwigForTesting;
use App\Shared\Testing\TestCase;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\InvalidConfigException;

use function assert;
use function is_array;

class RespondWithResultsTest extends TestCase
{
    use MockTwigForTesting;
    use MockHeroFactoryForTesting;
    use MockMessagesSidebarForTesting;
    use MockRenderPaginationForTesting;
    use MockBreadcrumbBuilderForTesting;
    use MockSearchFormBuilderForTesting;
    use MockResponseFactoryForTesting;
    use MockAudioPlayerContentModelFactoryForTesting;
    use MockRenderAudioPlayerFromContentModelForTesting;

    private RespondWithResults $responder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responder = new RespondWithResults(
            twig: $this->mockTwig(),
            heroFactory: $this->mockHeroFactory(),
            messagesSidebar: $this->mockMessagesSidebar(),
            renderPagination: $this->mockRenderPagination(),
            breadcrumbBuilder: $this->mockBreadcrumbBuilder(),
            searchFormBuilder: $this->mockSearchFormBuilder(),
            responseFactory: $this->mockResponseFactory(),
            playerModelFactory: $this->mockAudioPlayerContentModelFactory(),
            renderAudioPlayer: $this->mockRenderAudioPlayerFromContentModel(),
        );
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function testRespond(): void
    {
        $entry1 = $this->createMock(Entry::class);

        $entry2 = $this->createMock(Entry::class);

        $params = $this->createMock(Params::class);

        $result = new MessagesResult(
            123,
            [
                $entry1,
                $entry2,
            ],
        );

        $pagination = new Pagination();

        self::assertSame(
            $this->response,
            $this->responder->respond(
                params: $params,
                result: $result,
                pagination: $pagination,
            ),
        );

        self::assertCount(10, $this->calls);

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
            [
                'object' => 'RenderPagination',
                'method' => 'render',
                'args' => [$pagination],
            ],
            $call6,
        );

        $call7 = $this->calls[6];

        assert(is_array($call7));

        self::assertSame(
            [
                'object' => 'AudioPlayerContentModelFactory',
                'method' => 'makeFromSermonEntry',
                'args' => [$entry1],
            ],
            $call7,
        );

        $call8 = $this->calls[7];

        assert(is_array($call8));

        self::assertSame(
            [
                'object' => 'AudioPlayerContentModelFactory',
                'method' => 'makeFromSermonEntry',
                'args' => [$entry2],
            ],
            $call8,
        );

        $call9 = $this->calls[8];

        assert(is_array($call9));

        self::assertSame(
            'TwigEnvironment',
            $call9['object'],
        );

        self::assertSame(
            'render',
            $call9['method'],
        );

        $call9Args = $call9['args'];

        assert(is_array($call9Args));

        self::assertCount(2, $call9Args);

        self::assertSame(
            '@app/Http/Response/Media/Messages/Response/RespondWithResults.twig',
            $call9Args[0],
        );

        $twigContext = $call9Args[1];

        assert(is_array($twigContext));

        self::assertCount(7, $twigContext);

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

        self::assertSame(
            'RenderPaginationReturn',
            (string) $twigContext['pagination'],
        );

        $audioPlayers = $twigContext['audioPlayers'];

        assert(is_array($audioPlayers));

        self::assertCount(2, $audioPlayers);

        self::assertSame(
            'RenderAudioPlayerFromContentModelReturnString',
            (string) $audioPlayers[0],
        );

        self::assertSame(
            'RenderAudioPlayerFromContentModelReturnString',
            (string) $audioPlayers[1],
        );

        $call10 = $this->calls[9];

        assert(is_array($call10));

        self::assertSame(
            [
                'object' => 'StreamInterface',
                'method' => 'write',
                'args' => ['TwigRender'],
            ],
            $call10,
        );
    }
}
