<?php

namespace Drupal\editorial_block\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Editorial block type entity.
 *
 * @ConfigEntityType(
 *   id = "editorial_block_entity_type",
 *   label = @Translation("Editorial block type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\editorial_block\EditorialBlockEntityTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\editorial_block\Form\EditorialBlockEntityTypeForm",
 *       "edit" = "Drupal\editorial_block\Form\EditorialBlockEntityTypeForm",
 *       "delete" = "Drupal\editorial_block\Form\EditorialBlockEntityTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\editorial_block\EditorialBlockEntityTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "editorial_block_entity_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "editorial_block_entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/editorial_block_entity_type/{editorial_block_entity_type}",
 *     "add-form" = "/admin/structure/editorial_block_entity_type/add",
 *     "edit-form" = "/admin/structure/editorial_block_entity_type/{editorial_block_entity_type}/edit",
 *     "delete-form" = "/admin/structure/editorial_block_entity_type/{editorial_block_entity_type}/delete",
 *     "collection" = "/admin/structure/editorial_block_entity_type"
 *   }
 * )
 */
class EditorialBlockEntityType extends ConfigEntityBundleBase implements EditorialBlockEntityTypeInterface {

  /**
   * The Editorial block type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Editorial block type label.
   *
   * @var string
   */
  protected $label;

}
