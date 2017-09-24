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

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\FingersCrossedHandler;
use Pimple\Container;

class Provider extends \Silex\Provider\MonologServiceProvider
{
    public function register(Container $app): void
    {
        parent::register($app);

        $app['monolog.handler'] = function (Container $app) {
            $threshold = parent::translateLevel($app['monolog.threshold']);

            $handler = new ErrorLogHandler();
            $handler->setFormatter(
                new LineFormatter('%channel%.%level_name%: %message% %context% %extra%')
            );

            return new FingersCrossedHandler($handler, $threshold);
        };
    }
}
