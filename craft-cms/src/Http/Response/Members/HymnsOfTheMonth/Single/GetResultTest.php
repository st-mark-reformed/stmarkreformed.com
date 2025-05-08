<?php

declare(strict_types=1);

namespace App\Http\Response\Members\HymnsOfTheMonth\Single;

use App\Shared\FieldHandlers\Assets\AssetsFieldHandler;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use App\Shared\Testing\TestCase;
use craft\elements\Asset;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use DateTime;
use DateTimeInterface;

class GetResultTest extends TestCase
{
    private Entry $entry;

    private DateTime $date;

    private GetResult $getResult;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entry = $this->mockEntry();

        /** @phpstan-ignore-next-line */
        $this->date = DateTime::createFromFormat(
            DateTimeInterface::ATOM,
            '1982-01-27T10:00:10+00:00'
        );

        $this->getResult = new GetResult(
            genericHandler: $this->mockGenericHandler(),
            assetsFieldHandler: $this->mockAssetsFieldHandler(),
        );
    }

    /**
     * @phpstan-ignore-next-line
     */
    private function mockEntry(): Entry
    {
        $mock = $this->createMock(Entry::class);

        $mock->method('getUrl')->willReturn('/foo/bar/url');

        return $mock;
    }

    private function mockGenericHandler(): GenericHandler
    {
        $mock = $this->createMock(GenericHandler::class);

        $mock->method('getDate')->willReturnCallback(
            function (): DateTime {
                return $this->genericCall(
                    object: 'GenericHandler',
                    return: $this->date,
                );
            }
        );

        $mock->method('getString')->willReturnCallback(
            function (): string {
                return $this->genericCall(
                    object: 'GenericHandler',
                    return: 'GetStringGenericHandlerReturn',
                );
            }
        );

        return $mock;
    }

    private function mockAssetsFieldHandler(): AssetsFieldHandler
    {
        $mock = $this->createMock(AssetsFieldHandler::class);

        $mock->method('getOneOrNull')->willReturnCallback(
            function (): Asset {
                $asset = $this->createMock(Asset::class);

                $asset->method('getPath')->willReturn(
                    'foo/path/1',
                );

                return $asset;
            }
        );

        $mock->method('getAll')->willReturnCallback(
            function (): array {
                $asset2 = $this->createMock(Asset::class);

                $asset2->title = 'Asset 2 Title';

                $asset2->method('getPath')->willReturn(
                    'foo/path/2',
                );

                $asset3 = $this->createMock(Asset::class);

                $asset3->title = 'Asset 3 Title';

                $asset3->method('getPath')->willReturn(
                    'foo/path/3',
                );

                return $this->genericCall(
                    object: 'AssetsFieldHandler',
                    return: [$asset2, $asset3],
                );
            }
        );

        return $mock;
    }

    /**
     * @throws InvalidFieldException
     */
    public function testFromEntry(): void
    {
        $result = $this->getResult->fromEntry(entry: $this->entry);

        self::assertSame('January', $result->month());

        self::assertSame('1982', $result->year());

        self::assertSame(
            'GetStringGenericHandlerReturn',
            $result->title(),
        );

        self::assertSame(
            '/foo/bar/url/file/foo/path/1',
            $result->musicSheetDownloadUrl(),
        );

        self::assertSame(
            [
                [
                    'title' => 'Asset 2 Title',
                    'url' => '/foo/bar/url/file/foo/path/2',
                ],
                [
                    'title' => 'Asset 3 Title',
                    'url' => '/foo/bar/url/file/foo/path/3',
                ],
            ],
            $result->mapTracks(static fn (Track $track) => [
                'title' => $track->title(),
                'url' => $track->url(),
            ])
        );

        self::assertSame(
            [
                [
                    'object' => 'GenericHandler',
                    'method' => 'getDate',
                    'args' => [
                        $this->entry,
                        'date',
                    ],
                ],
                [
                    'object' => 'AssetsFieldHandler',
                    'method' => 'getAll',
                    'args' => [
                        $this->entry,
                        'hymnOfTheMonthPracticeTracks',
                    ],
                ],
                [
                    'object' => 'GenericHandler',
                    'method' => 'getString',
                    'args' => [
                        $this->entry,
                        'hymnPsalmName',
                    ],
                ],
            ],
            $this->calls,
        );
    }
}
