<?php

declare(strict_types=1);

namespace App\Http\Response\Media\MessagesFeed;

use App\Shared\Testing\MockResponseFactoryForTesting;
use App\Shared\Testing\MockRouteCollectorProxyForTesting;
use App\Shared\Testing\TestCase;
use craft\errors\InvalidFieldException;
use yii\base\Exception;
use yii\base\InvalidConfigException;

class MessagesFeedActionTest extends TestCase
{
    use MockResponseFactoryForTesting;
    use MockRouteCollectorProxyForTesting;
    use MockMessagesRssFeedFactoryForTesting;

    private MessagesFeedAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new MessagesFeedAction(
            responseFactory: $this->mockResponseFactory(),
            messagesRssFeedFactory: $this->mockMessagesRssFactory(),
        );
    }

    public function testAddRoute(): void
    {
        MessagesFeedAction::addRoute(
            routeCollector: $this->mockRouteCollector()
        );

        self::assertSame(
            [
                [
                    'object' => 'RouteCollectorProxyInterface',
                    'method' => 'get',
                    'args' => [
                        '/media/messages/feed',
                        MessagesFeedAction::class,
                    ],
                ],
            ],
            $this->calls,
        );
    }

    /**
     * @throws InvalidFieldException
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function testInvoke(): void
    {
        self::assertSame(
            $this->response,
            ($this->action)(),
        );

        self::assertSame(
            [
                [
                    'object' => 'MessagesRssFeedFactory',
                    'method' => 'make',
                    'args' => [],
                ],
                [
                    'object' => 'ResponseFactoryInterface',
                    'method' => 'createResponse',
                    'args' => [],
                ],
                [
                    'object' => 'ResponseInterface',
                    'method' => 'withHeader',
                    'args' => [
                        'EnableStaticCache',
                        'true',
                    ],
                ],
                [
                    'object' => 'ResponseInterface',
                    'method' => 'withHeader',
                    'args' => [
                        'Content-Type',
                        'text/xml',
                    ],
                ],
                [
                    'object' => 'StreamInterface',
                    'method' => 'write',
                    'args' => ['testDomXml'],
                ],
            ],
            $this->calls
        );
    }
}
