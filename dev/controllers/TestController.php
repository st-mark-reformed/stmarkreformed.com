<?php

namespace dev\controllers;

use yii\web\HttpException;

/**
 * http://stmarkreformed.test:3000/?action=dev/test/test
 */
class TestController extends BaseController
{
    public function actionTest()
    {
        throw new HttpException(404);
    }
}
