<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\SimpleCta;

use App\Http\Components\Link\Link;
use App\Http\Components\Link\LinkFactory;
use App\Http\PageBuilder\BlockResponse\BlockResponseBuilderContract;
use App\Shared\FieldHandlers\ColourSwatches\ColorOptionFromElementField;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use App\Shared\FieldHandlers\LinkField\LinkFieldHandler;
use App\Shared\FieldHandlers\SuperTable\SuperTableFieldHandler;
use craft\elements\MatrixBlock;
use craft\errors\InvalidFieldException;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Markup;
use verbb\supertable\elements\SuperTableBlockElement;

use function array_map;

class SimpleCta implements BlockResponseBuilderContract
{
    public function __construct(
        private TwigEnvironment $twig,
        private LinkFactory $linkFactory,
        private GenericHandler $genericHandler,
        private LinkFieldHandler $linkFieldHandler,
        private SuperTableFieldHandler $superTableFieldHandler,
        private ColorOptionFromElementField $colorOptionFromElementField,
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
        $backgroundColor = $this->colorOptionFromElementField->getStringValue(
            element: $matrixBlock,
            fieldName: 'backgroundColor',
            option: 'tailwindColor',
        );

        $ctas = $this->superTableFieldHandler->getAll(
            element: $matrixBlock,
            field: 'ctas',
        );

        return $this->twig->render(
            '@app/Http/PageBuilder/BlockResponse/SimpleCta/SimpleCta.twig',
            [
                'contentModel' => new SimpleCtaContentModel(
                    tailwindBackgroundColor: $backgroundColor,
                    preHeadline: $this->genericHandler->getString(
                        element: $matrixBlock,
                        field: 'preHeadline',
                    ),
                    headline: $this->genericHandler->getString(
                        element: $matrixBlock,
                        field: 'headline',
                    ),
                    content: new Markup(
                        $this->genericHandler->getString(
                            element: $matrixBlock,
                            field: 'contentField',
                        ),
                        'UTF-8',
                    ),
                    ctas: array_map(
                        function (
                            SuperTableBlockElement $block
                        ): Link {
                            return $this->linkFactory->fromLinkFieldModel(
                                linkFieldModel: $this->linkFieldHandler
                                    ->getModel(
                                        element: $block,
                                        field: 'urlField',
                                    )
                            );
                        },
                        $ctas,
                    ),
                ),
            ],
        );
    }
}
