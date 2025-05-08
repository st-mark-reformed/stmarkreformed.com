<?php

declare(strict_types=1);

namespace App\Http\Pagination;

use App\Shared\Testing\MockTwigForTesting;
use App\Shared\Testing\TestCase;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class RenderPaginationTest extends TestCase
{
    use MockTwigForTesting;

    private RenderPagination $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new RenderPagination(twig: $this->mockTwig());
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function testRender(): void
    {
        $pagination = new Pagination();

        self::assertSame(
            'TwigRender',
            (string) $this->service->render(pagination: $pagination)
        );

        self::assertSame(
            [
                [
                    'object' => 'TwigEnvironment',
                    'method' => 'render',
                    'args' => [
                        '@app/Http/Pagination/Pagination.twig',
                        ['pagination' => $pagination],
                    ],
                ],
            ],
            $this->calls,
        );
    }
}
