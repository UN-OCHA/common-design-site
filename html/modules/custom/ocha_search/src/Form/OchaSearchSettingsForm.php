<?php

namespace Drupal\ocha_search\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure search settings for this site.
 */
class OchaSearchSettingsForm extends ConfigFormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'ocha_search.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ocha_search_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);

    $form['search_text'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Search page text'),
      '#default_value' => $config->get('search_text'),
    ];

    $form['gcse_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('GCSE ID'),
      '#default_value' => $config->get('gcse_id'),
    ];

    $form['default_refinement'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Default Refinement'),
      '#default_value' => $config->get('default_refinement'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config(static::SETTINGS)
      ->set('default_refinement', $form_state->getValue('default_refinement'))
      ->set('search_text', $form_state->getValue('search_text'))
      ->set('gcse_id', $form_state->getValue('gcse_id'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
