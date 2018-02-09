<?php

namespace dev\services;

use craft\elements\Entry;
use dev\services\StorageService;

/**
 * Class NavService
 */
class NavService
{
    /** @var Entry[] $root  */
    private $root = [];

    /** @var Entry[] $entries */
    private $entries = [];

    /** @var array $output */
    private $output = [];

    /**
     * Gets a page with subnav
     * @param string $pageSlug
     * @return array
     * @throws \Exception
     */
    public function getPageWithSubNav(string $pageSlug) : array
    {
        $navArray = $this->buildNavArray();

        $ourItem = [];

        foreach ($navArray as $item) {
            if ($item['model']->slug !== $pageSlug) {
                continue;
            }

            $ourItem = $item;

            break;
        }

        return $ourItem;
    }

    /**
     * Builds the nav array
     * @return array
     * @throws \Exception
     */
    public function buildNavArray() : array
    {
        $check = StorageService::getInstance()->get('navArray');
        if (\is_array($check)) {
            return $check;
        }

        $this->root = [];
        $this->entries = [];
        $this->output = [];

        $query = Entry::find();
        $query->section = 'pages';

        /** @var Entry[] $entries */
        $entries = $query->all();

        foreach ($entries as $entry) {
            $includeInNav = (bool) ((int) $entry->includeInNav);

            if (! $includeInNav) {
                continue;
            }

            $parent = (int) ($entry->parent->id ?? 0);

            if ($parent > 0) {
                $this->entries[$parent][$entry->id] = $entry;
                continue;
            }

            $this->root[$entry->id] = $entry;
        }

        foreach ($this->root as $entry) {
            $this->output[] = $this->buildOutput($entry);
        }

        StorageService::getInstance()->set($this->output, 'navArray');

        return $this->output;
    }

    /**
     * Builds output
     * @param Entry $entry
     * @return array
     */
    private function buildOutput(Entry $entry) : array
    {
        $hasChildren = isset($this->entries[$entry->id]);

        $item = [
            'model' => $entry,
            'hasChildren' => $hasChildren,
            'children' => [],
        ];

        if (! $hasChildren) {
            return $item;
        }

        $children = [];

        foreach ($this->entries[$entry->id] as $subEntry) {
            $children[] = $this->buildOutput($subEntry);
        }

        $item['children'] = $children;

        return $item;
    }
}
