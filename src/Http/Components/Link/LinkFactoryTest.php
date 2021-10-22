<?php

declare(strict_types=1);

namespace App\Http\Components\Link;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use typedlinkfield\models\Link as LinkFieldModel;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 */
class LinkFactoryTest extends TestCase
{
    /**
     * @return MockObject&LinkFieldModel
     *
     * @phpstan-ignore-next-line
     */
    private function createLinkFieldModelStub(
        ?bool $isEmptyValue = false,
        ?string $getUrlValue = null,
        ?string $getTextValue = null,
        ?string $getTargetValue = null,
    ): MockObject|LinkFieldModel {
        $linkFieldModel = $this->createMock(
            LinkFieldModel::class,
        );

        $linkFieldModel->method('isEmpty')->willReturn(
            $isEmptyValue
        );

        $linkFieldModel->method('getUrl')->willReturn(
            $getUrlValue
        );

        $linkFieldModel->method('getText')->willReturn(
            $getTextValue
        );

        $linkFieldModel->method('getTarget')->willReturn(
            $getTargetValue
        );

        return $linkFieldModel;
    }

    public function testFromLinkFieldModelWhenLinkFieldModelIsEmpty(): void
    {
        $link = (new LinkFactory())->fromLinkFieldModel(
            linkFieldModel: $this->createLinkFieldModelStub(
                isEmptyValue: true,
            ),
        );

        self::assertTrue($link->isEmpty());

        self::assertSame('', $link->content());

        self::assertSame('', $link->href());

        self::assertFalse($link->newWindow());
    }

    public function testFromLinkFieldModelWhenNoHref(): void
    {
        $link = (new LinkFactory())->fromLinkFieldModel(
            linkFieldModel: $this->createLinkFieldModelStub(),
        );

        self::assertTrue($link->isEmpty());

        self::assertSame('', $link->content());

        self::assertSame('', $link->href());

        self::assertFalse($link->newWindow());
    }

    public function testFromLinkFieldModelWhenNoContent(): void
    {
        $link = (new LinkFactory())->fromLinkFieldModel(
            linkFieldModel: $this->createLinkFieldModelStub(
                getUrlValue: 'testUrlValue',
            ),
        );

        self::assertFalse($link->isEmpty());

        self::assertSame('testUrlValue', $link->content());

        self::assertSame('testUrlValue', $link->href());

        self::assertFalse($link->newWindow());
    }

    public function testFromLinkFieldModel(): void
    {
        $link = (new LinkFactory())->fromLinkFieldModel(
            linkFieldModel: $this->createLinkFieldModelStub(
                getUrlValue: 'testUrlValue',
                getTextValue: 'testTextValue',
                getTargetValue: '_blank',
            ),
        );

        self::assertFalse($link->isEmpty());

        self::assertSame('testTextValue', $link->content());

        self::assertSame('testUrlValue', $link->href());

        self::assertTrue($link->newWindow());
    }
}
