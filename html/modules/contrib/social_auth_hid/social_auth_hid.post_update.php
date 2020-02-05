<?php

/**
 * @file
 * Post update functions for the Social Auth HID module.
 */

/**
 * Adds HID base URL setting.
 */
function social_auth_hid_add_url_setting() {
  $config = \Drupal::configFactory()
    ->getEditable('social_auth_hid.settings');

  $config->set('base_url', 'https://auth.humanitarian.id')
    ->save(TRUE);
}
