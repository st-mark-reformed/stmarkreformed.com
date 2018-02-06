<?php

namespace dev\twigextensions;

use craft\helpers\Template;
use dev\services\NavService;
use dev\services\ConfigService;
use dev\services\TypesetService;
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
}
