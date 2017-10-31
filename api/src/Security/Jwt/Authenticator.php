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

use Bluemaex\Cyberschall\Entity\User;
use Lcobucci\JWT;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/** @SuppressWarnings("unused") */
class Authenticator extends AbstractGuardAuthenticator
{
    /** @var JWT\Parser * */
    private $parser;
    /** @var JWT\ValidationData * */
    private $constraint;
    /** @var JWT\Signer * */
    private $signer;
    /** @var string * */
    private $secret;

    public function __construct(
        JWT\Parser $parser,
        JWT\ValidationData $constraint,
        JWT\Signer $signer,
        string $secret
    ) {
        $this->parser = $parser;
        $this->constraint = $constraint;
        $this->signer = $signer;
        $this->secret = $secret;
    }

    public function getCredentials(Request $request)
    {
        $token = $request->query->get('token');
        if ($request->headers->has('Authorization')) {
            $token = str_replace('Bearer ', '', $request->headers->get('Authorization', ''));
        }

        try {
            return $this->parser->parse($token);
        } catch (\InvalidArgumentException $e) {
            return null;
        }
    }

    public function getUser($credentials, UserProviderInterface $userProvider): User
    {
        if (!$credentials->validate($this->constraint)) {
            throw new AuthenticationException('Invalid token.');
        }

        if (!$credentials->hasClaim('username') && !$credentials->hasClaim('roles')) {
            throw new AuthenticationException('Invalid Token.');
        }

        if (!$credentials->verify($this->signer, $this->secret)) {
            throw new AuthenticationException('Could not verify token. ');
        }

        return $userProvider->loadUserByUsername($credentials->getClaim('username'));
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        // if user has a new role, but old token - invalidate
        // otherwise, we are all good to go.
        return $credentials->getClaim('roles') === $user->getRoles();
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): void
    {
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        $data = ['message' => $exception->getMessage()];

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
