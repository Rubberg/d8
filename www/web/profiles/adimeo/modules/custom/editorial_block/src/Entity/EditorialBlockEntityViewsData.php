<?php

namespace Drupal\editorial_block\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Editorial block entities.
 */
class EditorialBlockEntityViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
