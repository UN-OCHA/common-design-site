<?php

namespace Drupal\azure_tweaks\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Url;

/**
 * Returns responses for Social Auth Hid module routes.
 */
class AuthController extends ControllerBase {

  public function redirectRegister() {
    $url = $this->config('azure_tweaks.settings')->get('register_url');
    $client_id = $this->config('openid_connect.client.uniteid')->get('settings.client_id');
    $redirect = Url::fromRoute('<front>')->setAbsolute()->toString();
    $redirect .= 'openid-connect/uniteid';

    $url .= '&client_id=' . $client_id;
    $url .= '&redirect_uri=' . $redirect;

    /** @var \Drupal\Core\Routing\TrustedRedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse $response */
    $response = new TrustedRedirectResponse($url);

    return $response->send();
  }

  public function redirectResetPassword() {
    $url = $this->config('azure_tweaks.settings')->get('password_url');
    $client_id = $this->config('openid_connect.client.uniteid')->get('settings.client_id');
    $redirect = Url::fromRoute('<front>')->setAbsolute()->toString();
    $redirect .= 'openid-connect/uniteid';

    $url .= '&client_id=' . $client_id;
    $url .= '&redirect_uri=' . $redirect;

    /** @var \Drupal\Core\Routing\TrustedRedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse $response */
    $response = new TrustedRedirectResponse($url);

    return $response->send();
  }

}
