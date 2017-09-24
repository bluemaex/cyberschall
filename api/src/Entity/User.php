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

namespace Bluemaex\Cyberschall\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Bluemaex\Cyberschall\Repository\UserRepository")
 */
class User implements EquatableInterface, \JsonSerializable, UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     *
     * @var string
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true)
     *
     * @var string
     */
    protected $username;

    /**
     * @ORM\Column(type="string", unique=true)
     *
     * @var string
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    protected $password;

    /**
     * @ORM\Column(type="json_array")
     *
     * @var array
     */
    protected $roles = ['ROLE_USER'];

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getSalt(): void
    {
    }

    public function eraseCredentials(): void
    {
    }

    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof self) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        if ($this->roles !== $user->getRoles()) {
            return false;
        }

        return true;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'roles' => $this->roles,
        ];
    }
}
