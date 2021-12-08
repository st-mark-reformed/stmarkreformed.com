<?php

declare(strict_types=1);

namespace App\Http\Shared;

use App\Shared\Testing\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;

use function assert;

class PageNumberFactoryTest extends TestCase
{
    private PageNumberFactory $factory;

    private ServerRequestInterface $request;

    private null|string $pageNum = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pageNum = null;

        $this->factory = new PageNumberFactory();

        $this->request = $this->createMock(
            ServerRequestInterface::class,
        );

        $this->request->method('getAttribute')->willReturnCallback(
            function (string $name): null|string {
                assert($name === 'pageNum');

                return $this->pageNum;
            }
        );
    }

    public function testWhenPageNumIs1(): void
    {
        $exception = null;

        $this->pageNum = '1';

        try {
            $this->factory->fromRequest(request: $this->request);
        } catch (Throwable $e) {
            $exception = $e;
        }

        assert($exception instanceof HttpNotFoundException);

        self::assertSame(
            $this->request,
            $exception->getRequest(),
        );
    }

    /**
     * @throws HttpNotFoundException
     */
    public function testWhenPageNumIsNull(): void
    {
        self::assertSame(
            1,
            $this->factory->fromRequest(request: $this->request),
        );
    }

    /**
     * @throws HttpNotFoundException
     */
    public function testWhenPageNumIs18(): void
    {
        $this->pageNum = '18';

        self::assertSame(
            18,
            $this->factory->fromRequest(request: $this->request),
        );
    }
}
