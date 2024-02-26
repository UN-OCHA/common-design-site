# Common Design Demo

This is the Common Design Drupal 10 site. The code for this project is managed with composer.

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

Automatic testing is run via GitHub Actions.
Code syntax, style and quality are inspected by default, based on [drupal-starterkit run-tests.yml](https://github.com/UN-OCHA/drupal-starterkit/blob/develop/.github/workflows/run-tests.yml)

To run Lighthouse tests, add the label "performance" to the PR.
To run end-to-end test with Jest, add the label "e2e" to the PR.

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

### Set up with local stack
See [/local/README](https://github.com/UN-OCHA/common-design-site/tree/develop/local)

##  Performance or e2e tests with Github Actions
Optional test steps - see `run-tests.yml`

Custom Github labels need to be applied to the PR for the above test steps to run: `performance` or `e2e` depending.

### Install demo content

Enables unocha_demo_content module and imports nodes, menu items etc from json, set the frontpage

### Set up node

Github action to install node

### Install node dependencies

In sub theme, install packages including Jest for e2e

### Run e2e tests

Runs Jest e2e tests in sub theme using as ci at http://127.0.0.1:8080

### Audit URLs using Lighthouse

Runs multiple times on 2 URLS (http://127.0.0.1:8080 and http://127.0.0.1:8080/demo) against a performance budget and provides temporary links to Lighthouse reports.
