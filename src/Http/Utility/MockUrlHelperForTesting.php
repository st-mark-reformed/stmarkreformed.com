<?php

declare(strict_types=1);

namespace App\Http\Utility;

use App\Shared\Testing\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

use function assert;

trait MockUrlHelperForTesting
{
    /**
     * @return UrlHelper&MockObject
     */
    public function mockUrlHelper(): mixed
    {
        assert($this instanceof TestCase);

        $mock = $this->createMock(UrlHelper::class);

        $mock->method($this::anything())->willReturnCallback(
            function (): string {
                assert($this instanceof TestCase);

                return $this->genericCall(
                    object: 'UrlHelper',
                    return: 'TestSiteUrlReturn',
                );
            }
        );

        return $mock;
    }
}
