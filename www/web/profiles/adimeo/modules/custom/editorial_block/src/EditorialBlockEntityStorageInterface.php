<?php

namespace Drupal\editorial_block;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface EditorialBlockEntityStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Editorial block revision IDs for a specific Editorial block.
   *
   * @param \Drupal\editorial_block\Entity\EditorialBlockEntityInterface $entity
   *   The Editorial block entity.
   *
   * @return int[]
   *   Editorial block revision IDs (in ascending order).
   */
  public function revisionIds(EditorialBlockEntityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Editorial block author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Editorial block revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\editorial_block\Entity\EditorialBlockEntityInterface $entity
   *   The Editorial block entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(EditorialBlockEntityInterface $entity);

  /**
   * Unsets the language for all Editorial block with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
