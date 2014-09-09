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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\DoctrineCommandHelper;
use Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
class MigrationsMigrateDoctrineCommand extends MigrateCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('mongodb:migrations:migrate')        
            ->addOption('dm', null, InputOption::VALUE_OPTIONAL, 'The document manager to use for this command.')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        DoctrineCommandHelper::setApplicationEntityManager($this->getApplication(), $input->getOption('dm'));

        $configuration = $this->getMigrationConfiguration($input, $output);
        AntiMattrCommand::configureMigrations($this->getApplication()->getKernel()->getContainer(), $configuration);

        parent::execute($input, $output);
    }
}
