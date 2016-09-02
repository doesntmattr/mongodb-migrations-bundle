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

use AntiMattr\MongoDB\Migrations\Tools\Console\Command\VersionCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
class MigrationsVersionDoctrineCommand extends VersionCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('mongodb:migrations:version')
            ->addOption('dm', null, InputOption::VALUE_OPTIONAL, 'The document manager to use for this command.', 'default_document_manager')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        CommandHelper::setApplicationDocumentManager($this->getApplication(), $input->getOption('dm'));

        $configuration = $this->getMigrationConfiguration($input, $output);
        CommandHelper::configureMigrations($this->getApplication()->getKernel()->getContainer(), $configuration);

        parent::execute($input, $output);
    }
}
