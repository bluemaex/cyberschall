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

namespace Bluemaex\Cyberschall\Security;

use Bluemaex\Cyberschall\Entity;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class Provider implements ServiceProviderInterface
{
    public function register(Container $app): void
    {
        $app['security.user.token_authenticator'] = function () {
            return new TokenAuthenticator();
        $app['security.cors.preflight_matcher'] = function () {
            return new Cors\PreflightRequestMatcher();
        };

        $app['security.user.provider'] = function () use ($app) {
            return new UserProvider(
                $app['orm.em']->getRepository(Entity\User::class),
                $app['debug']
            );
        };

        $app->register(new \Silex\Provider\SecurityServiceProvider(), [
            'security.access_rules' => [
                ['^/(config)$', 'IS_AUTHENTICATED_ANONYMOUSLY'],
                ['^.*$', 'ROLE_USER'],
            ],
            'security.firewalls' => [
                'cors-preflight' => [
                    'stateless' => true,
                    'pattern' => $app['security.cors.preflight_matcher'],
                ],
                'main' => [
                    'pattern' => '^/.*$',
                    'stateless' => true,
                    'anonymous' => true,
                    'guard' => [
                        'authenticators' => [
                            'security.user.token_authenticator',
                        ],
                    ],
                    'users' => $app['security.user.provider'],
                ],
            ],
        ]);
    }
}
