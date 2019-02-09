<?php

namespace dev\commands;

use Craft;
use yii\console\Controller;
use dev\jobs\InitAllAssetsTransformsJob;

class ImageTransformsController extends Controller
{
    public function actionInit()
    {
        Craft::$app->getQueue()->push(new InitAllAssetsTransformsJob());
    }
}
