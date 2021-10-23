<?php

declare(strict_types=1);

namespace App\Shared\Files;

use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 */
class RemoteSplFileInfoTest extends TestCase
{
    public function test(): void
    {
        $fileInfo = new RemoteSplFileInfo(
            filename: 'testFileName',
            size: 123,
            content: 'testContent',
        );

        self::assertSame(
            'testFileName',
            $fileInfo->getBasename(),
        );

        self::assertSame(
            123,
            $fileInfo->getSize(),
        );

        self::assertSame(
            'testContent',
            $fileInfo->getContent(),
        );

        self::assertSame(
            0,
            $fileInfo->getPerms(),
        );

        self::assertSame(
            0,
            $fileInfo->getInode(),
        );

        self::assertSame(
            0,
            $fileInfo->getOwner(),
        );

        self::assertSame(
            0,
            $fileInfo->getGroup(),
        );

        self::assertSame(
            0,
            $fileInfo->getATime(),
        );

        self::assertSame(
            0,
            $fileInfo->getMTime(),
        );

        self::assertSame(
            0,
            $fileInfo->getCTime(),
        );
    }
}
