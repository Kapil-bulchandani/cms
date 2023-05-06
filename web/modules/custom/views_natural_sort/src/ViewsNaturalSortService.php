<?php

namespace Drupal\views_natural_sort;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Messenger\Messenger;
use Drupal\Core\StringTranslation\TranslationManager;
use Drupal\views_natural_sort\Plugin\IndexRecordContentTransformationManager as TransformationManager;
use Drupal\views\ViewsData;
use Drupal\Core\Queue\QueueWorkerManagerInterface;
use Drupal\Core\Queue\QueueFactory;

/**
 * Service that manages Views Natural Sort records.
 */
class ViewsNaturalSortService {

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\Messenger
   */
  protected $messenger;

  /**
   * The string translation service.
   *
   * @var \Drupal\Core\StringTranslation\TranslationManager
   */
  protected $translator;

  /**
   * Constructor.
   */
  public function __construct(TransformationManager $transformationManager, ConfigFactory $configFactory, ModuleHandlerInterface $moduleHandler, LoggerChannelFactoryInterface $loggerFactory, Connection $database, ViewsData $viewsData, QueueFactory $queue, QueueWorkerManagerInterface $queueManager, EntityFieldManagerInterface $entityFieldManager, EntityTypeManagerInterface $entityTypeManager, Messenger $messenger, TranslationManager $translator) {
    $this->configFactory = $configFactory;
    $this->moduleHandler = $moduleHandler;
    $this->loggerFactory = $loggerFactory->get('views_natural_sort');
    $this->transformationManager = $transformationManager;
    $this->database = $database;
    $this->viewsData = $viewsData;
    $this->queue = $queue;
    $this->queueManager = $queueManager;
    $this->entityFieldManager = $entityFieldManager;
    $this->entityTypeManager = $entityTypeManager;
    $this->messenger = $messenger;
    $this->translator = $translator;
  }

  /**
   * Get the full list of transformations to run when saving an index record.
   *
   * @param \Drupal\views_natural_sort\IndexRecord $record
   *   The original entry to be written to the views_natural_sort table.
   *
   * @return array
   *   The final list of transformations.
   */
  public function getTransformations(IndexRecord $record) {
    $transformations = $this->getDefaultTransformations();
    $this->moduleHandler->alter('views_natural_sort_transformations', $transformations, $record);
    return $transformations;
  }

  /**
   * Gets the transformation plugins that are enabled by default.
   *
   * @return \Drupal\views_natural_sort\Plugin\IndexRecordContentTransformationInterface[]
   *   An array of transformation plugins.
   */
  public function getDefaultTransformations() {
    $default_transformations = [
      'remove_beginning_words',
      'remove_words',
      'remove_symbols',
      'numbers',
      'days_of_the_week',
    ];
    $config = $this->configFactory->get('views_natural_sort.settings');
    $transformations = [];
    foreach ($default_transformations as $plugin_id) {
      if ($config->get('transformation_settings.' . $plugin_id . '.enabled')) {
        $transformations[] = $this->transformationManager->createInstance($plugin_id, $config->get('transformation_settings.' . $plugin_id));
      }
    }
    return $transformations;
  }

  /**
   * Retrieve the full list of entities and properties that can be supported.
   *
   * @return array
   *   An array of property information keyed by entity machine name. Example:
   *   [
   *     'node' => [
   *       'type' => [
   *         'base_table' => 'node',
   *         'schema_field' => 'type',
   *       ]
   *       'title' => [
   *         'base_table' => 'node',
   *         'schema_field' => 'title',
   *       ]
   *       'language' => [
   *         'base_table' => 'node',
   *         'schema_field' => 'language',
   *       ]
   *     ]
   *     'user' => [
   *       'name' => [
   *         'base_table' => 'users',
   *         'schema_field' => 'name',
   *       ]
   *       'mail' => [
   *         'base_table' => 'users',
   *         'schema_field' => 'mail',
   *       ]
   *       'theme' => [
   *         'base_table' => 'users',
   *         'schema_field' => 'theme',
   *       ]
   *     ]
   *     'file' => [
   *       'name' => [
   *         'base_table' => 'file_managed',
   *         'schema_field' => 'filename',
   *       ]
   *       'mime' => [
   *         'base_table' => 'file_managed',
   *         'schema_field' => 'filemime',
   *       ]
   *     ]
   *   )
   */
  public function getSupportedEntityProperties() {
    static $supported_properties = [];
    // These keys won't mean a lot to sort naturally.
    $key_ignore_list = [
      'bundle',
      'default_langcode',
      'id',
      'owner',
      'published',
      'revision',
      'revision_translation_affected',
      'uid',
      'uuid',
    ];
    if (empty($supported_properties)) {
      foreach ($this->entityFieldManager->getFieldMap() as $entity_type => $info) {
        $entityTypeObject = $this->entityTypeManager->getDefinition($entity_type);
        $keys = $entityTypeObject->getKeys();
        // We don't currently support non-integer ids.
        if (empty($keys['id']) || empty($info[$keys['id']]) || $info[$keys['id']]['type'] != 'integer') {
          continue;
        }
        $property_ignore_list = [];
        foreach ($key_ignore_list as $key) {
          if (isset($keys[$key])) {
            $property_ignore_list[] = $key;
          }
        }
        foreach ($info as $field_name => $field_info) {
          if ($field_info['type'] == 'string' && !in_array($field_name, $property_ignore_list)) {
            $field_configs = empty($field_info['bundles']) ? $this->entityFieldManager->getBaseFieldDefinitions($entity_type) : $this->entityFieldManager->getFieldDefinitions($entity_type, reset($field_info['bundles']));

            if (!isset($field_configs[$field_name])) {
              continue;
            }

            $field_config = $field_configs[$field_name];

            if (empty($supported_properties[$entity_type])) {
              $supported_properties[$entity_type] = [];
            }
            $base_table = $this->getViewsBaseTable($field_config);
            if (empty($base_table)) {
              continue;
            }

            $supported_properties[$entity_type][$field_name] = [
              'base_table' => $base_table,
              // This may not be techincally correct. Research Further.
              'schema_field' => $field_name,
            ];
          }
        }
      }
    }
    $this->moduleHandler->alter('views_natural_sort_supported_properties', $supported_properties);
    return $supported_properties;
  }

