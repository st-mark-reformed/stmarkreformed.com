<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\Shared\AudioPlayer;

use App\Shared\Testing\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

use function assert;

trait MockAudioPlayerContentModelFactoryForTesting
{
    private AudioPlayerContentModel $audioPlayerContentModel;

    /**
     * @return AudioPlayerContentModelFactory&MockObject
     */
    protected function mockAudioPlayerContentModelFactory(): mixed
    {
        assert($this instanceof TestCase);

        $this->audioPlayerContentModel = $this->createMock(
            AudioPlayerContentModel::class,
        );

        $mock = $this->createMock(
            AudioPlayerContentModelFactory::class,
        );

        $mock->method($this::anything())->willReturnCallback(
            function (): AudioPlayerContentModel {
                assert($this instanceof TestCase);

                return $this->genericCall(
                    object: 'AudioPlayerContentModelFactory',
                    return: $this->audioPlayerContentModel,
                );
            }
        );

        return $mock;
    }
}
