<?php

namespace dev\services;

use Craft;
use yii\web\Response;
use craft\helpers\Template;

/**
 * Class RouteService
 */
class RouteService
{
    /**
     * Gets a file's modification time
     * @param string $route
     * @param array $args
     * @return \Twig_Markup
     */
    public function controllerRoute(string $route, array $args = []) : \Twig_Markup
    {
        $response = Craft::$app->runAction($route, $args);
        $data = $response instanceof Response ? $response->data : $response;
        return Template::raw($data);
    }
}
