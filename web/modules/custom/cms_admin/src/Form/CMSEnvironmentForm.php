<?php

namespace Drupal\cms_admin\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\cms_admin\doHelper;

/**
 * Form controller for the CMS environment edit forms.
 *
 * @ingroup cms_admin
 */
class CMSEnvironmentForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $entity = $this->entity;
    if ($entity->isNew()) {
      $form['#title'] = $this->t('Add CMS Environment');
    }
    else {
      $form['#title'] = $this->t('Edit CMS Environment %label', ['%label' => $entity->label()]);
    }

    $form['label']['#description'] = $this->t('Enter a unique name for this CMS Environment.');
    $form['label']['#required'] = TRUE;

    $region = $form_state->getValue('region') ?? 'nyc1';
    $doHelper = new doHelper();
    $sizes = $doHelper->getSizes();
    $options = [];
    foreach ($sizes as $size) {
      if (in_array($region, $size->regions)
        && $size->disk >= 25
        && $size->description === 'Basic'
      ) {
        $options[$size->slug] = $size->vcpus . ' CPU, ' . $size->memory / 1024 . 'GB' . ' ($' . $size->priceMonthly . '/month)';
      }
    }
    $form['server_size'] = [
      '#type' => 'select',
      '#title' => 'Server size',
      '#weight' => 5,
      '#options' => $options,
      '#description' => t('More info about server size you can found <a href="@link" target="_blank">here</a>',
        ['@link' => 'https://slugs.do-api.dev/']),
      '#default_value' => $entity->getServerSize(),
    ];

    if(!$entity->isNew()) {
      // Disable fields.
      $fields = [
        'region',
        'theme',
      ];
      foreach($fields as $field) {
        $form[$field]['#disabled'] = 'disabled';
      }
    }
    else {
      $form['status']['#attributes']['class'][] = 'hidden';
    }

    return $form;
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $fields = [
      'label',
      'domain_name',
      'region',
      'theme',
    ];
    foreach ($fields as $field) {
      $entity->{$field}->value = $form_state->getValue($field)[0]['value'];
    }
    $entity->status->value = $form_state->getValue('status')['value'];
    $entity->server_size->value = $form_state->getValue('server_size');
    if($entity->isNew()) {
      $machine_name = $this->generateMachineName($entity->label());
      $entity->set('machine_name', $machine_name);
    }
    $entity->save();
    $message = $this->t('The CMS Environment %label has been saved.', ['%label' => $entity->label()]);
    $this->messenger()->addStatus($message);
    $form_state->setRedirect('entity.cms_environment.canonical', ['cms_environment' => $entity->id()]);
  }

  /**
   * Callback to generate and validate a unique machine name for the CMSEnvironment entity.
   */
  public function generateMachineName($label, $id = NULL) {
    // Remove any non-alphanumeric characters from the machine name.
    $machine_name = preg_replace('@[^a-z0-9-]+@', '-', strtolower($label));
    $machine_name_with_suffix = $machine_name;
    // Check if the machine name already exists for the current entity.
    $query = \Drupal::entityQuery('cms_environment')
      ->accessCheck(FALSE)
      ->condition('machine_name', $machine_name);
    if ($id) {
      // Exclude the current entity from the query if it already exists.
      $query->condition('id', $id, '<>');
    }
    $existing_entity_ids = $query->execute();

    // If the machine name is not unique, increment a number at the end until a unique name is found.
    $i = 1;
    while (!empty($existing_entity_ids)) {
      $machine_name_with_suffix = $machine_name . '_' . $i;
      $query = \Drupal::entityQuery('cms_environment')
        ->accessCheck(FALSE)
        ->condition('machine_name', $machine_name_with_suffix);
      if ($id) {
        // Exclude the current entity from the query if it already exists.
        $query->condition('id', $id, '<>');
      }
      $existing_entity_ids = $query->execute();
      $i++;
    }

    // Return the unique machine name as the value of the field.
    return $machine_name_with_suffix;
  }

}

