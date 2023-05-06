<?php

namespace Drupal\chatgptx\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a settings form for the My Module module.
 */
class chatgptxForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'chatgptx_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['chatgptx.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('chatgptx.settings');

    $form['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('OpenAI API Key'),
      '#default_value' => $config->get('api_key'),
      '#required' => TRUE,
    ];

    $form['org_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Organization ID'),
      '#default_value' => $config->get('org_id'),
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('chatgptx.settings')
      ->set('api_key', $form_state->getValue('api_key'))
      ->set('org_id', $form_state->getValue('org_id'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
