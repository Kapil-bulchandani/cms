<?php

namespace Drupal\chatgptx\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Serialization\Yaml;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TrainingDataForm extends FormBase {

  public function getFormId() {
    return 'chatgptx_training_data_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Upload or enter your training data below.'),
    ];
    $form['upload'] = [
      '#type' => 'file',
      '#title' => $this->t('Upload a training data file'),
      '#description' => $this->t('Supported file types: yaml, yml, json.'),
    ];
    $form['data'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Enter training data'),
      '#description' => $this->t('Paste or enter your training data below.'),
      '#rows' => 20,
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];
    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $upload = $form_state->getValue('upload');
    $data = $form_state->getValue('data');
    if (empty($upload['size']) && empty(trim($data))) {
      $form_state->setErrorByName('upload', $this->t('Please upload a file or enter data.'));
    }
    else if (!empty($upload['size'])) {
      $file = UploadedFile::createFromPath($upload['tmp_name']);
      $extension = strtolower($file->getClientOriginalExtension());
      if (!in_array($extension, ['yaml', 'yml', 'json'])) {
        $form_state->setErrorByName('upload', $this->t('The uploaded file must be a YAML or JSON file.'));
      }
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $upload = $form_state->getValue('upload');
    $data = $form_state->getValue('data');
    $serializer = \Drupal::service('serializer');
    $storage = \Drupal::service('chatgptx.training_data_storage');
    if (!empty($upload['size'])) {
      $file = UploadedFile::createFromPath($upload['tmp_name']);
      $contents = file_get_contents($file->getPathname());
      $extension = strtolower($file->getClientOriginalExtension());
      if ($extension === 'yaml' || $extension === 'yml') {
        $data = Yaml::decode($contents);
      }
      else if ($extension === 'json') {
        $data = json_decode($contents, TRUE);
      }
    }
    else {
      $data = Yaml::decode($data);
    }
    $storage->setTrainingData($data);
    drupal_set_message($this->t('Training data saved successfully.'));
  }

}
