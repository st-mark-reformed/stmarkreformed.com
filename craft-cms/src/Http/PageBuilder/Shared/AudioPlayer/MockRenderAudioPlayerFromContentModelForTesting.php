<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\Shared\AudioPlayer;

use App\Shared\Testing\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

use function assert;

trait MockRenderAudioPlayerFromContentModelForTesting
{
    /**
     * @return RenderAudioPlayerFromContentModel&MockObject
     */
    public function mockRenderAudioPlayerFromContentModel(): mixed
    {
        assert($this instanceof TestCase);

        $mock = $this->createMock(
            RenderAudioPlayerFromContentModel::class,
        );

        $mock->method($this::anything())->willReturnCallback(
            function (): string {
                assert($this instanceof TestCase);

                return 'RenderAudioPlayerFromContentModelReturnString';
            }
        );

        return $mock;
    }
}
