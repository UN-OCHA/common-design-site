<?php

namespace Drupal\paragraph_view_mode\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\StringTextfieldWidget;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\paragraph_view_mode\StorageManagerInterface;
use Drupal\paragraph_view_mode\ViewModeInterface;
use Drupal\paragraphs\Entity\ParagraphsType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Plugin implementation of the 'paragraph_view_mode' widget.
 *
 * @FieldWidget(
 *   id = "paragraph_view_mode",
 *   label = @Translation("Paragraph view mode"),
 *   field_types = {
 *     "paragraph_view_mode",
 *   }
 * )
 */
class ParagraphViewModeWidget extends StringTextfieldWidget {

  /**
   * Current request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->request = $container->get('request_stack')->getCurrentRequest();

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'view_modes' => self::getAvailableViewModes(),
      'default_view_mode' => ViewModeInterface::DEFAULT,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {

    $element['view_modes'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Available view modes'),
      '#description' => $this->getViewModesFieldDescription(),
      '#options' => $this->defaultSettings()['view_modes'],
      '#default_value' => array_keys($this->getEnabledViewModes()),
      '#required' => FALSE,
      '#ajax' => [
        'callback' => [get_called_class(), 'defaultViewModes'],
        'event' => 'change',
        'wrapper' => 'paragraph-view-mode-default',
      ],
    ];

    if ($this->getSetting('view_modes')) {
      $element['default_view_mode'] = [
        '#type' => 'select',
        '#title' => $this->t('Default value'),
        '#description' => $this->t('View mode to be used as a default field value.'),
        '#options' => $element['view_modes']['#options'],
        '#default_value' => $this->getSetting('default_view_mode'),
        '#required' => FALSE,
        '#weight' => 2,
        '#prefix' => '<div id="paragraph-view-mode-default">',
        '#suffix' => '</div>',
      ];
    }

    return $element;
  }

  /**
   * Ajax callback for updating the default view mode options.
   *
   * @param array $form
   *   Form structure.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state.
   *
   * @return array
   *   Default view mode form element.
   */
  public function defaultViewModes(array $form, FormStateInterface $form_state) {
    $checkboxes = $form_state->getTriggeringElement();
    $element = NestedArray::getValue($form, array_slice($checkboxes['#array_parents'], 0, count($checkboxes['#array_parents']) - 2));

    $options = array_intersect_key($element['view_modes']['#options'], $element['view_modes']['#value']);

    $element['default_view_mode']['#options'] = empty($options) ? $element['view_modes']['#options'] : $options;

    return $element['default_view_mode'];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $settings = $this->getEnabledViewModes();

    if (empty($settings)) {
      $message = $this->t('No view modes enabled, "@default" view mode will be used instead.', [
        '@default' => ViewModeInterface::DEFAULT,
      ]);
    }
    else {
      $message = $this->t('Available view modes: @types', ['@types' => implode(', ', $settings)]);
    }

    $summary[] = $message;

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $element['value'] = [
      '#title' => $this->t('View mode'),
      '#type' => 'select',
      '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : $this->getSetting('default_view_mode'),
      '#options' => $this->getEnabledViewModes() ?: $this->getDefaultOption(),
      '#required' => TRUE,
      '#weight' => 1,
    ];

    return $element;
  }

  /**
   * Getter for available view modes in paragraph entity type.
   *
   * @return array
   *   Associative array of view mode machine names and labels.
   */
  protected static function getAvailableViewModes() {
    $request = \Drupal::request();
    $entity_display_respository = \Drupal::service('entity_display.repository');
    $paragraph_type = self::getParagraphsTypeFromRequest($request);

    $entity_id = StorageManagerInterface::ENTITY_TYPE;

    if ($paragraph_type instanceof ParagraphsType) {
      return $entity_display_respository->getViewModeOptionsByBundle($entity_id, $paragraph_type->id());
    }

    return $entity_display_respository->getViewModeOptions($entity_id);
  }

  /**
   * Getter for enabled view modes.
   *
   * @return array
   *   Associative array of view mode machine names and labels.
   */
  protected function getEnabledViewModes() {
    $availableViewModes = self::getAvailableViewModes();
    $currentViewModes = array_filter($this->getSetting('view_modes'));

    return array_intersect_key($availableViewModes, $currentViewModes);
  }

  /**
   * Provides default option for the form elements.
   *
   * @return array
   *   Default view mode option.
   */
  protected function getDefaultOption() {
    return [ViewModeInterface::DEFAULT => $this->t('Default')];
  }

  /**
   * Get ParagraphsType entity object from request.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Current request object.
   *
   * @return \Drupal\paragraphs\Entity\ParagraphsType|null
   *   ParagraphsType entity.
   */
  protected static function getParagraphsTypeFromRequest(Request $request): ?ParagraphsType {
    return $request->attributes->get('paragraphs_type', NULL);
  }

  /**
   * Getter for 'view modes' field description.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   Field description.
   */
  protected function getViewModesFieldDescription(): TranslatableMarkup {
    $paragraphs_type = self::getParagraphsTypeFromRequest($this->request);

    $url_route = implode('.', [
      'entity.entity_view_display',
      StorageManagerInterface::ENTITY_TYPE,
      ViewModeInterface::DEFAULT,
    ]);
    $url_paramters = ['paragraphs_type' => $paragraphs_type->id()];
    $url_options = ['fragment' => 'edit-modes'];

    $url = Url::fromRoute($url_route, $url_paramters, $url_options);

    return $this->t('It is using only the view modes enabled in the <strong>CUSTOM DISPLAY SETTINGS</strong> section under the <a href="@url">Manage Display</a> tab.', ['@url' => $url->toString()]);
  }

}
