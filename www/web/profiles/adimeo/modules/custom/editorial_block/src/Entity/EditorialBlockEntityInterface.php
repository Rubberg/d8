<?php

namespace Drupal\editorial_block\Entity;

use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Editorial block entities.
 *
 * @ingroup editorial_block
 */
interface EditorialBlockEntityInterface extends RevisionableInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Editorial block name.
   *
   * @return string
   *   Name of the Editorial block.
   */
  public function getName();

  /**
   * Sets the Editorial block name.
   *
   * @param string $name
   *   The Editorial block name.
   *
   * @return \Drupal\editorial_block\Entity\EditorialBlockEntityInterface
   *   The called Editorial block entity.
   */
  public function setName($name);

  /**
   * Gets the Editorial block creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Editorial block.
   */
  public function getCreatedTime();

  /**
   * Sets the Editorial block creation timestamp.
   *
   * @param int $timestamp
   *   The Editorial block creation timestamp.
   *
   * @return \Drupal\editorial_block\Entity\EditorialBlockEntityInterface
   *   The called Editorial block entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Editorial block published status indicator.
   *
   * Unpublished Editorial block are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Editorial block is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Editorial block.
   *
   * @param bool $published
   *   TRUE to set this Editorial block to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\editorial_block\Entity\EditorialBlockEntityInterface
   *   The called Editorial block entity.
   */
  public function setPublished($published);

  /**
   * Gets the Editorial block revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Editorial block revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\editorial_block\Entity\EditorialBlockEntityInterface
   *   The called Editorial block entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Editorial block revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Editorial block revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\editorial_block\Entity\EditorialBlockEntityInterface
   *   The called Editorial block entity.
   */
  public function setRevisionUserId($uid);

}
