<?php

declare(strict_types=1);

namespace Config\Dependencies;

use App\ExceptionHandling\CsrfFailureHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RxAnte\AppBootstrap\Dependencies\Bindings;
use Slim\Csrf\Guard as CsrfGuard;

readonly class CsrfBindings
{
    public function __invoke(Bindings $bindings): void
    {
        $bindings->addBinding(
            CsrfGuard::class,
            static function (ContainerInterface $di): CsrfGuard {
                return new CsrfGuard(
                    $di->get(ResponseFactoryInterface::class),
                    failureHandler: static function (
                        ServerRequestInterface $request,
                        RequestHandlerInterface $handler,
                    ) use ($di): ResponseInterface {
                        return $di->get(CsrfFailureHandler::class)->process(
                            $request,
                            $handler,
                        );
                    },
                );
            },
        );
    }
}
