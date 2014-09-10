# DO NOT USE - In Development

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