  /**
   * Helper to get all properties as keyed in views that are natural sortable.
   */
  public function getViewsSupportedEntityProperties() {
    static $views_supported_properties = [];
    if (empty($views_supported_properties)) {
      $supported_entity_properties = $this->getSupportedEntityProperties();
      $views_data = $this->viewsData->getAll();

      if (empty($views_data)) {
        return FALSE;
      }
      foreach ($supported_entity_properties as $entity => $properties) {
        foreach ($properties as $property => $schema_info) {
          if (!empty($views_data[$schema_info['base_table']][$schema_info['schema_field']]) &&
            !empty($views_data[$schema_info['base_table']][$schema_info['schema_field']]['sort']) &&
            !empty($views_data[$schema_info['base_table']][$schema_info['schema_field']]['sort']['id']) &&
            $views_data[$schema_info['base_table']][$schema_info['schema_field']]['sort']['id'] == 'natural') {
            $views_supported_properties[$entity][$property] = $schema_info;
          }
        }
      }
    }
    return $views_supported_properties;
  }

  /**
   * Store set of transformed records related to an entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   An entity of any kind we will store VNS records for.
   */
  public function storeIndexRecordsFromEntity(EntityInterface $entity) {
    // @todo Consider abstracting this out. The creation and storage of records
    // should be handled by a converter class that interacts with specific
    // IndexRecordTypes and creates IndexRecords. Those would probably be called
    // directly and have nothign to do with this service.
    $entity_type = $entity->getEntityTypeId();
    $supported_entity_properties = $this->getViewsSupportedEntityProperties();
    if (!$entity->id()) {
      return;
    }
    foreach ($supported_entity_properties[$entity_type] as $field => $field_info) {
      if (!isset($entity->{$field})) {
        continue;
      }
      foreach ($entity->get($field)->getValue() as $delta => $value) {
        $record = $this->createIndexRecord([
          'eid' => $entity->id(),
          'entity_type' => $entity_type,
          'field' => $field,
          'delta' => $delta,
          // This may have to be passed in if it's not always ['value'].
          'content' => $value['value'],
        ]);
        $record->save();
      }
    }
  }

  /**
   * Helper function to queue up all entities for vns indexing.
   */
  public function queueDataForRebuild(array $entry_types = []) {
    if (empty($entry_types)) {
      $entry_types = $this->moduleHandler->invokeAll('views_natural_sort_get_entry_types');
    }
    $queues = [];
    foreach ($entry_types as $entry_type) {
      $queues = array_unique(array_merge($queues, array_filter($this->moduleHandler->invokeAll('views_natural_sort_queue_rebuild_data', $entry_type))));
    }
    $operations = [];
    foreach ($queues as $queue) {
      $operations[] = [
        [$this, 'rebuildIndex'],
        [$queue],
      ];
    }
    $batch = [
      'operations' => $operations,
      'title' => $this->t('Rebuilding Views Natural Sort Indexing Entries'),
      'finished' => [$this, 'finishRebuild'],
    ];
    batch_set($batch);
  }

  /**
   * Batch job callback for rebuilding.
   */
  public function finishRebuild($success, $results, $operations) {
    if ($success) {
      $this->messenger->addMessage($this->translator->translate('Index rebuild has completed.'));
      $this->messenger->addMessage($this->translator->translate('Indexed %count.', [
        '%count' => $this->translator->formatPlural($results['entries'], '1 entry', '@count entries'),
      ]));
    }
  }

  /**
   * Create a single index record.
   */
  public function createIndexRecord(array $values = []) {
    $record = new IndexRecord($this->database, $values);
    $transformations = $this->getTransformations($record);
    $record->setTransformations($transformations);
    return $record;
  }

  /**
   * Helper function to help get the base tables for properties on an entity.
   *
   * @see EntityViewsData::getViewsData()
   */
  public function getViewsBaseTable($fieldDefinition) {
    $entityType = $this->entityTypeManager->getDefinition($fieldDefinition->getTargetEntityTypeId());
    $base_table = $entityType->getBaseTable() ?: $entityType->id();
    $views_revision_base_table = NULL;
    $revisionable = $entityType->isRevisionable();
    $base_field = $entityType->getKey('id');

    $revision_table = '';
    if ($revisionable) {
      $revision_table = $entityType->getRevisionTable() ?: $entityType->id() . '_revision';
    }

    $translatable = $entityType->isTranslatable();
    $data_table = '';
    if ($translatable) {
      $data_table = $entityType->getDataTable() ?: $entityType->id() . '_field_data';
    }

    // Some entity types do not have a revision data table defined, but still
    // have a revision table name set in
    // \Drupal\Core\Entity\Sql\SqlContentEntityStorage::initTableLayout() so we
    // apply the same kind of logic.
    $revision_data_table = '';
    if ($revisionable && $translatable) {
      $revision_data_table = $entityType->getRevisionDataTable() ?: $entityType->id() . '_field_revision';
    }
    $revision_field = $entityType->getKey('revision');

    $views_base_table = $base_table;
    if ($data_table) {
      $views_base_table = $data_table;
    }
    // @todo Add support for finding Fields API Fields base tables. See views.views.inc.
    return $views_base_table;
  }

}
