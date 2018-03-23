<?php

namespace AntiMattr\Bundle\MongoDBMigrationsBundle\Command;

use PHPUnit\Framework\TestCase;

/**
 * @author Douglas Reith <douglas@reith.com.au>
 */
class MigrationsExecuteCommandTest extends TestCase
{
    public static function testCommandIsEnabled()
    {
        $command = new MigrationsExecuteCommand();
        self::assertTrue($command->isEnabled());
    }
}
