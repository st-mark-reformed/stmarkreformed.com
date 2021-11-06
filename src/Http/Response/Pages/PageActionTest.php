<?php

declare(strict_types=1);

namespace App\Http\Response\Pages;

use App\Http\Response\Pages\RenderPage\RenderPageContract;
use App\Http\Response\Pages\RenderPage\RenderPageFactory;
use App\Http\Shared\RouteParamsHandler;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use BuzzingPixel\SlimBridge\ElementSetRoute\RouteParams;
use craft\base\Element;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use yii\base\InvalidConfigException;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress MixedAssignment
 * @psalm-suppress MixedArgument
 */
class PageActionTest extends TestCase
{
    private PageAction $action;

    /**
     * @var Entry&MockObject
     * @phpstan-ignore-next-line
     */
    private mixed $entry;

    /** @var RouteParams&MockObject */
    private mixed $routeParams;

    /** @var MockObject&ResponseInterface */
    private mixed $response;

    /** @var mixed[] */
    private array $renderPageFactoryCalls = [];

    /** @var mixed[] */
    private array $routeParamsHandlerCalls = [];

    /** @var mixed[] */
    private array $bodyCalls = [];

    /** @var mixed[] */
    private array $responseCalls = [];

    /** @var mixed[] */
    private array $responseFactoryCalls = [];

    private bool $genericHandlerBooleanReturn = false;

    /** @var mixed[] */
    private array $genericHandlerCalls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->genericHandlerBooleanReturn = false;

        $this->renderPageFactoryCalls = [];

        $this->routeParamsHandlerCalls = [];

        $this->bodyCalls = [];

        $this->responseCalls = [];

        $this->responseFactoryCalls = [];

        $this->genericHandlerCalls = [];

        $this->entry = $this->createMock(Entry::class);

        $this->routeParams = $this->createMock(
            RouteParams::class,
        );

        $renderPageContract = $this->createMock(
            RenderPageContract::class
        );

        $renderPageContract->method('render')->willReturn(
            'renderPageContractString',
        );

        $renderPageFactory = $this->createMock(
            RenderPageFactory::class,
        );

        $renderPageFactory->method('make')->willReturnCallback(
            function (Entry $entry) use (
                $renderPageContract,
            ): RenderPageContract {
                $this->renderPageFactoryCalls[] = [
                    'method' => 'make',
                    'entry' => $entry,
                ];

                return $renderPageContract;
            }
        );

        $routeParamsHandler = $this->createMock(
            RouteParamsHandler::class,
        );

        $routeParamsHandler->method('getEntry')->willReturnCallback(
            function (RouteParams $routeParams): Entry {
                $this->routeParamsHandlerCalls[] = [
                    'method' => 'getEntry',
                    'routeParams' => $routeParams,
                ];

                return $this->entry;
            }
        );

        $body = $this->createMock(StreamInterface::class);

        $body->method('write')->willReturnCallback(
            function (string $string): int {
                $this->bodyCalls[] = [
                    'method' => 'write',
                    'string' => $string,
                ];

                return 1234;
            }
        );

        $this->response = $this->createMock(
            ResponseInterface::class,
        );

        $this->response->method('withHeader')->willReturnCallback(
            function (
                string $name,
                string $value
            ): ResponseInterface {
                $this->responseCalls[] = [
                    'method' => 'withHeader',
                    'name' => $name,
                    'value' => $value,
                ];

                return $this->response;
            }
        );

        $this->response->method('getBody')->willReturn(
            $body
        );

        $responseFactory = $this->createMock(
            ResponseFactoryInterface::class,
        );

        $responseFactory->method('createResponse')
            ->willReturnCallback(
                function (
                    int $code = 200,
                    string $reasonPhrase = '',
                ): ResponseInterface {
                    $this->responseFactoryCalls[] = [
                        'method' => 'createResponse',
                        'code' => $code,
                        'reasonPhrase' => $reasonPhrase,
                    ];

                    return $this->response;
                }
            );

        $genericHandler = $this->createMock(
            GenericHandler::class,
        );

        $genericHandler->method('getBoolean')->willReturnCallback(
            function (Element $element, string $field): bool {
                $this->genericHandlerCalls[] = [
                    'method' => 'getBoolean',
                    'element' => $element,
                    'field' => $field,
                ];

                return $this->genericHandlerBooleanReturn;
            }
        );

        $this->action = new PageAction(
            routeParams: $this->routeParams,
            genericHandler: $genericHandler,
            responseFactory: $responseFactory,
            renderPageFactory: $renderPageFactory,
            routeParamsHandler: $routeParamsHandler,
        );
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function testWhenStaticCacheIsNotEnabled(): void
    {
        $this->genericHandlerBooleanReturn = false;

        self::assertSame(
            $this->response,
            ($this->action)(),
        );

        self::assertSame(
            [
                [
                    'method' => 'make',
                    'entry' => $this->entry,
                ],
            ],
            $this->renderPageFactoryCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'getEntry',
                    'routeParams' => $this->routeParams,
                ],
            ],
            $this->routeParamsHandlerCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'write',
                    'string' => 'renderPageContractString',
                ],
            ],
            $this->bodyCalls,
        );

        self::assertSame(
            [],
            $this->responseCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'createResponse',
                    'code' => 200,
                    'reasonPhrase' => '',
                ],
            ],
            $this->responseFactoryCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'getBoolean',
                    'element' => $this->entry,
                    'field' => 'enableStaticCache',
                ],
            ],
            $this->genericHandlerCalls,
        );
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function testWhenStaticCacheEnabled(): void
    {
        $this->genericHandlerBooleanReturn = true;

        self::assertSame(
            $this->response,
            ($this->action)(),
        );

        self::assertSame(
            [
                [
                    'method' => 'make',
                    'entry' => $this->entry,
                ],
            ],
            $this->renderPageFactoryCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'getEntry',
                    'routeParams' => $this->routeParams,
                ],
            ],
            $this->routeParamsHandlerCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'write',
                    'string' => 'renderPageContractString',
                ],
            ],
            $this->bodyCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'withHeader',
                    'name' => 'EnableStaticCache',
                    'value' => 'true',
                ],
            ],
            $this->responseCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'createResponse',
                    'code' => 200,
                    'reasonPhrase' => '',
                ],
            ],
            $this->responseFactoryCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'getBoolean',
                    'element' => $this->entry,
                    'field' => 'enableStaticCache',
                ],
            ],
            $this->genericHandlerCalls,
        );
    }
}
