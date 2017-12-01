<?php

namespace Drupal\editorial_block;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\editorial_block\Entity\EditorialBlockEntityInterface;

/**
 * Defines the storage handler class for Editorial block entities.
 *
 * This extends the base storage class, adding required special handling for
 * Editorial block entities.
 *
 * @ingroup editorial_block
 */
class EditorialBlockEntityStorage extends SqlContentEntityStorage implements EditorialBlockEntityStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(EditorialBlockEntityInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {editorial_block_entity_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {editorial_block_entity_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(EditorialBlockEntityInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {editorial_block_entity_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('editorial_block_entity_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
