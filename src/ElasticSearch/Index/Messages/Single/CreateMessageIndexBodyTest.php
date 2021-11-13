<?php

declare(strict_types=1);

namespace App\ElasticSearch\Index\Messages\Single;

use App\Shared\FieldHandlers\Categories\CategoriesFieldHandler;
use App\Shared\FieldHandlers\Entry\EntryFieldHandler;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use App\Shared\FieldHandlers\Tags\TagsFieldHandler;
use craft\base\Element;
use craft\elements\Category;
use craft\elements\Entry;
use craft\elements\Tag;
use craft\errors\InvalidFieldException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 */
class CreateMessageIndexBodyTest extends TestCase
{
    private CreateMessageIndexBody $createMessageIndexBody;

    /** @var mixed[] */
    private array $calls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->createMessageIndexBody = new CreateMessageIndexBody(
            genericHandler: $this->mockGenericHandler(),
            tagsFieldHandler: $this->mockTagsFieldHandler(),
            entryFieldHandler: $this->mockEntryFieldHandler(),
            categoriesFieldHandler: $this->mockCategoryFieldHandler(),
        );
    }

    /**
     * @return GenericHandler&MockObject
     */
    private function mockGenericHandler(): mixed
    {
        $genericHandler = $this->createMock(
            GenericHandler::class,
        );

        $genericHandler->method('getString')->willReturnCallback(
            function (Element $element, string $field): string {
                $this->calls[] = [
                    'object' => 'GenericHandler',
                    'method' => 'getString',
                    'element' => $element,
                    'field' => $field,
                ];

                return 'genericString';
            }
        );

        return $genericHandler;
    }

    /**
     * @return TagsFieldHandler&MockObject
     */
    private function mockTagsFieldHandler(): mixed
    {
        $tagsFieldHandler = $this->createMock(
            TagsFieldHandler::class,
        );

        $tagsFieldHandler->method('getAll')->willReturnCallback(
            function (Element $element, string $field): array {
                $this->calls[] = [
                    'object' => 'TagsFieldHandler',
                    'method' => 'getAll',
                    'element' => $element,
                    'field' => $field,
                ];

                $tag1 = $this->createMock(Tag::class);

                $tag1->title = 'test tag title 1';

                $tag1->uid = 'tagUid1';

                $tag2 = $this->createMock(Tag::class);

                $tag2->title = 'test tag title 2';

                $tag2->uid = 'tagUid2';

                return [$tag1, $tag2];
            }
        );

        return $tagsFieldHandler;
    }

    /**
     * @return EntryFieldHandler&MockObject
     */
    private function mockEntryFieldHandler(): mixed
    {
        $entryFieldHandler = $this->createMock(
            EntryFieldHandler::class,
        );

        $entryFieldHandler->method('getAll')->willReturnCallback(
            function (Element $element, string $field): array {
                $this->calls[] = [
                    'object' => 'EntryFieldHandler',
                    'method' => 'getAll',
                    'element' => $element,
                    'field' => $field,
                ];

                $entry1 = $this->createMock(Entry::class);

                $entry1->title = 'test entry title 1';

                $entry1->slug = 'test-entry-slug-1';

                $entry1->uid = 'entryUid1';

                $entry2 = $this->createMock(Entry::class);

                $entry2->title = 'test entry title 2';

                $entry2->slug = 'test-entry-slug-2';

                $entry2->uid = 'entryUid2';

                return [$entry1, $entry2];
            }
        );

        return $entryFieldHandler;
    }

    /**
     * @return CategoriesFieldHandler&MockObject
     */
    private function mockCategoryFieldHandler(): mixed
    {
        $categoriesFieldHandler = $this->createMock(
            CategoriesFieldHandler::class,
        );

        $categoriesFieldHandler->method('getAll')->willReturnCallback(
            function (Element $element, string $field): array {
                $this->calls[] = [
                    'object' => 'CategoriesFieldHandler',
                    'method' => 'getAll',
                    'element' => $element,
                    'field' => $field,
                ];

                $cat1 = $this->createMock(Category::class);

                $cat1->title = 'test cat title 1';

                $cat1->slug = 'test-cat-slug-1';

                $cat1->uid = 'catUid1';

                $cat2 = $this->createMock(Category::class);

                $cat2->title = 'test cat title 2';

                $cat2->slug = 'test-cat-slug-2';

                $cat2->uid = 'catUid2';

                return [$cat1, $cat2];
            }
        );

        return $categoriesFieldHandler;
    }

    /**
     * @throws InvalidFieldException
     */
    public function testFromMessage(): void
    {
        $message = $this->createMock(Entry::class);

        $message->title = 'test message title';

        $return = $this->createMessageIndexBody->fromMessage(
            message: $message,
        );

        self::assertSame(
            [
                'title' => 'test message title',
                'speakerName' => 'test entry title 1, test entry title 2',
                'speakerSlug' => 'test-entry-slug-1, test-entry-slug-2',
                'speakerId' => 'entryUid1, entryUid2',
                'messageText' => 'genericString',
                'messageSeries' => 'test cat title 1, test cat title 2',
                'messageSeriesSlug' => 'test-cat-slug-1, test-cat-slug-2',
                'messageSeriesId' => 'catUid1, catUid2',
                'shortDescription' => 'genericString',
                'tags' => 'test tag title 1, test tag title 2',
                'tagIds' => 'tagUid1, tagUid2',
            ],
            $return,
        );

        self::assertSame(
            [
                [
                    'object' => 'EntryFieldHandler',
                    'method' => 'getAll',
                    'element' => $message,
                    'field' => 'profile',
                ],
                [
                    'object' => 'GenericHandler',
                    'method' => 'getString',
                    'element' => $message,
                    'field' => 'messageText',
                ],
                [
                    'object' => 'CategoriesFieldHandler',
                    'method' => 'getAll',
                    'element' => $message,
                    'field' => 'messageSeries',
                ],
                [
                    'object' => 'GenericHandler',
                    'method' => 'getString',
                    'element' => $message,
                    'field' => 'shortDescription',
                ],
                [
                    'object' => 'TagsFieldHandler',
                    'method' => 'getAll',
                    'element' => $message,
                    'field' => 'keywords',
                ],
            ],
            $this->calls,
        );
    }
}
