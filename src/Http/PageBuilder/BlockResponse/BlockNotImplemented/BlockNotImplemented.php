<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\BlockNotImplemented;

use App\Http\PageBuilder\BlockResponse\BlockResponseBuilderContract;
use craft\elements\MatrixBlock;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class BlockNotImplemented implements BlockResponseBuilderContract
{
    public function __construct(private TwigEnvironment $twig)
    {
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     *
     * @phpstan-ignore-next-line
     */
    public function buildResponse(MatrixBlock $matrixBlock): string
    {
        return $this->twig->render(
            '@app/Http/PageBuilder/BlockResponse/BlockNotImplemented/BlockNotImplemented.twig'
        );
    }
}
