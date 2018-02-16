<?php

namespace dev\twigextensions;

use Craft;
use TS\Text\Truncation;
use craft\helpers\Template;
use dev\services\NavService;
use dev\services\ConfigService;
use dev\services\StorageService;
use dev\services\TypesetService;
use dev\services\PaginationService;
use dev\services\FileOperationsService;

/**
 * Class GetEnvTwigExtension
 *
 * @package dev\twigextensions
 */
class DevTwigExtensions extends \Twig_Extension
{
    /**
     * Returns the twig filters
     * @return \Twig_Filter[]
     */
    public function getFilters() : array
    {
        return [
            new \Twig_Filter('typeset', [$this, 'typesetFilter']),
            new \Twig_Filter('smartypants', [$this, 'smartypantsFilter']),
            new \Twig_Filter('widont', [$this, 'widontFilter']),
            new \Twig_Filter('cast', [$this, 'cast']),
            new \Twig_Filter('truncate', [$this, 'truncate'])
        ];
    }

    /**
     * Returns the twig functions
     * @return \Twig_Function[]
     */
    public function getFunctions() : array
    {
        return [
            new \Twig_Function('getenv', function ($str) {
                return getenv($str);
            }),
            new \Twig_Function('fileTime', [
                new FileOperationsService(),
                'getFileTime',
            ]),
            new \Twig_Function('customConfig', [
                new ConfigService(),
                'getCustomConfig',
            ]),
            new \Twig_Function('navArray', [
                new NavService(),
                'buildNavArray',
            ]),
            new \Twig_Function('pageWithSubNav', [
                new NavService(),
                'getPageWithSubNav',
            ]),
            new \Twig_Function('uniqueId', function () {
                return uniqid('', false);
            }),
            new \Twig_Function('getUriPathSansPagination', function (bool $omitLeadingSlash = false) {
                return PaginationService::getUriPathSansPagination($omitLeadingSlash);
            }),
            new \Twig_Function('getPagination', function (array $options = []) {
                return PaginationService::getPagination($options);
            }),
            new \Twig_Function('includeAudioPlayer', function ($opt = null) {
                $storage = StorageService::getInstance();

                if ($opt !== null) {
                    $storage->set($opt === true, 'includeAudioPlayer');
                }

                return $storage->get('includeAudioPlayer');
            }),
            new \Twig_Function('checkOldPodcastQueryString', function () {
                return isset(Craft::$app->getRequest()->getQueryParams()['podcast']);
            }),
        ];
    }

    /**
     * @param string $str
     * @return \Twig_Markup
     */
    public function typesetFilter(string $str) : \Twig_Markup
    {
        return Template::raw((new TypesetService())->typeset($str));
    }

    /**
     * @param string $str
     * @return \Twig_Markup
     */
    public function smartypantsFilter(string $str) : \Twig_Markup
    {
        return Template::raw((new TypesetService())->smartypants($str));
    }

    /**
     * @param string $str
     * @return \Twig_Markup
     */
    public function widontFilter(string $str) : \Twig_Markup
    {
        return Template::raw((new TypesetService())->widont($str));
    }

    /**
     * Casts the value
     * @param mixed $val
     * @param string $to
     * @return mixed
     */
    public function cast($val, string $to = 'string')
    {
        if ($to === 'int') {
            return (int) $val;
        }

        if ($to === 'float') {
            return (float) $val;
        }

        return Template::raw((string) $val);
    }

    /**
     * Truncates HTML/text
     * @param string $val
     * @param int $limit
     * @param string $strategy Defaults to word
     * @return mixed
     * @throws \Exception
     */
    public function truncate(
        string $val,
        int $limit,
        string $strategy = 'word'
    ) {
        $strategies = [
            'char' => Truncation::STRATEGY_CHARACTER,
            'line' => Truncation::STRATEGY_LINE,
            'paragraph' => Truncation::STRATEGY_PARAGRAPH,
            'sentence' => Truncation::STRATEGY_SENTENCE,
            'word' => Truncation::STRATEGY_WORD,
        ];

        $strategy = $strategies[$strategy];

        $truncation = new Truncation($limit, $strategy);

        return Template::raw($truncation->truncate($val));
    }
}
