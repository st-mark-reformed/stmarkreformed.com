<?php

declare(strict_types=1);

namespace App\Http\Response\LogIn;

use App\Shared\Testing\TestCase;
use craft\config\GeneralConfig;
use craft\elements\User;
use craft\services\Users;
use craft\web\User as UserSession;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;

class CraftUserHandlerTest extends TestCase
{
    /** @phpstan-ignore-next-line */
    private User|null $returnedUser = null;

    private bool $authenticateReturn = false;

    private bool $loginReturn = false;

    private GeneralConfig $config;

    private CraftUserHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->returnedUser = $this->mockUser();

        $this->config = new GeneralConfig();

        $this->config->rememberedUserSessionDuration = 0;

        $this->handler = new CraftUserHandler(
            usersService: $this->mockUsersService(),
            config: $this->config,
            userSession: $this->mockUserSession(),
            userHelper: $this->mockUserHelper(),
        );
    }

    /**
     * @return User&MockObject
     *
     * @phpstan-ignore-next-line
     */
    private function mockUser(): User|MockObject
    {
        $user = $this->createMock(User::class);

        $user->authError = 'testAuthError';

        $user->method('authenticate')->willReturnCallback(
            function (): bool {
                return $this->genericCall(
                    object: 'User',
                    return: $this->authenticateReturn,
                );
            }
        );

        return $user;
    }

    /**
     * @return Users&MockObject
     */
    private function mockUsersService(): Users|MockObject
    {
        $service = $this->createMock(Users::class);

        $service->method('getUserByUsernameOrEmail')
            ->willReturnCallback(
                /** @phpstan-ignore-next-line */
                function (): User|null {
                    return $this->genericCall(
                        object: 'UsersService',
                        return: $this->returnedUser,
                    );
                }
            );

        return $service;
    }

    /**
     * @return UserSession&MockObject
     */
    private function mockUserSession(): UserSession|MockObject
    {
        $service = $this->createMock(UserSession::class);

        $service->method('login')->willReturnCallback(
            function (): bool {
                return $this->genericCall(
                    object: 'UserSession',
                    return: $this->loginReturn,
                );
            }
        );

        return $service;
    }

    /**
     * @return CraftUserHelperFacade&MockObject
     */
    private function mockUserHelper(): CraftUserHelperFacade|MockObject
    {
        $helper = $this->createMock(
            CraftUserHelperFacade::class,
        );

        $helper->method('getLoginFailureMessage')->willReturnCallback(
            function (): string {
                return $this->genericCall(
                    object: 'CraftUserHelperFacade',
                    return: 'GetLoginFailureMessageReturn',
                );
            }
        );

        return $helper;
    }

    /**
     * @throws Exception
     */
    public function testLogUserInWhenUserIsNull(): void
    {
        $this->returnedUser = null;

        $payload = $this->handler->logUserIn(
            email: 'foo@bar.baz',
            password: 'foo-bar-baz-123',
        );

        self::assertFalse($payload->succeeded());

        self::assertSame(
            'GetLoginFailureMessageReturn',
            $payload->message(),
        );

        self::assertSame(
            [
                [
                    'object' => 'UsersService',
                    'method' => 'getUserByUsernameOrEmail',
                    'args' => ['foo@bar.baz'],
                ],
                [
                    'object' => 'CraftUserHelperFacade',
                    'method' => 'getLoginFailureMessage',
                    'args' => [
                        'invalid_credentials',
                        null,
                    ],
                ],
            ],
            $this->calls,
        );
    }

    /**
     * @throws Exception
     */
    public function testLogUserInWhenPasswordIsNull(): void
    {
        $payload = $this->handler->logUserIn(
            email: 'foo@bar.baz',
            password: 'foo-bar-baz-123',
        );

        self::assertFalse($payload->succeeded());

        self::assertSame(
            'GetLoginFailureMessageReturn',
            $payload->message(),
        );

        self::assertSame(
            [
                [
                    'object' => 'UsersService',
                    'method' => 'getUserByUsernameOrEmail',
                    'args' => ['foo@bar.baz'],
                ],
                [
                    'object' => 'CraftUserHelperFacade',
                    'method' => 'getLoginFailureMessage',
                    'args' => [
                        'invalid_credentials',
                        null,
                    ],
                ],
            ],
            $this->calls,
        );
    }

    /**
     * @throws Exception
     */
    public function testLogUserInWhenAuthFails(): void
    {
        /** @phpstan-ignore-next-line */
        $this->returnedUser->password = 'foo-pass';

        $payload = $this->handler->logUserIn(
            email: 'foo@bar.baz',
            password: 'foo-bar-baz-123',
        );

        self::assertFalse($payload->succeeded());

        self::assertSame(
            'GetLoginFailureMessageReturn',
            $payload->message(),
        );

        self::assertSame(
            [
                [
                    'object' => 'UsersService',
                    'method' => 'getUserByUsernameOrEmail',
                    'args' => ['foo@bar.baz'],
                ],
                [
                    'object' => 'User',
                    'method' => 'authenticate',
                    'args' => ['foo-bar-baz-123'],
                ],
                [
                    'object' => 'CraftUserHelperFacade',
                    'method' => 'getLoginFailureMessage',
                    'args' => [
                        'testAuthError',
                        $this->returnedUser,
                    ],
                ],
            ],
            $this->calls,
        );
    }

    /**
     * @throws Exception
     */
    public function testLogInWhenLogInFails(): void
    {
        /** @phpstan-ignore-next-line */
        $this->returnedUser->password = 'foo-pass';

        $this->authenticateReturn = true;

        $this->config->rememberedUserSessionDuration = 432;

        $payload = $this->handler->logUserIn(
            email: 'foo@bar.baz',
            password: 'foo-bar-baz-123',
        );

        self::assertFalse($payload->succeeded());

        self::assertSame(
            'GetLoginFailureMessageReturn',
            $payload->message(),
        );

        self::assertSame(
            [
                [
                    'object' => 'UsersService',
                    'method' => 'getUserByUsernameOrEmail',
                    'args' => ['foo@bar.baz'],
                ],
                [
                    'object' => 'User',
                    'method' => 'authenticate',
                    'args' => ['foo-bar-baz-123'],
                ],
                [
                    'object' => 'UserSession',
                    'method' => 'login',
                    'args' => [
                        $this->returnedUser,
                        432,
                    ],
                ],
                [
                    'object' => 'CraftUserHelperFacade',
                    'method' => 'getLoginFailureMessage',
                    'args' => [
                        null,
                        $this->returnedUser,
                    ],
                ],
            ],
            $this->calls,
        );
    }

    /**
     * @throws Exception
     */
    public function testLogInWhenLogInSucceeds(): void
    {
        /** @phpstan-ignore-next-line */
        $this->returnedUser->password = 'foo-pass';

        $this->authenticateReturn = true;

        $this->loginReturn = true;

        $payload = $this->handler->logUserIn(
            email: 'foo@bar.baz',
            password: 'foo-bar-baz-123',
        );

        self::assertTrue($payload->succeeded());

        self::assertSame('', $payload->message());

        self::assertSame(
            [
                [
                    'object' => 'UsersService',
                    'method' => 'getUserByUsernameOrEmail',
                    'args' => ['foo@bar.baz'],
                ],
                [
                    'object' => 'User',
                    'method' => 'authenticate',
                    'args' => ['foo-bar-baz-123'],
                ],
                [
                    'object' => 'UserSession',
                    'method' => 'login',
                    'args' => [
                        $this->returnedUser,
                        3600,
                    ],
                ],
            ],
            $this->calls,
        );
    }
}
