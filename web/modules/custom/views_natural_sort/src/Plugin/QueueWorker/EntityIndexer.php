<?php

namespace Drupal\views_natural_sort\Plugin\QueueWorker;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\views_natural_sort\ViewsNaturalSortService;

/**
 * Provides base functionality for the VNS Entity Index Queue Workers.
 *
 * @QueueWorker(
 *   id = "views_natural_sort_entity_index",
 *   title = @Translation("Views Natural Sort Entity Index"),
 * )
 */
class EntityIndexer extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  /**
   * Entity Type Manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, ViewsNaturalSortService $viewsNaturalSortService) {
    $this->entityTypeManager = $entityTypeManager;
    $this->viewsNaturalSortService = $viewsNaturalSortService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('views_natural_sort.service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function processItem($data) {
    $entity = $this->entityTypeManager
      ->getStorage($data['entity_type'])
      ->load($data['entity_id']);
    if ($entity) {
      $this->viewsNaturalSortService->storeIndexRecordsFromEntity($entity);
    }
  }

}
