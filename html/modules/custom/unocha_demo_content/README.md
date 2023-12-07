# UNOCHA Demo Content

For the moment all 39 nodes including files are in yaml, so if you do `drush si --existing-config && drush en ocha_demo_content` you'll get a copy of the production site.

To re-create or add default content the easiest is to add the uuid inside `unocha_demo_content.info.yml` and run `drush dcem unocha_demo_content`

To get the uuid you can use

```bash
drush sqlq "select * from node"
drush sqlq "select * from media"
drush sqlq "select * from file_managed"
drush sqlq "select * from taxonomy_term_data"
drush sqlq "select * from block_content"
drush sqlq "select * from menu_link_content"
```
 See https://www.drupal.org/docs/contributed-modules/default-content-for-d8/defining-default-content
