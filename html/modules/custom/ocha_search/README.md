OCHA search module
============================

This modules provides basic configuration for a dedicated search page for
results from Google Custom Search Engine searches.

It adds a page at `/results` and requires a GCSE ID, which can be configured at
https://programmablesearchengine.google.com/controlpanel/all
@TODO: check if results is the right url. It was chosen as using `/search`
may require disabling other modules or views.

Many of the configuration options are already chosen in the xml files in the
gcse_config directory. These can be uploaded to a custom search engine via the
advanced tab in the setup.

For context-cse.yml - change SITE NAME and site_name accordingly.
For annotations.yml - add a refinement with only urls from the current site.

All the color preferences and some other styling choices are included in a
css file in the theme, so configuration of those can be ignored.
