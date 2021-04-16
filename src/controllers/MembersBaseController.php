<?php

namespace src\controllers;

use Craft;
use craft\elements\User;
use craft\helpers\UrlHelper;

class MembersBaseController extends BaseController
{
    public function init()
    {
        parent::init();

        $userSession = Craft::$app->getUser();

        if (! $userSession->getIsGuest()) {
            return;
        }

        $this->redirect(
            UrlHelper::urlWithParams(
                '/members/log-in',
                array_merge(
                    $this->request->getQueryParams(),
                    [
                        'return' => '/' . ltrim(
                                $this->request->getFullPath(),
                            '/'
                        ),
                    ]
                )
            ),
            303
        );
    }
}
