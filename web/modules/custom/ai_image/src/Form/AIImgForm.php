<?php

namespace Drupal\ai_image\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a Iiimg form.
 */
class AIImgForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_img_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $img_preview = \Drupal::state()->get('recent_image');
    $recent_prompt = \Drupal::state()->get('recent_prompt');
    if(isset($img_preview)) {
      $form['box'] = array(
        '#type' => 'markup',
        '#prefix' => '<div id="ai-img-preview-box">',
        '#suffix' => '<span>Click to view full-size image</span></div>',
        '#markup' => '<a href="'.$img_preview.'" target="_blank"><img src="'.$img_preview.'" alt="Image preview" title="'.$recent_prompt.'"></a>',
      );
    }

    $form['prompt'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Enter text to generate AI image'),
      '#required' => TRUE,
    ];

    // The image size is restricted for now because of timing issues
    // with the API.
    /*
    $form['image_size'] = [
      '#type' => 'select',
      '#title' => $this->t('Select image resolution'),
      '#options' => [
        '256x256' => $this->t('256x256'),
        '512x512' => $this->t('512x512'),
        '1024x1024' => $this->t('1024x1024'),
      ],
    ];
    */

    // Image size restriction
    $form['image_size'] = [
      '#value' => '768x768',
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Send'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (mb_strlen($form_state->getValue('prompt')) < 10) {
      $form_state->setErrorByName('prompt', $this->t('Prompt should be at least 10 characters.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    //$this->messenger()->addStatus($this->t('The prompt has been sent.'));
    $img_url = \Drupal::service('ai_image.get_image')->getImage($form['prompt']['#value'], $form['image_size']['#value']);
    $form_state->setRedirect('ai_image');
    //$this->messenger()->addStatus($this->t("<a href=".$img_url.">Image URL</a>"));
  }
}
