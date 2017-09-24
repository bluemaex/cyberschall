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

use Bluemaex\Cyberschall\Command;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Knp\Console\ConsoleEvent;
use Knp\Console\ConsoleEvents;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Console\Helper\QuestionHelper;

class Provider implements ServiceProviderInterface
{
    public function register(Container $app): void
    {
        $app['dispatcher']->addListener(
            ConsoleEvents::INIT,
            function (ConsoleEvent $event) use ($app): void {
                $console = $event->getApplication();

                $console->addCommands([
                    new Command\SetupDemoUser($app),
                ]);

                $console->addCommands([
                    new \Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand(),
                    new \Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand(),
                    new \Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand(),
                    new \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand(),
                    new \Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand(),
                    new \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand(),
                    new \Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand(),
                    new \Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand(),
                    new \Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand(),
                    new \Doctrine\ORM\Tools\Console\Command\EnsureProductionSettingsCommand(),
                    new \Doctrine\ORM\Tools\Console\Command\GenerateRepositoriesCommand(),
                    new \Doctrine\ORM\Tools\Console\Command\GenerateEntitiesCommand(),
                    new \Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand(),
                    new \Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand(),
                    new \Doctrine\ORM\Tools\Console\Command\InfoCommand(),
                ]);

                $entityManager = $app['orm.ems']['cyberschall'];
                $console->getHelperSet()->set(new EntityManagerHelper($entityManager), 'em');
                $console->getHelperSet()->set(new ConnectionHelper($entityManager->getConnection()), 'db');
                $console->getHelperSet()->set(new QuestionHelper($entityManager->getConnection()), 'question');
            }
        );
    }
}
