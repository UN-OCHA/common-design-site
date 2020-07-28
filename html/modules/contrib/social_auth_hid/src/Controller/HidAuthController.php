<?php

namespace Drupal\social_auth_hid\Controller;

use Drupal\Core\Messenger\MessengerInterface;
use Drupal\social_api\Plugin\NetworkManager;
use Drupal\social_auth\Controller\OAuth2ControllerBase;
use Drupal\social_auth\SocialAuthDataHandler;
use Drupal\social_auth\User\UserAuthenticator;
use Drupal\social_auth_hid\HidAuthManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Returns responses for Social Auth Hid module routes.
 */
class HidAuthController extends OAuth2ControllerBase {

  /**
   * HidAuthController constructor.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\social_api\Plugin\NetworkManager $network_manager
   *   Used to get an instance of social_auth_google network plugin.
   * @param \Drupal\social_auth\UserAuthenticator $user_authenticator
   *   Manages user login/registration.
   * @param \Drupal\social_auth_hid\HidAuthManager $hid_manager
   *   Used to manage authentication methods.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request
   *   Used to access GET parameters.
   * @param \Drupal\social_auth\SocialAuthDataHandler $data_handler
   *   SocialAuthDataHandler object.
   */
  public function __construct(MessengerInterface $messenger,
    NetworkManager $network_manager,
    UserAuthenticator $user_authenticator,
    HidAuthManager $hid_manager,
    RequestStack $request,
    SocialAuthDataHandler $data_handler) {
    parent::__construct('Social Auth Hid', 'social_auth_hid', $messenger, $network_manager, $user_authenticator, $hid_manager, $request, $data_handler);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('messenger'),
      $container->get('plugin.network.manager'),
      $container->get('social_auth.user_authenticator'),
      $container->get('social_auth_hid.manager'),
      $container->get('request_stack'),
      $container->get('social_auth.data_handler')
    );
  }

  /**
   * Response for path 'user/login/hid/callback'.
   *
   * HID returns the user here after user has authenticated in HID.
   */
  public function callback() {
    // Checks if there was an authentication error.
    $response = $this->checkAuthError();

    // If there are no errors, attempt to authenticate the user.
    if (!$response) {
      /* @var \League\OAuth2\Client\Provider\Hid|null $profile */
      $profile = $this->processCallback();

      // If authentication was successful.
      if ($profile !== NULL) {
        // Gets (or not) extra initial data.
        $data = $this->userAuthenticator->checkProviderIsAssociated($profile->getId()) ? NULL : $this->providerManager->getExtraDetails();

        // If user information could be retrieved.
        $response = $this->userAuthenticator->authenticateUser($profile->getName(), $profile->getEmail(), $profile->getId(), $this->providerManager->getAccessToken(), $profile->getAvatar(), $data);
      }
    }

    // Prevent redirect loop.
    //
    // This happens when `auto_redirect` is checked and a redirection to the
    // user login form is returned by `OAuth2ControllerBase::checkAuthError()`
    // or `userAuthenticator::authenticateUser()`.
    //
    // List of cases where a redirection to the login form occurs:
    //
    // - There is an authentication error on the provider side.
    //
    //   See OAuth2ControllerBase::checkAuthError().
    //   See UserAuthenticator::dispatchAuthenticationError().
    //
    // - The provider could not be associated with the account (error).
    //
    //   See UserAuthenticator::associateNewProvider().
    //
    // - Trying to log in with the user 1 when authenticating with the admin
    //   account is disabled.
    //
    //   See UserAuthenticator::authenticateExistingUser().
    //
    // - Trying to log in with an account that has a role for which logging in
    //   with Social Auth is disabled.
    //
    //   See UserAuthenticator::authenticateExistingUser().
    //
    // - Trying to log in with an account that is blocked (not yet approved
    //   for example).
    //
    //   See UserAuthenticator::authenticateExistingUser().
    //
    // - Logging in as a new user and admin approval is required.
    //
    //   See UserAuthenticator::authenticateNewUser().
    //
    // - Logging in as a new user and registration is disabled.
    //
    //   See UserAuthenticator::authenticateNewUser().
    //
    // @see \Drupal\social_auth\Controller\OAuth2ControllerBase::checkAuthError()
    // @see \Drupal\social_auth\User\UserAuthenticator
    // @see \Drupal\social_auth_hid\Routing\RouteSubscriber::alterRoutes()
    if (!$response || $response->getTargetUrl() === '/user/login/hid') {
      return $this->redirect('<front>');
    }

    return $response;
  }

}
