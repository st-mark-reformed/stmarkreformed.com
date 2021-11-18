<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission;

use App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\Responder\ResponderContract;
use App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\Responder\ResponderFactory;
use App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\SendEmail\SendEmailContract;
use App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\SendEmail\SendEmailFactory;
use App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\SendEmail\SendEmailResult;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Slim\Interfaces\RouteInterface;

use function assert;

class PostSubmissionActionTest extends TestCase
{
    private PostSubmissionAction $action;

    /** @var mixed[] */
    private array $calls = [];

    public function setUp(): void
    {
        parent::setUp();

        $this->calls = [];

        $this->action = new PostSubmissionAction(
            sendEmailFactory: $this->mockSendEmailFactory(),
            responderFactory: $this->mockResponderFactory(),
        );
    }

    /**
     * @return MockObject&SendEmailFactory
     */
    private function mockSendEmailFactory(): mixed
    {
        $factory = $this->createMock(
            SendEmailFactory::class,
        );

        $factory->method('make')->willReturnCallback(
            function (FormValues $formValues): SendEmailContract {
                $this->calls[] = [
                    'object' => 'SendEmailFactory',
                    'method' => 'make',
                    'formValues' => $formValues,
                ];

                return $this->mockSendEmail();
            }
        );

        return $factory;
    }

    /**
     * @return MockObject&SendEmailContract
     */
    private function mockSendEmail(): mixed
    {
        $sendEmail = $this->createMock(
            SendEmailContract::class,
        );

        $sendEmail->method('send')->willReturnCallback(
            function (FormValues $formValues): SendEmailResult {
                $this->calls[] = [
                    'object' => 'SendEmailContract',
                    'method' => 'send',
                    'formValues' => $formValues,
                ];

                return new SendEmailResult(
                    sentSuccessfully: true,
                    formValues: $formValues,
                );
            }
        );

        return $sendEmail;
    }

    /**
     * @return MockObject&ResponderFactory
     */
    private function mockResponderFactory(): mixed
    {
        $factory = $this->createMock(
            ResponderFactory::class,
        );

        $factory->method('make')->willReturnCallback(
            function (SendEmailResult $result): ResponderContract {
                $this->calls[] = [
                    'object' => 'ResponderFactory',
                    'method' => 'make',
                    'result' => $result,
                ];

                return $this->mockResponder();
            }
        );

        return $factory;
    }

    /**
     * @return MockObject&ResponderContract
     */
    private function mockResponder(): mixed
    {
        $responder = $this->createMock(
            ResponderContract::class,
        );

        $responder->method('respond')->willReturnCallback(
            function (SendEmailResult $result): ResponseInterface {
                $this->calls[] = [
                    'object' => 'ResponderContract',
                    'method' => 'respond',
                    'result' => $result,
                ];

                $response = $this->createMock(
                    ResponseInterface::class,
                );

                $response->method('getStatusCode')->willReturn(
                    456,
                );

                return $response;
            }
        );

        return $responder;
    }

    public function testAddRoute(): void
    {
        $routeCollector = $this->createMock(
            RouteCollectorProxyInterface::class,
        );

        $routeCollector->method(self::anything())->willReturnCallback(
            function (): RouteInterface {
                $this->calls[] = ['anything' => 'anything'];

                return $this->createMock(
                    RouteInterface::class,
                );
            }
        );

        $routeCollector->method('post')
            ->willReturnCallback(
                function (
                    string $pattern,
                    string $class,
                ): RouteInterface {
                    $this->calls[] = [
                        'object' => 'RouteCollectorProxyInterface',
                        'method' => 'post',
                        'pattern' => $pattern,
                        'class' => $class,
                    ];

                    return $this->createMock(
                        RouteInterface::class,
                    );
                }
            );

        PostSubmissionAction::addRoute(
            routeCollector: $routeCollector,
        );

        self::assertSame(
            [
                ['anything' => 'anything'],
                [
                    'object' => 'RouteCollectorProxyInterface',
                    'method' => 'post',
                    'pattern' => '/contact-form-submission',
                    'class' => PostSubmissionAction::class,
                ],
            ],
            $this->calls,
        );
    }

    public function testInvokeWhenFormIsValid(): void
    {
        $request = $this->createMock(
            ServerRequestInterface::class,
        );

        $request->method('getParsedBody')->willReturn([
            'from_url' => '/test/from/url',
            'redirect_url' => '/test/redirect/url',
            'your_name' => 'test name',
            'your_email' => 'test@foobar.com',
            'message' => 'test message',
        ]);

        $response = ($this->action)(request: $request);

        self::assertSame(456, $response->getStatusCode());

        self::assertCount(4, $this->calls);

        self::assertSame(
            'SendEmailFactory',
            $this->calls[0]['object'],
        );

        self::assertSame(
            'make',
            $this->calls[0]['method'],
        );

        $formValues = $this->calls[0]['formValues'];

        assert($formValues instanceof FormValues);

        self::assertTrue($formValues->isValid());

        self::assertFalse($formValues->isNotValid());

        self::assertSame([], $formValues->errorMessages());

        $tmp = $formValues->withErrorMessage(
            'test key',
            'test val'
        );

        self::assertNotSame(
            $tmp,
            $formValues,
        );

        self::assertFalse($tmp->isValid());

        self::assertTrue($tmp->isNotValid());

        self::assertSame(
            ['test key' => 'test val'],
            $tmp->errorMessages(),
        );

        self::assertSame(
            ['test val'],
            $tmp->formattedErrorMessages(),
        );

        self::assertSame(
            '/test/from/url',
            $formValues->fromUrl()->toString(),
        );

        self::assertSame(
            '/test/redirect/url',
            $formValues->redirectUrl()->toString(),
        );

        self::assertSame(
            'test name',
            $formValues->name()->toString(),
        );

        self::assertSame(
            'test name',
            $formValues->nameRaw(),
        );

        self::assertSame(
            'test@foobar.com',
            $formValues->email()->toString(),
        );

        self::assertSame(
            'test@foobar.com',
            $formValues->emailRaw(),
        );

        self::assertSame(
            'test message',
            $formValues->message()->toString(),
        );

        self::assertSame(
            'test message',
            $formValues->messageRaw(),
        );
    }

    public function testInvokeWhenFormIsInValid(): void
    {
        $request = $this->createMock(
            ServerRequestInterface::class,
        );

        $request->method('getParsedBody')->willReturn([]);

        $response = ($this->action)(request: $request);

        self::assertSame(456, $response->getStatusCode());

        self::assertCount(4, $this->calls);

        self::assertSame(
            'SendEmailFactory',
            $this->calls[0]['object'],
        );

        self::assertSame(
            'make',
            $this->calls[0]['method'],
        );

        $formValues = $this->calls[0]['formValues'];

        assert($formValues instanceof FormValues);

        self::assertFalse($formValues->isValid());

        self::assertTrue($formValues->isNotValid());

        self::assertSame(
            [
                'your_name' => 'Must not be empty',
                'your_email' => 'Email address must be a valid email address',
                'message' => 'Must not be empty',
            ],
            $formValues->errorMessages()
        );
    }
}
