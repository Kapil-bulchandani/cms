<?php

namespace Drupal\chatgptx\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use  Drupal\chatgptx\chatgptxService;

/**
 * Provides a settings form for the chatgptx module.
 */
class chatgptxDatasetForm extends FormBase {

  protected $chatgptxService;
  protected $logger;
  protected $configFactory;

  public function __construct(chatgptxService $chatgptxService, LoggerChannelFactoryInterface $loggerFactory, ConfigFactoryInterface $configFactory) {
    $this->chatgptxService = $chatgptxService;
    $this->logger = $loggerFactory->get('chatgptx');
    $this->configFactory = $configFactory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('chatgptx.service'),
      $container->get('logger.factory'),
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'chatgptx_dataset_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['dataset'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Dataset'),
      '#rows' => 10,
      '#description' => $this->t('Enter the dataset as a CSV.'),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get the API key and organization ID from configuration.
    $config = $this->configFactory->get('my_module.settings');
    $api_key = $config->get('api_key');
    $organization_id = $config->get('organization_id');

    // Process the dataset using the API key and organization ID.
    // ...
    // Get the dataset from the form submission
    $dataset = $form_state->getValue('dataset');

    // Preprocess the dataset
    $preprocessed_dataset = $this->chatgptxService->preprocessDataset($dataset);

    // Train the model using the preprocessed dataset and the API key and organization ID from the settings
    $model = $this->chatgptxService->trainModel($preprocessed_dataset, $api_key, $organization_id);

    // Save the model
    $this->chatgptxService->saveModel($model);

    drupal_set_message($this->t('Dataset submitted successfully.'));
  }

}
