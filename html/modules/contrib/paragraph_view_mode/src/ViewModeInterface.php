<?php

namespace Drupal\paragraph_view_mode;

/**
 * Provides interfacie for paragraph view modes.
 *
 * @package Drupal\paragraph_view_mode
 */
interface ViewModeInterface {

  /**
   * Default view mode.
   */
  const DEFAULT = 'default';

  /**
   * Special view mode used as a preview.
   */
  const PREVIEW = 'preview';

}
