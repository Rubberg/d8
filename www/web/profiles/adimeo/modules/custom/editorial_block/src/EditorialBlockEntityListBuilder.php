<?php

namespace Drupal\editorial_block;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Editorial block entities.
 *
 * @ingroup editorial_block
 */
class EditorialBlockEntityListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Editorial block ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\editorial_block\Entity\EditorialBlockEntity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.editorial_block_entity.edit_form',
      ['editorial_block_entity' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
