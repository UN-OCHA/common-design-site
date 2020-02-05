<?php

namespace Drupal\social_auth_hid\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Path\PathValidatorInterface;
use Drupal\Core\Routing\RequestContext;
use Drupal\Core\Routing\RouteProviderInterface;
use Drupal\social_auth\Form\SocialAuthSettingsForm;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Settings form for Social Auth Humanitarian ID.
 */
class HidAuthSettingsForm extends SocialAuthSettingsForm {

  /**
   * The request context.
   *
   * @var \Drupal\Core\Routing\RequestContext
   */
  protected $requestContext;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   * @param \Drupal\Core\Routing\RouteProviderInterface $route_provider
   *   Used to check if route exists.
   * @param \Drupal\Core\Path\PathValidatorInterface $path_validator
   *   Used to check if path is valid and exists.
   * @param \Drupal\Core\Routing\RequestContext $request_context
   *   Holds information about the current request.
   */
  public function __construct(ConfigFactoryInterface $config_factory, RouteProviderInterface $route_provider, PathValidatorInterface $path_validator, RequestContext $request_context) {
    parent::__construct($config_factory, $route_provider, $path_validator);
    $this->requestContext = $request_context;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this class.
    return new static(
    // Load the services required to construct this class.
      $container->get('config.factory'),
      $container->get('router.route_provider'),
      $container->get('path.validator'),
      $container->get('router.request_context')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'social_auth_hid_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return array_merge(
      parent::getEditableConfigNames(),
      ['social_auth_hid.settings']
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('social_auth_hid.settings');

    $form['hid_settings'] = [
      '#type' => 'details',
      '#title' => $this->t('Humanitarian ID Client settings'),
      '#open' => TRUE,
      '#description' => $this->t('You need to first request the creation of an HID OAuth application by emailing <a href="mailto:@hid-info">@hid-info</a>', ['@hid-info' => 'info@humanitarian.id']),
    ];

    $form['hid_settings']['client_id'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Client ID'),
      '#default_value' => $config->get('client_id'),
      '#description' => $this->t('Copy the Client ID here.'),
    ];

    $form['hid_settings']['client_secret'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Client Secret'),
      '#default_value' => $config->get('client_secret'),
      '#description' => $this->t('Copy the Client Secret here.'),
    ];

    $form['hid_settings']['advanced'] = [
      '#type' => 'details',
      '#title' => $this->t('Advanced settings'),
      '#open' => FALSE,
    ];

    $form['hid_settings']['advanced']['base_url'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('HID base URL'),
      '#description' => $this->t('Customize your HID base URL, e.g. if you want to use HID dev or HID staging.'),
      '#default_value' => $config->get('base_url'),
    ];


    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    parent::validateForm($form, $form_state);
    if (!UrlHelper::isValid($values['base_url'], TRUE)) {
      $form_state->setErrorByName('base_url', $this->t("The HID base URL is invalid."));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->config('social_auth_hid.settings')
      ->set('client_id', trim($values['client_id']))
      ->set('client_secret', trim($values['client_secret']))
      ->set('base_url', rtrim($values['base_url'], '/'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
