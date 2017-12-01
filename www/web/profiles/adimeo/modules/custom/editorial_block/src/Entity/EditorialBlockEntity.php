<?php

namespace Drupal\editorial_block\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Editorial block entity.
 *
 * @ingroup editorial_block
 *
 * @ContentEntityType(
 *   id = "editorial_block_entity",
 *   label = @Translation("Editorial block"),
 *   bundle_label = @Translation("Editorial block type"),
 *   handlers = {
 *     "storage" = "Drupal\editorial_block\EditorialBlockEntityStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\editorial_block\EditorialBlockEntityListBuilder",
 *     "views_data" = "Drupal\editorial_block\Entity\EditorialBlockEntityViewsData",
 *     "translation" = "Drupal\editorial_block\EditorialBlockEntityTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\editorial_block\Form\EditorialBlockEntityForm",
 *       "add" = "Drupal\editorial_block\Form\EditorialBlockEntityForm",
 *       "edit" = "Drupal\editorial_block\Form\EditorialBlockEntityForm",
 *       "delete" = "Drupal\editorial_block\Form\EditorialBlockEntityDeleteForm",
 *     },
 *     "access" = "Drupal\editorial_block\EditorialBlockEntityAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\editorial_block\EditorialBlockEntityHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "editorial_block_entity",
 *   data_table = "editorial_block_entity_field_data",
 *   revision_table = "editorial_block_entity_revision",
 *   revision_data_table = "editorial_block_entity_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer editorial block entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "bundle" = "type",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/editorial_block_entity/{editorial_block_entity}",
 *     "add-page" = "/admin/structure/editorial_block_entity/add",
 *     "add-form" = "/admin/structure/editorial_block_entity/add/{editorial_block_entity_type}",
 *     "edit-form" = "/admin/structure/editorial_block_entity/{editorial_block_entity}/edit",
 *     "delete-form" = "/admin/structure/editorial_block_entity/{editorial_block_entity}/delete",
 *     "version-history" = "/admin/structure/editorial_block_entity/{editorial_block_entity}/revisions",
 *     "revision" = "/admin/structure/editorial_block_entity/{editorial_block_entity}/revisions/{editorial_block_entity_revision}/view",
 *     "revision_revert" = "/admin/structure/editorial_block_entity/{editorial_block_entity}/revisions/{editorial_block_entity_revision}/revert",
 *     "translation_revert" = "/admin/structure/editorial_block_entity/{editorial_block_entity}/revisions/{editorial_block_entity_revision}/revert/{langcode}",
 *     "revision_delete" = "/admin/structure/editorial_block_entity/{editorial_block_entity}/revisions/{editorial_block_entity_revision}/delete",
 *     "collection" = "/admin/structure/editorial_block_entity",
 *   },
 *   bundle_entity_type = "editorial_block_entity_type",
 *   field_ui_base_route = "entity.editorial_block_entity_type.edit_form"
 * )
 */
class EditorialBlockEntity extends RevisionableContentEntityBase implements EditorialBlockEntityInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly, make the editorial_block_entity owner the
    // revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Editorial block entity.'))
      ->setRevisionable(FALSE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Editorial block entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Editorial block is published.'))
      ->setRevisionable(TRUE)
      ->setDefaultValue(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    return $fields;
  }

}
