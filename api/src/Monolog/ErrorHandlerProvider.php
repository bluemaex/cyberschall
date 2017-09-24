<?php

declare(strict_types=1);

/*
 * This file is part of Cyberschall.
 *
 * (c) Max Stockner <mail@bluemaex.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bluemaex\Cyberschall\Monolog;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Application;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ErrorHandlerProvider implements ServiceProviderInterface, BootableProviderInterface
{
    const MESSAGE_DEFAULT = 'Whoops, looks like something went wrong.';
    const MESSAGE_404 = 'The page you tried cannot be found.';

    public function register(Container $app): void
    {
        $app['error.message.factory'] = $app->protect(function ($message, $code, $e = null) use ($app) {
            $response = [
                'message' => $message,
                'status' => $code,
            ];

            // in debug mode, output more exception info
            if (true === $app['debug'] && $e instanceof \Exception) {
                $response['exception'] = [
                    $e->getMessage(),
                    $e->getFile().':'.$e->getLine(),
                    explode("\n", $e->getTraceAsString()),
                ];
            }

            return JsonResponse::create($response);
        });

        $app['error.handle.http.exception'] = $app->protect(
            function (HttpExceptionInterface $e, Request $request, $code) use ($app) {
                $message = self::MESSAGE_DEFAULT;
                if ($e->getStatusCode() > Response::HTTP_BAD_REQUEST) {
                    $message = self::MESSAGE_404;
                }

                return $app['error.message.factory']($message, $e->getStatusCode(), $e);
            }
        );

        $app['error.handle.default.exception'] = $app->protect(
            function (\Exception $e, Request $request, $code) use ($app) {
                return $app['error.message.factory'](self::MESSAGE_DEFAULT, $code, $e);
            }
        );

        $app['error.handle.fatal'] = $app->protect(
            function ($e) use ($app): void {
                echo $app['error.message.factory'](self::MESSAGE_DEFAULT, 500, $e)->getContent();
            }
        );
    }

    public function boot(Application $app): void
    {
        // handling for fatal errors
        $exceptionHandler = ExceptionHandler::register();
        $exceptionHandler->setHandler($app['error.handle.fatal']);

        // handling for exceptions
        $app->error($app['error.handle.http.exception']);
        $app->error($app['error.handle.default.exception']);
    }
}
