:warning: Forked from [antimattr/mongodb-migrations-bundle](https://github.com/antimattr/mongodb-migrations-bundle) for contributors as the original project isn't being maintained. See [issue 16](https://github.com/antimattr/mongodb-migrations/issues/16)

The original authors did an awesome job of making a library that has been really
really useful AND stable.  Thank you @rcatlin and @matthewfitz !

MongoDBMigrationsBundle
========================

This bundle integrates the [AntiMattr MongoDB Migrations library](https://github.com/doesntmattr/mongodb-migrations).
into Symfony so that you can safely and quickly manage MongoDB migrations.

Installation
============

Add the following to your composer.json file:

```json
{
    "require": {
        "doesntmattr/mongodb-migrations": "~1.0@stable",
        "doesntmattr/mongodb-migrations-bundle": "~1.0@stable"
    }
}
```

Install the libraries by running:

```bash
composer install
```

If everything worked, the MonogDBMigrationsBundle can now be found at vendor/doesntmattr/mongodb-migrations-bundle.

Finally, be sure to enable the bundle in AppKernel.php by including the following:

```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
        //...
        new AntiMattr\Bundle\MongoDBMigrationsBundle\MongoDBMigrationsBundle(),
    );
}
```

Configuration
=============

```yaml
mongo_db_migrations:
    collection_name: "migration_versions"
    database_name: "opensky_devo"
    dir_name: "%kernel.root_dir%/../src/OpenSky/Bundle/MainBundle/Migrations/MongoDB"
    script_dir_name: "%kernel.root_dir%/scripts"
    name: "OpenSky DEVO MongoDB Migrations"
    namespace: "OpenSky\Bundle\MainBundle\Migrations\MongoDB"
```

Container Aware Migrations
==========================

In some cases you might need access to the container to ensure the proper update of your data structure. This could be necessary to update relations with some specific logic or to create new entities.

Therefore you can just implement the ContainerAwareInterface with its needed methods to get full access to the container.

```php
// ...
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Version20130326212938 extends AbstractMigration implements ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function up(Database $db)
    {
        // ... migration content
    }

    public function postUp(Database $db)
    {
        $dm = $this->container->get('doctrine.odm.default_document_manager');
        // ... update the entities
    }
}
```

MongoDB Cursor Timeouts
=======================

In some cases you may need the Cursor timeout to be extended. You can of course do this on a per migration basis, or you can do this for all migrations by extending the base migration and adding to the constructor.

```php
// ...
use AntiMattr\MongoDB\Migrations\AbstractMigration as BaseMigration;
use AntiMattr\MongoDB\Migrations\Version;
use MongoCursor;

abstract class AbstractMigration extends BaseMigration
{
    /**
     * @var AntiMattr\MongoDB\Migrations\Version
     */
    public function __construct(Version $version)
    {
        parent::__construct($version);
        MongoCursor::$timeout = -1;
    }
}
```

Features
========

For a full list of available features, see the README.md in the underlying library

https://github.com/doesntmattr/mongodb-migrations/blob/master/README.md

Differences from the underlying library are limited to the Console commands, namely database configurations are handled by Symfony's Dependency injection container, so you don't pass them as command line args.

Examples of the Command Line args with the difference below:

Features - Generate a New Migration
-----------------------------------

```bash
> ./console mongodb:migrations:generate
Generated new migration class to "Example/Migrations/TestAntiMattr/MongoDB/Version20140822185742.php"
```

Features - Status of Migrations
-------------------------------

```bash
> ./console mongodb:migrations:status

 == Configuration

    >> Name:                                AntiMattr Example Migrations
    >> Database Driver:                     MongoDB
    >> Database Name:                       test_antimattr_migrations
    >> Configuration Source:                demo/ConsoleApplication/config/test_antimattr_mongodb.yml
    >> Version Collection Name:             migration_versions
    >> Migrations Namespace:                Example\Migrations\TestAntiMattr\MongoDB
    >> Migrations Directory:                Example/Migrations/TestAntiMattr/MongoDB
    >> Current Version:                     0
    >> Latest Version:                      2014-08-22 18:57:44 (20140822185744)
    >> Executed Migrations:                 0
    >> Executed Unavailable Migrations:     0
    >> Available Migrations:                3
    >> New Migrations:                      3
```

Features - Migrate all Migrations
---------------------------------

This is what you will execute during your deployment process.

```bash
./console mongodb:migrations:migrate
                                                                    
                    AntiMattr Example Migrations                    
                                                                    

WARNING! You are about to execute a database migration that could result in data lost. Are you sure you wish to continue? (y/n)y
Migrating up to 20140822185744 from 0

  ++ migrating 20140822185742


     Collection test_a

     metric           before               after                difference           
     ================================================================================
     count            100                  100                  0                   
     size             20452                20452                0                   
     avgObjSize       204.52               204.52               0                   
     storageSize      61440                61440                0                   
     numExtents       2                    2                    0                   
     nindexes         1                    2                    1                   
     lastExtentSize   49152                49152                0                   
     paddingFactor    1                    1                    0                   
     totalIndexSize   8176                 16352                8176                

  ++ migrated (0.03s)

  ++ migrating 20140822185743


  ++ migrated (0s)

  ++ migrating 20140822185744


  ++ migrated (0s)

  ------------------------

  ++ finished in 0.03
  ++ 3 migrations executed
```

Features - Execute a Single Migration
-------------------------------------

```bash
./console mongodb:migrations:execute 20140822185742
WARNING! You are about to execute a database migration that could result in data lost. Are you sure you wish to continue? (y/n)y

  ++ migrating 20140822185742


     Collection test_a

     metric           before               after                difference           
     ================================================================================
     count            100                  100                  0                   
     size             20620                20620                0                   
     avgObjSize       206.2                206.2                0                   
     storageSize      61440                61440                0                   
     numExtents       2                    2                    0                   
     nindexes         1                    2                    1                   
     lastExtentSize   49152                49152                0                   
     paddingFactor    1                    1                    0                   
     totalIndexSize   8176                 16352                8176                

  ++ migrated (0.02s)
```

Features - Version Up or Down
-----------------------------

Is your migration history out of sync for some reason? You can manually add or remove a record from the history without running the underlying migration.

You can delete

```bash
./console mongodb:migrations:version --delete 20140822185744
```

You can add

```bash
./console mongodb:migrations:version --add 20140822185744
```
