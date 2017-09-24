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

namespace Bluemaex\Cyberschall\Index;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class Provider implements ControllerProviderInterface, ServiceProviderInterface, BootableProviderInterface
{
    public function connect(Application $app): \Silex\ControllerCollection
    {
        /** @var \Silex\ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers
            ->get('/config', 'index.controller:indexAction')
            ->bind('indexIndex');

        $controllers
            ->get('/admin', 'index.controller:adminAction')
            ->bind('indexAdmin');

        return $controllers;
    }

    public function register(Container $container): void
    {
        $container['index.controller'] = function () use ($container) {
            return new Controller($container['security.token_storage']->getToken());
        };
    }

    public function boot(Application $app): void
    {
        $app->mount('', $this->connect($app));
    }
}
