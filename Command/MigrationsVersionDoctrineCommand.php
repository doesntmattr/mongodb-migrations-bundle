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

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 *
 * @deprecated will be removed in version 2.0
 */
class MigrationsVersionDoctrineCommand extends MigrationsVersionCommand
{
    protected function configure()
    {
        parent::configure();

        $replacement = str_replace('Doctrine', '', __CLASS__);
        $notice = sprintf(
            '%s is deprecated and will be removed in 2.0. Use %s instead',
            __CLASS__,
            $replacement
        );

        @trigger_error($notice, E_USER_DEPRECATED);
    }
}
