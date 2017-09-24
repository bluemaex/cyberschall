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
use Bluemaex\Cyberschall\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    const ERROR_NOTFOUND = 'Access-Token is not valid';

    /** @var UserRepository */
    private $repository;
    /** @var bool */
    private $debug;

    public function __construct(UserRepository $repository, bool $debug)
    {
        $this->repository = $repository;
        $this->debug = $debug;
    }

    public function loadUserByUsername($accessToken): User
    {
        $user = $this->repository->findBy(['id' => $accessToken]);
        if (null === $user) {
            throw new UsernameNotFoundException(self::ERROR_NOTFOUND);
        }

        return $user;
    }

    public function refreshUser(UserInterface $user): User
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getPassword());
    }

    public function supportsClass($class): bool
    {
        return 'Bluemaex\Cyberschall\Entity\User' === $class;
    }
}
