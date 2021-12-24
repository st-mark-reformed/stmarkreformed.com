<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages;

use App\Http\Pagination\Pagination;
use App\Http\Response\Media\Messages\Response\ResponderContract;
use App\Http\Response\Media\Messages\Response\ResponderFactory;
use App\Messages\MockMessagesApiForTesting;
use App\Messages\RetrieveMessages\MessageRetrievalParams;
use App\Messages\RetrieveMessages\MessagesResult;
use App\Shared\Testing\MockRouteCollectorProxyForTesting;
use App\Shared\Testing\TestCase;
use craft\elements\Entry;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function assert;
use function is_array;

// phpcs:disable Generic.Files.LineLength.TooLong

class PaginatedMessagesListActionTest extends TestCase
{
    use MockMessagesApiForTesting;
    use MockRouteCollectorProxyForTesting;

    private PaginatedMessagesListAction $action;

    /** @var MockObject&ServerRequestInterface */
    private mixed $request;

    /** @var MockObject&ResponseInterface */
    private mixed $response;

    protected function setUp(): void
    {
        parent::setUp();

        $this->request = $this->mockRequest();

        $this->response = $this->createMock(
            ResponseInterface::class,
        );

        $this->action = new PaginatedMessagesListAction(
            messagesApi: $this->mockMessagesApi(),
            responderFactory: $this->mockResponderFactory(),
        );
    }

    /**
     * @return ResponderFactory&MockObject
     */
    private function mockResponderFactory(): mixed
    {
        $mock = $this->createMock(ResponderFactory::class);

        $mock->method($this::anything())->willReturnCallback(
            function (): ResponderContract {
                return $this->genericCall(
                    object: 'ResponderFactory',
                    return: $this->mockResponder(),
                );
            }
        );

        return $mock;
    }

    /**
     * @return ResponderContract&MockObject
     */
    private function mockResponder(): mixed
    {
        $mock = $this->createMock(ResponderContract::class);

        $mock->method('respond')->willReturnCallback(
            function (): ResponseInterface {
                return $this->genericCall(
                    object: 'ResponderContract',
                    return: $this->response,
                );
            }
        );

        return $mock;
    }

    /**
     * @return MockObject&ServerRequestInterface
     */
    private function mockRequest(): mixed
    {
        $mock = $this->createMock(
            ServerRequestInterface::class,
        );

        $mock->method('getQueryParams')->willReturn([
            'foo' => 'bar',
            'baz' => 'foo',
            'by' => ['by1', 'by2'],
            'series' => ['series1', 'series2'],
            'page' => 456,
            'scripture_reference' => 'foo-scripture-reference',
            'title' => 'foo-title',
            'date_range_start' => '1982-01-27',
            'date_range_end' => '1992-01-27',
        ]);

        return $mock;
    }

    public function testAddRoute(): void
    {
        $this->action::addRoute(routeCollector: $this->mockRouteCollector());

        self::assertSame(
            [
                [
                    'object' => 'RouteCollectorProxyInterface',
                    'method' => 'get',
                    'args' => [
                        '/media/messages',
                        PaginatedMessagesListAction::class,
                    ],
                ],
            ],
            $this->calls,
        );
    }

    public function testInvoke(): void
    {
        self::assertSame(
            $this->response,
            ($this->action)(request: $this->request),
        );

        self::assertCount(3, $this->calls);

        $call1 = $this->calls[0];

        assert(is_array($call1));

        self::assertSame(
            'MessagesApi',
            $call1['object'],
        );

        self::assertSame(
            'retrieveMessages',
            $call1['method'],
        );

        $call1Args = $call1['args'];

        assert(is_array($call1Args));

        self::assertCount(1, $call1Args);

        $params = $call1Args[0];

        assert($params instanceof MessageRetrievalParams);

        self::assertSame(25, $params->limit());

        self::assertSame(11375, $params->offset());

        self::assertSame(
            ['by1', 'by2'],
            $params->by(),
        );

        self::assertSame(
            ['series1', 'series2'],
            $params->series(),
        );

        self::assertSame(
            'foo-scripture-reference',
            $params->scriptureReference(),
        );

        self::assertSame(
            'foo-title',
            $params->title(),
        );

        self::assertSame(
            '1982-01-27',
            /** @phpstan-ignore-next-line */
            $params->dateRangeStart()->format('Y-m-d'),
        );

        self::assertSame(
            '1992-01-27',
            /** @phpstan-ignore-next-line */
            $params->dateRangeEnd()->format('Y-m-d'),
        );

        $call2 = $this->calls[1];

        assert(is_array($call2));

        self::assertSame(
            'ResponderFactory',
            $call2['object'],
        );

        self::assertSame(
            'make',
            $call2['method'],
        );

        $call2Args = $call2['args'];

        assert(is_array($call2Args));

        self::assertCount(1, $call2Args);

        $messagesResult = $call2Args[0];

        assert($messagesResult instanceof MessagesResult);

        self::assertSame(
            123,
            $messagesResult->absoluteTotal(),
        );

        self::assertSame(
            [
                ['slug' => 'message-1'],
                ['slug' => 'message-2'],
            ],
            $messagesResult->map(
                static fn (Entry $entry) => [
                    'slug' => $entry->slug,
                ],
            ),
        );

        $call3 = $this->calls[2];

        assert(is_array($call3));

        self::assertSame(
            'ResponderContract',
            $call3['object'],
        );

        self::assertSame(
            'respond',
            $call3['method'],
        );

        $call3Args = $call3['args'];

        assert(is_array($call3Args));

        self::assertCount(3, $call3Args);

        $messagesParams = $call3Args[0];

        assert($messagesParams instanceof Params);

        self::assertSame(
            456,
            $messagesParams->page(),
        );

        self::assertSame(
            ['by1', 'by2'],
            $messagesParams->by(),
        );

        self::assertSame(
            ['series1', 'series2'],
            $messagesParams->series(),
        );

        self::assertSame(
            'foo-scripture-reference',
            $messagesParams->scriptureReference(),
        );

        self::assertSame(
            'foo-title',
            $messagesParams->title(),
        );

        self::assertSame(
            '1982-01-27',
            $messagesParams->dateRangeStart(),
        );

        self::assertSame(
            '1992-01-27',
            $messagesParams->dateRangeEnd(),
        );

        self::assertSame(25, $messagesParams->perPage());

        self::assertSame(
            $messagesResult,
            $call3Args[1],
        );

        $pagination = $call3Args[2];

        assert($pagination instanceof Pagination);

        self::assertSame(
            '/media/messages',
            $pagination->base(),
        );

        self::assertSame(
            25,
            $pagination->perPage(),
        );

        self::assertSame(
            456,
            $pagination->currentPage(),
        );

        self::assertTrue($pagination->queryStringBased());

        self::assertSame(
            5,
            $pagination->totalPages(),
        );

        self::assertSame(
            '?foo=bar&baz=foo&by%5B0%5D=by1&by%5B1%5D=by2&series%5B0%5D=series1&series%5B1%5D=series2&page=456&scripture_reference=foo-scripture-reference&title=foo-title&date_range_start=1982-01-27&date_range_end=1992-01-27',
            $pagination->queryString(),
        );
    }
}
