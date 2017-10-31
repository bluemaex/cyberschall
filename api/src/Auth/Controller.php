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

use Bluemaex\Cyberschall\Repository\UserRepository;
use Lcobucci\JWT;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Controller
{
    const EXPIRATION_TIME = 3600 * 24;

    /** @var UserRepository */
    private $userRepo;
    /** @var JWT\Builder * */
    private $builder;
    /** @var JWT\Signer * */
    private $signer;
    /** @var string * */
    private $secret;

    public function __construct(
        UserRepository $userRepo,
        JWT\Builder $builder,
        JWT\Signer $signer,
        string $secret
    ) {
        $this->userRepo = $userRepo;
        $this->builder = $builder;
        $this->signer = $signer;
        $this->secret = $secret;
    }

    public function loginAction(Request $request): JsonResponse
    {
        $username = $request->request->get('username');
        $password = $request->request->get('password');

        $user = $this->userRepo->getUserByCredentials($username, $password);
        if (!$user) {
            throw new NotFoundHttpException('Given User/Password Combination not found.');
        }

        $token = $this->builder
                    ->setIssuer('bluemaex.cyberschall.api')
                    ->setSubject('bluemaex.cyberschall.user')
                    ->setIssuedAt(time())
                    ->setNotBefore(time())
                    ->setExpiration(time() + self::EXPIRATION_TIME)
                    ->set('username', $user->getUsername())
                    ->set('roles', $user->getRoles())
                    ->sign($this->signer, $this->secret)
                    ->getToken();

        return new JsonResponse(['token' => (string) $token]);
    }
}
