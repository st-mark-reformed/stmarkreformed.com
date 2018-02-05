<?php

namespace dev\twigextensions;

use dev\services\FileOperationsService;

/**
 * Class GetEnvTwigExtension
 *
 * @package dev\twigextensions
 */
class GetEnvTwigExtension extends \Twig_Extension
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
                'getFileTime'
            ]),
        ];
    }
}
