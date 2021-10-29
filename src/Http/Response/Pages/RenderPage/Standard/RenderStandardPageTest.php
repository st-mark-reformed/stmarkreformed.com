<?php

declare(strict_types=1);

namespace App\Http\Response\Pages\RenderPage\Standard;

use App\Http\Components\Hero\Hero;
use App\Http\Entities\Meta;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress MixedAssignment
 * @psalm-suppress MixedArgument
 */
class RenderStandardPageTest extends TestCase
{
    private RenderStandardPage $renderPage;

    /** @var Meta&MockObject */
    private mixed $meta;

    /** @var Hero&MockObject */
    private mixed $hero;

    /** @var mixed[] */
    private array $twigCalls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->twigCalls = [];

        $this->meta = $this->createMock(Meta::class);

        $this->hero = $this->createMock(Hero::class);

        $twig = $this->createMock(TwigEnvironment::class);

        $twig->method('render')->willReturnCallback(
            function (string $name, array $context): string {
                $this->twigCalls[] = [
                    'method' => 'render',
                    'name' => $name,
                    'context' => $context,
                ];

                return 'testTwigRenderedString';
            }
        );

        $this->renderPage = new RenderStandardPage(
            meta: $this->meta,
            hero: $this->hero,
            contentString: 'testContentString',
            twig: $twig,
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
            'testTwigRenderedString',
            $this->renderPage->render(),
        );

        self::assertCount(1, $this->twigCalls);

        self::assertSame(
            'render',
            $this->twigCalls[0]['method'],
        );

        self::assertSame(
            '@app/Http/Response/Pages/RenderPage/Standard/Page.twig',
            $this->twigCalls[0]['name'],
        );

        $context = $this->twigCalls['0']['context'];

        self::assertCount(3, $context);

        self::assertSame(
            $this->meta,
            $context['meta'],
        );

        self::assertSame(
            $this->hero,
            $context['hero'],
        );

        self::assertSame(
            'testContentString',
            (string) $context['content'],
        );
    }
}
