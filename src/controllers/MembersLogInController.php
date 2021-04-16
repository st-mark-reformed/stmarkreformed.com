<?php

namespace src\controllers;

use Craft;
use craft\config\GeneralConfig;
use craft\elements\User;
use craft\helpers\UrlHelper;
use craft\helpers\User as UserHelper;
use craft\services\Security;
use craft\services\Users;
use craft\web\User as WebUser;
use Exception;
use src\services\StorageService;
use yii\web\Response;

class MembersLogInController extends BaseController
{
    /** @var WebUser */
    private $userSession;
    /** @var Users */
    private $users;
    /** @var StorageService $storage */
    private $storage;
    /** @var GeneralConfig */
    private $generalConfig;
    /** @var Security */
    private $security;

    public function init()
    {
        parent::init();

        $this->userSession = Craft::$app->getUser();
        $this->users = Craft::$app->getUsers();
        $this->storage = StorageService::getInstance();
        $this->generalConfig = Craft::$app->getConfig()->getGeneral();
        $this->security = Craft::$app->getSecurity();
    }

    /**
     * @throws Exception
     */
    public function actionLogIn() : Response
    {
        if (! $this->userSession->getIsGuest()) {
            $this->redirect(
                UrlHelper::urlWithParams(
                    $this->request->getBodyParam(
                        'return',
                        '/members'
                    ),
                    $this->request->getQueryParams()
                ),
                303
            );
        }

        if (! $this->request->getIsPost()) {
            return $this->renderLogInPage();
        }

        return $this->respondToPost();
    }

    private function renderLogInPage() : Response
    {
        return $this->renderTemplate(
            '_core/MemberLogIn.twig',
            [
                'heroHeading' => 'Log In to Members Area',
                'return' => $this->request->getParam(
                    'return',
                    '/members'
                ),
            ],
            false
        );
    }

    /**
     * @throws Exception
     *
     * @see \craft\controllers\UsersController::actionLogin
     */
    private function respondToPost() : Response
    {
        $this->requirePostRequest();

        $email = $this->request->getBodyParam('email');
        $password = $this->request->getBodyParam('password');

        $user = $this->users->getUserByUsernameOrEmail($email);

        if ($user === null || $user->password === null) {
            // Delay to match $user->authenticate()'s delay
            $this->security->validatePassword(
                'p@ss1w0rd',
                '$2y$13$nj9aiBeb7RfEfYP3Cum6Revyu14QelGGxwcnFUKXIrQUitSodEPRi'
            );

            return $this->handleLoginFailure(
                User::AUTH_INVALID_CREDENTIALS
            );
        }

        if (! $user->authenticate($password)) {
            return $this->handleLoginFailure(
                $user->authError,
                $user
            );
        }

        if ($this->generalConfig->rememberedUserSessionDuration !== 0) {
            $duration = $this->generalConfig->rememberedUserSessionDuration;
        } else {
            $duration = $this->generalConfig->userSessionDuration;
        }

        if (! $this->userSession->login($user, $duration)) {
            return $this->handleLoginFailure(null, $user);
        }

        return $this->redirect(
            UrlHelper::urlWithParams(
                $this->request->getBodyParam(
                    'return',
                    '/members'
                ),
                array_filter(
                    $this->request->getQueryParams(),
                    function (string $key) {
                        return $key !== 'return';
                    },
                    ARRAY_FILTER_USE_KEY
                )
            ),
            303
        );
    }

    /**
     * @throws Exception
     */
    private function handleLoginFailure(
        string $authError = null,
        User $user = null
    ) : Response {
        // Delay randomly between 0 and 1.5 seconds.
        usleep(random_int(0, 1500000));

        $message = UserHelper::getLoginFailureMessage(
            $authError,
            $user
        );

        $this->storage->set($message, 'errorMessage');

        return $this->renderLogInPage();
    }
}
