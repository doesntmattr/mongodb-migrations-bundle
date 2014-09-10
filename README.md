MongoDBMigrationsBundle
========================

This bundle integrates the [AntiMattr MongoDB Migrations library](https://github.com/antimattr/mongodb-migrations).
into Symfony so that you can safely and quickly manage MongoDB migrations.

Installation
============

Add the following to your composer.json file:

```json
{
    "require": {
        "antimattr/mongodb-migrations": "dev-master",
        "antimattr/mongodb-migrations-bundle": "dev-master"
    }
}
```

Install the libraries by running:

```bash
composer install
```

If everything worked, the MonogDBMigrationsBundle can now be found at vendor/antimattr/mongodb-migrations-bundle.

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
        $em = $this->container->get('doctrine.odm.default_document_manager');
        // ... update the entities
    }
}
```
