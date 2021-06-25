<?php

namespace src\controllers;

use Craft;
use craft\helpers\UrlHelper;

class MembersBaseController extends BaseController
{
    protected $isLoggedIn = false;

    public function init()
    {
        parent::init();

        $userSession = Craft::$app->getUser();

        if (! $userSession->getIsGuest()) {
            $this->isLoggedIn = true;

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
