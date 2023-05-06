<?php

namespace Drupal\chatgptx\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;

class CustomController extends ControllerBase {

  public function importDataForm() {
    $form['data'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Data'),
      '#description' => $this->t('Paste your data here.'),
      '#required' => TRUE,
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Import'),
    ];

    return $form;
  }

  public function importDataFormSubmit(array &$form, FormStateInterface $form_state) {
    $data = $form_state->getValue('data');
    $rows = explode("\n", $data);

    foreach ($rows as $row) {
      $fields = explode(",", $row);
      $node = Node::create([
        'type' => 'article',
        'title' => $fields[0],
        'body' => [
          'value' => $fields[1],
          'format' => 'full_html',
        ],
      ]);
      $node->save();
    }

    drupal_set_message($this->t('Data imported successfully.'));
  }

}
