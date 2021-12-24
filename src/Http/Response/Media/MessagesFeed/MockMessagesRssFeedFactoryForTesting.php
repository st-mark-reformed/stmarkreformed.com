<?php

declare(strict_types=1);

namespace App\Http\Response\Media\MessagesFeed;

use App\Shared\Testing\TestCase;
use DOMDocument;
use PHPUnit\Framework\MockObject\MockObject;

use function assert;

trait MockMessagesRssFeedFactoryForTesting
{
    /**
     * @return MessagesRssFeedFactory&MockObject
     */
    public function mockMessagesRssFactory(): mixed
    {
        assert($this instanceof TestCase);

        $messagesRssDomDocument = $this->createMock(
            DOMDocument::class,
        );

        $messagesRssDomDocument->method('saveXML')->willReturn(
            'testDomXml',
        );

        $mock = $this->createMock(
            MessagesRssFeedFactory::class,
        );

        $mock->method($this::anything())->willReturnCallback(
            function () use ($messagesRssDomDocument): DOMDocument {
                assert($this instanceof TestCase);

                return $this->genericCall(
                    object: 'MessagesRssFeedFactory',
                    return: $messagesRssDomDocument,
                );
            }
        );

        return $mock;
    }
}
