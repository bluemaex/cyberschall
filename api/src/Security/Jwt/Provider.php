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

namespace Bluemaex\Cyberschall\Security\Jwt;

use Lcobucci\JWT as JWT;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class Provider implements ServiceProviderInterface
{
    public function register(Container $app): void
    {
        $app['security.jwt.builder'] = function () {
            return new JWT\Builder();
        };

        $app['security.jwt.parser'] = function () {
            return new JWT\Parser();
        };

        $app['security.jwt.signer'] = function () {
            return new JWT\Signer\Hmac\Sha256();
        };

        $app['security.jwt.validation'] = $app->protect(function ($currentTime = null) {
            return new JWT\ValidationData($currentTime);
        });

        $app['security.jwt.authenticator'] = function () use ($app) {
            return new Authenticator(
                $app['security.jwt.parser'],
                $app['security.jwt.validation'](),
                $app['security.jwt.signer'],
                $app['security.jwt.secret']
            );
        };
    }
}
