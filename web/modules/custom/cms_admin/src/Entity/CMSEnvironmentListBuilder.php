<?php

namespace Drupal\cms_admin\Entity;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityListBuilder;


  /**
 * Defines a class to build a listing of CMS Environment entities.
 */
class CMSEnvironmentListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('ID');
    $header['label'] = $this->t('Label');
    $header['status'] = $this->t('Status');
    $header['operations'] = $this->t('Operations');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['id'] = $entity->id();
    $row['label'] = $entity->label();
    $row['status'] = $entity->status() ? $this->t('Enabled') : $this->t('Disabled');

    $operations['edit'] = [
      'title' => $this->t('Edit'),
      'url' => Url::fromRoute('entity.cms_environment.edit', ['cms_environment' => $entity->id()]),
    ];
    $operations['delete'] = [
      'title' => $this->t('Delete'),
      'url' => Url::fromRoute('entity.cms_environment.delete', ['cms_environment' => $entity->id()]),
    ];

    $row['operations'] = [
      '#type' => 'operations',
      '#links' => $operations,
    ];

    return $row + parent::buildRow($entity);
  }

}
