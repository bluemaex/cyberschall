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

use Bluemaex\Cyberschall\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/** @SuppressWarnings("unused") */
class TokenAuthenticator extends AbstractGuardAuthenticator
{
    public function getCredentials(Request $request)
    {
        if ($request->headers->has('Authorization')) {
            $token = str_replace('Bearer ', '', $request->headers->get('Authorization'));

            return ['token' => $token];
        }

        if ($request->headers->has('X-Auth-Token')) {
            return ['token' => $request->headers->get('X-Auth-Token')];
        }

        if ($request->query->has('token')) {
            return ['token' => $request->query->get('token')];
        }

        return null;
    }

    public function getUser($credentials, UserProviderInterface $userProvider): User
    {
        return $userProvider->loadUserByUsername($credentials['token']);
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        // we do not to check credentials, because if the API
        // returns an user, it is actually already valid.
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): void
    {
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        $data = ['message' => strtr($exception->getMessageKey(), $exception->getMessageData())];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
    {
        $data = ['message' => 'Authentication Required'];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }
}
