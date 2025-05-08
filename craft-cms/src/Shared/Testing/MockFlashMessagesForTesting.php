<?php

declare(strict_types=1);

namespace App\Shared\Testing;

use PHPUnit\Framework\MockObject\MockObject;
use Slim\Flash\Messages as FlashMessages;

use function assert;

trait MockFlashMessagesForTesting
{
    /**
     * @return FlashMessages&MockObject
     */
    public function mockFlashMessages(): FlashMessages|MockObject
    {
        assert($this instanceof TestCase);

        $mock = $this->createMock(FlashMessages::class);

        $mock->method($this::anything())->willReturnCallback(
            function (): void {
                assert($this instanceof TestCase);

                $this->genericCall(object: 'FlashMessages');
            }
        );

        return $mock;
    }
}
