<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse;

use App\Http\PageBuilder\BlockResponse\BlockNotImplemented\BlockNotImplemented;
use craft\elements\MatrixBlock;
use Psr\Container\ContainerInterface;
use yii\base\InvalidConfigException;

use function assert;

class BlockResponseBuilderFactory
{
    public function __construct(private ContainerInterface $container)
    {
    }

    /**
     * @throws InvalidConfigException
     *
     * @phpstan-ignore-next-line
     */
    public function make(MatrixBlock $matrixBlock): BlockResponseBuilderContract
    {
        $typeMap = BlockResponseBuilderContract::BLOCK_TYPE_MAP;

        $handle = $matrixBlock->getType()->handle;

        /** @psalm-suppress MixedAssignment */
        $responseBuilderClass = $typeMap[$handle] ??
            BlockNotImplemented::class;

        /** @psalm-suppress MixedArgument */
        $class = $this->container->get($responseBuilderClass);

        assert($class instanceof BlockResponseBuilderContract);

        return $class;
    }
}
