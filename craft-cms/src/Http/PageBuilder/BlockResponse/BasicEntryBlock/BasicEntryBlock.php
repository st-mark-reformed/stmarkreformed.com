<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\BasicEntryBlock;

use App\Http\Components\Link\Link;
use App\Http\PageBuilder\BlockResponse\BlockResponseBuilderContract;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use craft\elements\MatrixBlock;
use craft\errors\InvalidFieldException;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

use function array_map;
use function assert;
use function is_array;

class BasicEntryBlock implements BlockResponseBuilderContract
{
    public function __construct(
        private TwigEnvironment $twig,
        private GenericHandler $genericHandler,
    ) {
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws InvalidFieldException
     *
     * @phpstan-ignore-next-line
     */
    public function buildResponse(MatrixBlock $matrixBlock): string
    {
        $headline = $this->genericHandler->getString(
            element: $matrixBlock,
            field: 'heading',
        );

        $subHeadline = $this->genericHandler->getString(
            element: $matrixBlock,
            field: 'subheading',
        );

        $content = $this->genericHandler->getTwigMarkup(
            element: $matrixBlock,
            field: 'body',
        );

        $ctas = $matrixBlock->getFieldValue('callToAction') ?? [];

        assert(is_array($ctas));

        $ctas = array_map(
            static fn (array $cta) => new Link(
                isEmpty: false,
                content: $cta['text'],
                href: $cta['link'],
                newWindow: false,
            ),
            $ctas,
        );

        return $this->twig->render(
            '@app/Http/PageBuilder/BlockResponse/BasicEntryBlock/BasicEntryBlock.twig',
            [
                'contentModel' => new BasicEntryBlockContentModel(
                    headline: $headline,
                    subHeadline: $subHeadline,
                    content: $content,
                    ctas: $ctas,
                ),
            ],
        );
    }
}
