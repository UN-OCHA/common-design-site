# OCHA GTM

Originally forked from [GTM Barebones][gtm-barebones] with modifications to defer the scripts. This was done after [discussion with the project maintainer][gtm-defer-issue] since the option couldn't really be added without complicating the module beyond the maintainer's desire.

  [gtm-barebones]: https://www.drupal.org/project/gtm_barebones
  [gtm-defer-issue]: https://www.drupal.org/project/gtm_barebones/issues/3415279#comment-15404703

## Usage

This module does not expose its configuration to the admin UI. You can choose to export and modify config, or utilise config overrides in `settings.php`:

```php
$config['ocha_gtm.settings']['container_id'] = 'GTM-ABCDEFGHIJK';
$config['ocha_gtm.settings']['environment_id'] = 'env-123456';
$config['ocha_gtm.settings']['environment_token'] = 'iBN8NANliiuqnAAi81LapqkkdUIjak';
```
