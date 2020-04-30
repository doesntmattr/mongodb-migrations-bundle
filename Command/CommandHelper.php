<?php

/*
 * This file is part of the AntiMattr MongoDB Migrations Bundle, a library by Matthew Fitzgerald.
 *
 * (c) 2014 Matthew Fitzgerald
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AntiMattr\Bundle\MongoDBMigrationsBundle\Command;

use AntiMattr\MongoDB\Migrations\Configuration\Configuration;
use AntiMattr\MongoDB\Migrations\Version;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Tools\Console\Helper\DocumentManagerHelper;
use ErrorException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
final class CommandHelper
{
    public static function configureMigrations(ContainerInterface $container, Configuration $configuration): void
    {
        $dir = $container->getParameter('mongo_db_migrations.dir_name');
        if (!is_dir($dir) && !@mkdir($dir, 0777, true) && !is_dir($dir)) {
            $error = error_get_last();
            throw new ErrorException(sprintf('Failed to create directory "%s" with message "%s"', $dir, $error['message']));
        }

        $configuration->setMigrationsCollectionName($container->getParameter('mongo_db_migrations.collection_name'));
        $configuration->setMigrationsDatabaseName($container->getParameter('mongo_db_migrations.database_name'));
        $configuration->setMigrationsDirectory($dir);
        $configuration->setMigrationsNamespace($container->getParameter('mongo_db_migrations.namespace'));
        $configuration->setName($container->getParameter('mongo_db_migrations.name'));
        $configuration->registerMigrationsFromDirectory($dir);
        $configuration->setMigrationsScriptDirectory($container->getParameter('mongo_db_migrations.script_dir_name'));

        self::injectContainerToMigrations($container, $configuration->getMigrations());
    }

    /**
     * @param null|string $dmName
     */
    public static function setApplicationDocumentManager(Application $application, $dmName): void
    {
        /** @var ManagerRegistry $managerRegistry */
        $managerRegistry = $application->getKernel()->getContainer()->get('doctrine_mongodb');

        /** @var DocumentManager $dm */
        $dm = $managerRegistry->getManager($dmName);

        $helperSet = $application->getHelperSet();
        $helperSet->set(new DocumentManagerHelper($dm), 'dm');
    }

    /**
     * @param Version[] $versions
     *
     * Injects the container to migrations aware of it
     */
    private static function injectContainerToMigrations(ContainerInterface $container, array $versions): void
    {
        foreach ($versions as $version) {
            $migration = $version->getMigration();
            if ($migration instanceof ContainerAwareInterface) {
                $migration->setContainer($container);
            }
        }
    }
}
