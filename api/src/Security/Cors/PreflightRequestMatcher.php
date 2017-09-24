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

namespace Bluemaex\Cyberschall\Security\Cors;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

class PreflightRequestMatcher implements RequestMatcherInterface
{
    public function matches(Request $request)
    {
        return $this->isPreflightRequest($request);
    }

    private function isPreflightRequest(Request $request)
    {
        return 'OPTIONS' === $request->getMethod() && $request->headers->has('Access-Control-Request-Method');
    }
}
