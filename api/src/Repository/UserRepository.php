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

namespace Bluemaex\Cyberschall\Repository;

use Bluemaex\Cyberschall\Entity\User;
use Doctrine\ORM\EntityRepository;
use Ramsey\Uuid\Uuid;

class UserRepository extends EntityRepository
{
    const ALIAS = 'u';

    public function update(User $user)
    {
        return $this->getEntityManager()->flush($user);
    }

    public function create(User $user): void
    {
        $em = $this->getEntityManager();

        if (null === $user->getId()) {
            $user = $user->setId(Uuid::getFactory()->uuid4());
        }

        $em->transactional(
            function ($em) use ($user): void {
                $em->persist($user);
                $em->flush();
            }
        );
    }

    public function getUserByCredentials($username, $password): ?User
    {
        $user = $this->findOneBy(['username' => $username]);
        if ($user instanceof User && password_verify($password, $user->getPassword())) {
            return $user;
        }

        return null;
    }
}
