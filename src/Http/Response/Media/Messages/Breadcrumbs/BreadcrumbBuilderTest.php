<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages\Breadcrumbs;

use App\Http\Components\Link\Link;
use App\Http\Response\Media\Messages\Params;
use App\Shared\Testing\MockTwigForTesting;
use App\Shared\Testing\TestCase;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

use function array_map;
use function assert;
use function is_array;

// phpcs:disable Generic.Files.LineLength.TooLong

class BreadcrumbBuilderTest extends TestCase
{
    use MockTwigForTesting;

    private BreadcrumbBuilder $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new BreadcrumbBuilder(
            twig: $this->mockTwig(),
        );
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function testNoSearchFirstPage(): void
    {
        self::assertSame(
            '',
            (string) $this->service->fromParams(new Params()),
        );

        self::assertSame([], $this->calls);
    }

    public function testSearchOnly(): void
    {
        self::assertSame(
            'TwigRender',
            (string) $this->service->fromParams(new Params(
                by: ['by-1', 'by-2'],
                series: ['series-1', 'series-2'],
                scriptureReference: 'test-scripture',
                title: 'test-title',
                dateRangeStart: 'date-start',
                dateRangeEnd: 'date-end',
            )),
        );

        self::assertCount(1, $this->calls);

        self::assertSame(
            'TwigEnvironment',
            $this->calls[0]['object'],
        );

        self::assertSame(
            'render',
            $this->calls[0]['method'],
        );

        $args = $this->calls[0]['args'];

        assert(is_array($args));

        self::assertCount(2, $args);

        self::assertSame(
            'Http/_Infrastructure/Breadcrumbs.twig',
            $args[0],
        );

        $context = $args[1];

        assert(is_array($context));

        self::assertCount(1, $context);

        /** @var Link[] $breadcrumbs */
        $breadcrumbs = $context['breadcrumbs'];

        self::assertSame(
            [
                [
                    'content' => 'Home',
                    'href' => '/',
                ],
                [
                    'content' => 'All Messages',
                    'href' => '/media/messages',
                ],
                [
                    'content' => 'Search',
                    'href' => '/media/messages?by%5B0%5D=by-1&by%5B1%5D=by-2&series%5B0%5D=series-1&series%5B1%5D=series-2&scriptureReference=test-scripture&title=test-title&dateRangeStart=date-start&dateRangeEnd=date-end',
                ],
            ],
            array_map(
                static fn (Link $link) => [
                    'content' => $link->content(),
                    'href' => $link->href(),
                ],
                $breadcrumbs,
            ),
        );
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function testPageNumOnly(): void
    {
        self::assertSame(
            'TwigRender',
            (string) $this->service->fromParams(new Params(
                page: 4,
            )),
        );

        self::assertCount(1, $this->calls);

        self::assertSame(
            'TwigEnvironment',
            $this->calls[0]['object'],
        );

        self::assertSame(
            'render',
            $this->calls[0]['method'],
        );

        $args = $this->calls[0]['args'];

        assert(is_array($args));

        self::assertCount(2, $args);

        self::assertSame(
            'Http/_Infrastructure/Breadcrumbs.twig',
            $args[0],
        );

        $context = $args[1];

        assert(is_array($context));

        self::assertCount(1, $context);

        /** @var Link[] $breadcrumbs */
        $breadcrumbs = $context['breadcrumbs'];

        self::assertSame(
            [
                [
                    'content' => 'Home',
                    'href' => '/',
                ],
                [
                    'content' => 'All Messages',
                    'href' => '/media/messages',
                ],
                [
                    'content' => 'Page 4',
                    'href' => '/media/messages?page=4',
                ],
            ],
            array_map(
                static fn (Link $link) => [
                    'content' => $link->content(),
                    'href' => $link->href(),
                ],
                $breadcrumbs,
            ),
        );
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function testSearchAndPageNum(): void
    {
        self::assertSame(
            'TwigRender',
            (string) $this->service->fromParams(new Params(
                page: 2,
                by: ['by'],
                series: ['series'],
                scriptureReference: 'scripture',
                title: 'title',
                dateRangeStart: 'start',
                dateRangeEnd: 'end',
            )),
        );

        self::assertCount(1, $this->calls);

        self::assertSame(
            'TwigEnvironment',
            $this->calls[0]['object'],
        );

        self::assertSame(
            'render',
            $this->calls[0]['method'],
        );

        $args = $this->calls[0]['args'];

        assert(is_array($args));

        self::assertCount(2, $args);

        self::assertSame(
            'Http/_Infrastructure/Breadcrumbs.twig',
            $args[0],
        );

        $context = $args[1];

        assert(is_array($context));

        self::assertCount(1, $context);

        /** @var Link[] $breadcrumbs */
        $breadcrumbs = $context['breadcrumbs'];

        self::assertSame(
            [
                [
                    'content' => 'Home',
                    'href' => '/',
                ],
                [
                    'content' => 'All Messages',
                    'href' => '/media/messages',
                ],
                [
                    'content' => 'Search',
                    'href' => '/media/messages?by%5B0%5D=by&series%5B0%5D=series&scriptureReference=scripture&title=title&dateRangeStart=start&dateRangeEnd=end',
                ],
                [
                    'content' => 'Page 2',
                    'href' => '/media/messages?page=2&by%5B0%5D=by&series%5B0%5D=series&scriptureReference=scripture&title=title&dateRangeStart=start&dateRangeEnd=end',
                ],
            ],
            array_map(
                static fn (Link $link) => [
                    'content' => $link->content(),
                    'href' => $link->href(),
                ],
                $breadcrumbs,
            ),
        );
    }
}
