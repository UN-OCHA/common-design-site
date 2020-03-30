<?php

namespace Drupal\social_auth_hid\Routing;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * The config.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   Used for accessing configuration object factory.
   */
  public function __construct(ConfigFactoryInterface $configFactory) {
    $this->config = $configFactory->get('social_auth_hid.settings');
  }

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    // Redirect login.
    if ($this->config->get('auto_redirect')) {
      if ($route = $collection->get('user.login')) {
        $route->setPath('/user/login/hid');
      }
    }

    // Deny access.
    if ($this->config->get('disable_default')) {
      if ($route = $collection->get('user.login.http')) {
        $route->setRequirement('_access', 'FALSE');
      }
      if ($route = $collection->get('user.pass')) {
        $route->setRequirement('_access', 'FALSE');
      }
      if ($route = $collection->get('user.pass.http')) {
        $route->setRequirement('_access', 'FALSE');
      }
      if ($route = $collection->get('user.register')) {
        $route->setRequirement('_access', 'FALSE');
      }
    }
  }

}
