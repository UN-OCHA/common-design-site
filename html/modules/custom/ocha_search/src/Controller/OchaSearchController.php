<?php

namespace Drupal\ocha_search\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route responses for the Ocha Search module.
 */
class OchaSearchController extends ControllerBase {

  /**
   * Returns a simple page.
   *
   * @return array
   *   A renderable array.
   */
  public function search() {
    return [
      '#gcse_id' => $this->config('ocha_search.settings')->get('gcse_id'),
      '#search_text' => $this->config('ocha_search.settings')->get('search_text'),
      '#default_refinement' => $this->config('ocha_search.settings')->get('default_refinement'),
      '#theme' => 'ocha_search_results_page',
    ];
  }

}
