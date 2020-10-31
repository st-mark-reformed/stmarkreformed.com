<?php

namespace src\commands;

use Craft;
use yii\console\Controller;
use src\jobs\InitAllAssetsTransformsJob;

class ImageTransformsController extends Controller
{
    public function actionInit()
    {
        Craft::$app->getQueue()->push(new InitAllAssetsTransformsJob());
    }
}
