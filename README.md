[![MIT license](http://img.shields.io/badge/license-MIT-brightgreen.svg)](http://opensource.org/licenses/MIT)
[![Build Status](https://travis-ci.org/doesntmattr/mongodb-migrations-bundle.svg?branch=master)](https://travis-ci.org/doesntmattr/mongodb-migrations-bundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/doesntmattr/mongodb-migrations-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/doesntmattr/mongodb-migrations-bundle/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/doesntmattr/mongodb-migrations-bundle/v/stable)](https://packagist.org/packages/doesntmattr/mongodb-migrations-bundle)
[![Total Downloads](https://poser.pugx.org/doesntmattr/mongodb-migrations-bundle/downloads)](https://packagist.org/packages/doesntmattr/mongodb-migrations-bundle)

# MongoDB Migrations Bundle

This bundle integrates the [MongoDB Migrations](https://github.com/doesntmattr/mongodb-migrations) library into Symfony to get you set up more quickly.

It was moved to the doesntmattr organisation from [antimattr/mongodb-migrations-bundle](https://github.com/antimattr/mongodb-migrations-bundle) to continue maintenance (See [issue 16](https://github.com/antimattr/mongodb-migrations/issues/16)).

The original authors are @rcatlin and @matthewfitz

## PHP Version Support

If you require php 5.6 support use version `^1.0`. Version `^3.0` requires at least php 7.1. The `1.x` releases will only receive bug fixes.

## Installation

Install with composer:

```bash
# For php 5.6
composer require "doesntmattr/mongodb-migrations-bundle=^1.0"

# For php 7.1
composer require "doesntmattr/mongodb-migrations-bundle=^3.0"
```

then enable the bundle in `AppKernel.php` by including the following:

```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = [
        //...
        new AntiMattr\Bundle\MongoDBMigrationsBundle\MongoDBMigrationsBundle(),
    ];
}
```

## Configuration

Add following configuration lines to `config.yml` file.

```yaml
# app/config/config.yml
mongo_db_migrations:
    collection_name: "migration_versions"
    database_name: "opensky_devo"
    dir_name: "%kernel.root_dir%/../src/OpenSky/Bundle/MainBundle/Migrations/MongoDB"
    script_dir_name: "%kernel.root_dir%/scripts"
    name: "OpenSky DEVO MongoDB Migrations"
    namespace: "OpenSky\\Bundle\\MainBundle\\MigrationsMongoDB"
```

## Container Aware Migrations

In some cases you might want to access some services you have defined in the container. For example you may want to use a Factory to create new entities in the structure you need.

To get access to the container simply implement the ContainerAwareInterface including the required method `setContainer()`:

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

## MongoDB Cursor Timeouts


In some cases you may need the Cursor timeout to be extended. If so, add the MongoDB option `['socketTimeoutMs' => -1]` to your update method. 


## Features

For a full list of available features, see the README.md in the MongoDB Migrations library:

https://github.com/doesntmattr/mongodb-migrations/blob/master/README.md

Differences from the underlying library are limited to the Console commands, namely database configurations are handled by Symfony's Dependency injection container, so you don't pass them as command line args.

Examples of the Command Line args with the difference below:

### Generate a New Migration


```bash
> ./console mongodb:migrations:generate
Generated new migration class to "Example/Migrations/TestAntiMattr/MongoDB/Version20140822185742.php"
```

### Status of Migrations

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

### Migrate all Migrations

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

### Execute a Single Migration

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

Use `--replay` if you need to re-run an executed migration.


### Version Up or Down

Is your migration history out of sync for some reason? You can manually add or remove a record from the history without running the underlying migration.

You can delete

```bash
./console mongodb:migrations:version --delete 20140822185744
```

You can add

```bash
./console mongodb:migrations:version --add 20140822185744
```
