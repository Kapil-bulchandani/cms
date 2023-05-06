<?php

namespace Drupal\chatgptx\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Controller for chatgptx.
 */
class chatgptxController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function content() {
    return [
      '#markup' => $this->t('chatgptx module'),
    ];
  }

  /**
   * Controller for processing the chatgptx form submission.
   */
  public function processForm(array $form, FormStateInterface $form_state) {
    // Get the submitted values from the form state.
    $dataset = $form_state->getValue('dataset');

    // Process the dataset here.
    // ...

    // Redirect to the same form after processing the dataset.
    $form_state->setRedirect('chatgptx.form');

    // Return a message indicating that the dataset was processed.
    drupal_set_message($this->t('Dataset processed successfully.'));
  }

}
