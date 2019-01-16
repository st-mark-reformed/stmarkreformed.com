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
            new \Twig_Filter('truncate', [$this, 'truncate']),
            new \Twig_Filter('ucfirst', [$this, 'ucfirst']),
            new \Twig_Filter('replaceLinkProtocol', [$this, 'replaceLinkProtocol']),
        ];
    }

    /**
     * Returns the twig functions
     * @return \Twig_Function[]
     */
    public function getFunctions() : array
    {
        return [
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
            new \Twig_Function('getStorage', function ($key, $namespace = 'storage') {
                return StorageService::getInstance()->get($key, $namespace);
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
     * @return \Twig_Markup|int|float
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
     * @return \Twig_Markup
     * @throws \Exception
     */
    public function truncate(
        string $val,
        int $limit,
        string $strategy = 'word'
    ) : \Twig_Markup {
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

    /**
     * Uppercases first letter
     * @param string $val
     * @return \Twig_Markup
     */
    public function ucfirst(string $val) : \Twig_Markup
    {
        return Template::raw(ucfirst($val));
    }

    /**
     * Replace link protocol
     * @param string $val
     * @return \Twig_Markup
     */
    public function replaceLinkProtocol(string $val, $protocol) : \Twig_Markup
    {
        return Template::raw(
            str_replace(['http://', 'https://'], $protocol, $val)
        );
    }
}
