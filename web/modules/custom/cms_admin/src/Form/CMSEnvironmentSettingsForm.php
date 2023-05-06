<?php

namespace Drupal\cms_admin\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides a form for editing CMS Environment settings.
 */
class CMSEnvironmentSettingsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cms_environment_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('cms_admin.settings');

    $form['do'] = [
      '#type' => 'details',
      '#title' => 'DigitalOcean',
      '#open' => TRUE,
    ];

    $form['do']['secret_key'] = [
      '#type' => 'key_select',
      '#title' => $this->t('DigitalOcean secret key'),
      '#default_value' => $config->get('secret_key'),
    ];

    $form['github'] = [
      '#type' => 'details',
      '#title' => 'GitHub',
      '#open' => TRUE,
    ];

    $form['github']['github_key'] = [
      '#type' => 'key_select',
      '#title' => $this->t('GitHub secret key'),
      '#default_value' => $config->get('github_key'),
    ];

    $form['github']['github_owner'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Initial repo owner'),
      '#default_value' => $config->get('github_owner'),
    ];

    $form['github']['github_repo'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Initial repo'),
      '#default_value' => $config->get('github_repo'),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save settings'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->configFactory()->getEditable('cms_admin.settings');

    $config->set('secret_key', $form_state->getValue('secret_key'));
    $config->set('github_key', $form_state->getValue('github_key'));
    $config->set('github_owner', $form_state->getValue('github_owner'));
    $config->set('github_repo', $form_state->getValue('github_repo'));
    $config->save();

    \Drupal::messenger()
      ->addStatus($this->t('The CMS Admin settings have been saved.'));
    $form_state->setRedirectUrl(Url::fromRoute('cms_admin.settings'));
  }

}
