<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse;

use App\Http\PageBuilder\BlockResponse\BlockNotImplemented\BlockNotImplemented;
use App\Http\PageBuilder\BlockResponse\ImageContentCta\ImageContentCta;
use craft\elements\MatrixBlock;
use craft\models\MatrixBlockType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use yii\base\InvalidConfigException;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 */
class BlockResponseBuilderFactoryTest extends TestCase
{
    /**
     * @return MockObject&MatrixBlock
     *
     * @phpstan-ignore-next-line
     */
    private function createMatrixBlockStub(
        string $handleReturn,
    ): MockObject|MatrixBlock {
        $typeStub = $this->createMock(
            MatrixBlockType::class,
        );

        $typeStub->handle = $handleReturn;

        $matrixBlockStub = $this->createMock(
            MatrixBlock::class,
        );

        $matrixBlockStub->method('getType')->willReturn(
            $typeStub,
        );

        return $matrixBlockStub;
    }

    /**
     * @throws InvalidConfigException
     */
    public function testMakeWhenNoBlockTypeMatch(): void
    {
        $instanceStub = $this->createMock(
            BlockResponseBuilderContract::class,
        );

        $containerSpy = $this->createMock(
            ContainerInterface::class,
        );

        $containerSpy->expects(self::once())
            ->method('get')
            ->with(self::equalTo(
                BlockNotImplemented::class,
            ))
            ->willReturn($instanceStub);

        $factory = new BlockResponseBuilderFactory(
            container: $containerSpy,
        );

        self::assertSame(
            $instanceStub,
            $factory->make(
                matrixBlock: $this->createMatrixBlockStub(handleReturn: 'foo'),
            ),
        );
    }

    /**
     * @throws InvalidConfigException
     */
    public function testMakeWhenTypeMatch(): void
    {
        $instanceStub = $this->createMock(
            BlockResponseBuilderContract::class,
        );

        $containerSpy = $this->createMock(
            ContainerInterface::class,
        );

        $containerSpy->expects(self::once())
            ->method('get')
            ->with(self::equalTo(
                ImageContentCta::class,
            ))
            ->willReturn($instanceStub);

        $factory = new BlockResponseBuilderFactory(
            container: $containerSpy,
        );

        self::assertSame(
            $instanceStub,
            $factory->make(
                matrixBlock: $this->createMatrixBlockStub(
                    handleReturn: 'imageContentCta'
                ),
            ),
        );
    }
}
