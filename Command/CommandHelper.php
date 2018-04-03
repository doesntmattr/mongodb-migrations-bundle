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
use Doctrine\ODM\MongoDB\Tools\Console\Helper\DocumentManagerHelper;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
final class CommandHelper
{
    /**
     * configureMigrations.
     *
     * @param ContainerInterface $container
     * @param Configuration      $configuration
     */
    public static function configureMigrations(ContainerInterface $container, Configuration $configuration)
    {
        $dir = $container->getParameter('mongo_db_migrations.dir_name');
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
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
     * @param Application $application
     * @param string      $dmName
     */
    public static function setApplicationDocumentManager(Application $application, $dmName)
    {
        /* @var $dm \Doctrine\ODM\DocumentManager */
        $alias = sprintf(
            'doctrine_mongodb.odm.%s',
            $dmName
        );
        $dm = $application->getKernel()->getContainer()->get($alias);
        $helperSet = $application->getHelperSet();
        $helperSet->set(new DocumentManagerHelper($dm), 'dm');
    }

    /**
     * injectContainerToMigrations.
     *
     * Injects the container to migrations aware of it.
     *
     * @param ContainerInterface $container
     * @param array              $versions
     */
    private static function injectContainerToMigrations(ContainerInterface $container, array $versions)
    {
        foreach ($versions as $version) {
            $migration = $version->getMigration();
            if ($migration instanceof ContainerAwareInterface) {
                $migration->setContainer($container);
            }
        }
    }
}
