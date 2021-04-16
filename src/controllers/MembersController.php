<?php

namespace src\controllers;

use yii\web\Response;

class MembersController extends MembersBaseController
{
    public function actionIndex() : Response
    {
        return $this->renderTemplate(
            '_core/MembersIndex.twig',
            [
                'heroHeading' => 'Members Area',
                'listingItems' => [
                    [
                        'href' => '/members/hymns-of-the-month',
                        'title' => 'Hymns of the Month &rarr;',
                        'body' => 'Find resources for the St. Mark Hymns of the Month',
                    ]
                ],
            ]
        );
    }
}
