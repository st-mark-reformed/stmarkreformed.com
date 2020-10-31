<?php

namespace src\controllers;

use Craft;
use yii\web\Response;
use craft\web\Controller;
use craft\helpers\UrlHelper;
use src\jobs\InitAllAssetsTransformsJob;

class ImageTransformsController extends Controller
{
    public function actionInit(): Response
    {
        Craft::$app->getQueue()->push(new InitAllAssetsTransformsJob());

        Craft::$app->getSession()->setNotice('All Assets Image Transforms Initiated');

        return $this->redirect(
            UrlHelper::cpUrl('utilities/image-transforms-utility')
        );
    }
}
