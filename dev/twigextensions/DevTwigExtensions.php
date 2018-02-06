<?php

namespace dev\twigextensions;

use dev\services\ConfigService;
use dev\services\FileOperationsService;
use dev\services\NavService;

/**
 * Class GetEnvTwigExtension
 *
 * @package dev\twigextensions
 */
class DevTwigExtensions extends \Twig_Extension
{
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
}
