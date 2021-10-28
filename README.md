# Common Design Demo | [![Build Status](https://travis-ci.com/UN-OCHA/common-design-site.svg?branch=master)](https://travis-ci.com/UN-OCHA/common-design-site)

This is the Common Design Drupal 8 site. The code for this project is managed with composer.

To install security updates for Drupal, run `composer update drupal/core drupal/core-dev --with-dependencies`.

## Installation

- Checkout the repository.
- Run `composer install`. This will download Drupal core, contributed modules, contributed themes and libraries.
- Deploy the html and vendor directories to the web server(s).

### Initialisation

Use drush to bootstrap your site and import the initial configuration from the config subdirectory.

```bash
drush -y si --admin-password="your admin password" --db-url=mysql://drupal:drupal@127.0.0.1/drupal minimal
drush -y cset system.site uuid $(grep uuid config/system.site.yml | awk '{print $2}')
drush -y cim --source=config
drush php-eval 'node_access_rebuild();'
drush cr
```

For local development, add this line to settings.local.php:
`$config['config_split.config_split.config_dev']['status'] = TRUE;`
After importing a fresh database, run `drush cim` to enable devel, database log
and stage_file_proxy.

### Remote

Automatic testing is run via Travis CI. Code syntax, style and quality are inspected.

With a small alteration, these tests could run via GitHub Actions.

### Locally

`fin exec "vendor/bin/phpunit --debug --colors --printer '\\Drupal\\Tests\\Listeners\\HtmlOutputPrinter'"`

## Config

```php
// Docksal DB connection settings.
$databases['default']['default'] = array (
  'database' => 'default',
  'username' => 'user',
  'password' => 'user',
  'host' => 'db',
  'driver' => 'mysql',
);

$databases['migrate']['default'] = array (
  'database' => 'd7',
  'username' => 'user',
  'password' => 'user',
  'host' => 'db',
  'driver' => 'mysql',
);
```

### Update core

```bash
fin composer outdated drupal/core
```

```bash
fin composer update drupal/core --with-dependencies
fin drush updb -y && fin drush cr
```

### Update contrib

```bash
fin composer outdated drupal/*
```

```bash
fin composer update drupal/* --with-dependencies
fin drush updb -y && fin drush cr
```
