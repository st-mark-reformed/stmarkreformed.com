<?php

namespace dev\controllers;

use yii\web\Response;
use craft\web\Controller;
use yii\base\InvalidParamException;
use buzzingpixel\craftstatic\Craftstatic;

/**
 * Class BaseController
 */
abstract class BaseController extends Controller
{
    protected $allowAnonymous = true;

    /**
     * Renders a template
     * @param string $template The name of the template to load
     * @param array $variables The variables that should be available to the template
     * @param bool $cache Should static caching be used?
     * @param bool $minify Should minification be used
     * @return Response
     * @throws InvalidParamException if the view file does not exist.
     */
    public function renderTemplate(
        string $template,
        array $variables = [],
        $cache = true,
        $minify = true
    ) : Response {
        $response = parent::renderTemplate($template, $variables);

        if ($minify) {
            $options = [
                'cssMinifier' => '\Minify_CSSmin::minify',
                'jsMinifier' => '\JSMin\JSMin::minify'
            ];

            $response->data = \Minify_HTML::minify($response->data, $options);
        }

        if ($cache && getenv('STATIC_CACHE_ENABLED') === 'true') {
            Craftstatic::$plugin->getStaticHandler()->handleContent(
                $response->data
            );
        }

        return $response;
    }
}
