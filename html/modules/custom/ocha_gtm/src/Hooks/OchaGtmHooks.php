<?php

declare(strict_types = 1);

namespace Drupal\ocha_gtm\Hooks;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Routing\AdminContext;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Hooks.
 */
final class OchaGtmHooks implements ContainerInjectionInterface {

  /**
   * Constructor.
   */
  private function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    protected AdminContext $adminContext,
  ) {
  }

  /**
   * {@inheritdoc}
   */
  final public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('config.factory'),
      $container->get('router.admin_context'),
    );
  }

  /**
   * Implements hook_page_attachments().
   *
   * @see https://developers.google.com/tag-manager/quickstart
   * @see ocha_gtm_page_attachments()
   */
  public function pageAttachments(array &$attachments): void {
    if (TRUE === $this->isExcluding()) {
      return;
    }

    $settings = $this->configFactory->get('ocha_gtm.settings');
    $containerId = $settings->get('container_id');
    if (NULL === $containerId) {
      return;
    }

    $environmentId = $settings->get('environment_id');
    $environmentToken = $settings->get('environment_token');
    $attachments['#attached']['html_head'][] = [
      [
        '#type' => 'html_tag',
        '#weight' => -50,
        '#tag' => 'script',
        '#value' => Markup::create(<<<JS
          (function(w,d,s,l,i1,i2,i3){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='//www.googletagmanager.com/gtm.js?id='+i1+dl+'&gtm_auth='+i2+'&gtm_preview='+i3+'&gtm_cookies_win=x';var n=d.querySelector('[nonce]');n&&j.setAttribute('nonce',n.nonce||n.getAttribute('nonce'));f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','$containerId','$environmentToken','$environmentId');
          JS),
      ],
      'ocha_gtm_gtm_tag',
    ];

    // Cacheability as exclusions vary.
    (new CacheableMetadata())
      ->addCacheContexts(['route'])
      ->applyTo($attachments);
  }

  /**
   * Implements hook_page_top().
   *
   * @see ocha_gtm_page_top()
   */
  public function pageTop(array &$page_top): void {
    if (TRUE === $this->isExcluding()) {
      return;
    }

    $settings = $this->configFactory->get('ocha_gtm.settings');
    $containerId = $settings->get('container_id');
    if (NULL === $containerId) {
      return;
    }

    $environmentId = $settings->get('environment_id');
    $environmentToken = $settings->get('environment_token');

    $page_top['ocha_gtm_gtm_noscript_tag'] = [
      '#type' => 'inline_template',
      '#template' => <<<TEMPLATE
        <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id={{ containerId }}&gtm_auth={{ environmentToken }}&gtm_preview={{ environmentId }}&gtm_cookies_win=x" height="0" width="0" style="display:none;visibility:hidden"></iframe>
        </noscript>
        TEMPLATE,
      '#context' => [
        'containerId' => $containerId,
        'environmentToken' => $environmentToken,
        'environmentId' => $environmentId,
      ],
    ];
  }

  private function isExcluding(): bool {
    return $this->adminContext->isAdminRoute() === TRUE;
  }

}
