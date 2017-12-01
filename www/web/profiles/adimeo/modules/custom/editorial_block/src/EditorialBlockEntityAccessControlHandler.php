<?php

namespace Drupal\editorial_block;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Editorial block entity.
 *
 * @see \Drupal\editorial_block\Entity\EditorialBlockEntity.
 */
class EditorialBlockEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\editorial_block\Entity\EditorialBlockEntityInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished editorial block entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published editorial block entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit editorial block entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete editorial block entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add editorial block entities');
  }

}
