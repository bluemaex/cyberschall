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

namespace Bluemaex\Cyberschall\Auth;

use Bluemaex\Cyberschall\Entity\User;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;

class Provider implements ControllerProviderInterface, ServiceProviderInterface, BootableProviderInterface
{
    public function connect(Application $app): ControllerCollection
    {
        /** @var \Silex\ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers
            ->post('/login', 'auth.controller:loginAction')
            ->bind('authLogin');

        return $controllers;
    }

    public function register(Container $container): void
    {
        $container['auth.controller'] = function () use ($container) {
            return new Controller(
                $container['orm.em']->getRepository(User::class),
                $container['security.jwt.builder'],
                $container['security.jwt.signer'],
                $container['security.jwt.secret']
            );
        };
    }

    public function boot(Application $app): void
    {
        $app->mount('/auth', $this->connect($app));
    }
}
