<?php

namespace Drupal\cms_admin\Entity;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Access control handler for the CMS Environment entity.
 *
 * @see \Drupal\cms_admin\Entity\CMSEnvironment
 */
class CMSEnvironmentAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\cms_admin\Entity\CMSEnvironmentInterface $entity */
    \Drupal::logger('custom_log')->notice('$operation: ' . $operation);
//    switch ($operation) {
////      case 'view':
////        return $entity->access('view', $account);
////
////      case 'update':
////        return $entity->access('edit', $account);
////
////      case 'delete':
////        return $entity->access('delete', $account);
////
////      case 'create':
////        return $entity->access('create', $account);
////
////      case 'list':
////        return AccessResult::allowedIfHasPermission($account, 'cms_environment.list');
//
//
//      case 'view':
//        return AccessResult::allowedIfHasPermission($account, 'cms_environment.list');
//
//
//      case 'update':
//        return AccessResult::allowedIfHasPermission($account, 'cms_environment.list');
//
//
//      case 'delete':
//        return AccessResult::allowedIfHasPermission($account, 'cms_environment.list');
//
//
//      case 'create':
//        return AccessResult::allowedIfHasPermission($account, 'cms_environment.list');
//
//
//      case 'list':
//        return AccessResult::allowedIfHasPermission($account, 'cms_environment.list');
//
//    }

    // Unknown operation, no opinion.
//    return parent::checkAccess($entity, $operation, $account);
    return AccessResult::allowed();

  }

  /**
   * {@inheritdoc}
   */
  public function access(EntityInterface $entity, $operation, AccountInterface $account = NULL, $return_as_object = FALSE) {
    return AccessResult::allowed();

    /** @var \Drupal\cms_admin\Entity\CMSEnvironmentInterface $entity */
    if ($operation === 'update') {
      return $entity->access('edit', $account, $return_as_object);
    }
    if ($operation === 'delete') {
      return $entity->access('delete', $account, $return_as_object);
    }
    return parent::access($entity, $operation, $account, $return_as_object);
  }

}
