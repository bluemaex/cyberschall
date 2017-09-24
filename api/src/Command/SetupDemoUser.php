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

namespace Bluemaex\Cyberschall\Command;

use Bluemaex\Cyberschall\Entity;
use Pimple\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupDemoUser extends Command
{
    const USER_ID = 'test-id';
    const USERNAME = 'TEST';
    const EMAIL = 'test@test.test';

    /** @var Container */
    private $app;

    public function __construct(Container $app)
    {
        parent::__construct();
        $this->app = $app;
    }

    protected function configure(): void
    {
        $this
            ->setName('setup:demo-user')
            ->setDescription('Add an demo user to the database.');
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repo = $this->app['orm.em']->getRepository(Entity\User::class);
        $user = $repo->find(self::USER_ID);
        if ($user) {
            return $output->writeln('Example User already exists');
        }

        $user = new Entity\User();
        $user->setId(self::USER_ID)
            ->setEmail(self::EMAIL)
            ->setUsername(self::USERNAME)
            ->setPassword(password_hash('no', PASSWORD_DEFAULT));

        $repo->create($user);

        $output->writeln('<info>Example User created</info>');
    }
}
