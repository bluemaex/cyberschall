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

namespace Bluemaex\Cyberschall;

class Environment
{
    public static function getenv(string $var, $default = null)
    {
        $value = getenv($var);
        if (false === $value) {
            return $default;
        }

        return $value;
    }
}
