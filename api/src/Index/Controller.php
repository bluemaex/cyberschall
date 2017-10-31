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

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class Controller
{
    public function __construct(TokenInterface $token = null)
    {
        $this->user = null !== $token ? $token->getUser() : null;
    }

    public function indexAction(): JsonResponse
    {
        return new JsonResponse(['hello' => 'world']);
    }

    public function adminAction(): JsonResponse
    {
        return new JsonResponse(['hello' => $this->user]);
    }
}
