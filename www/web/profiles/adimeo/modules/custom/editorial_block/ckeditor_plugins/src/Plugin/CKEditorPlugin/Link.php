<?php

/**
 * @file
 * Definition of \Drupal\ckeditor_plugins\Plugin\CKEditorPlugin\Link.
 */

namespace Drupal\ckeditor_plugins\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginInterface;
use Drupal\ckeditor\CKEditorPluginButtonsInterface;
use Drupal\Component\Plugin\PluginBase;
use Drupal\editor\Entity\Editor;

/**
 * Defines the "Link" plugin.
 *
 * @CKEditorPlugin(
 *   id = "link",
 *   label = @Translation("Link")
 * )
 */
class Link extends PluginBase implements CKEditorPluginInterface, CKEditorPluginButtonsInterface {

  /**
   * Implements \Drupal\ckeditor\Plugin\CKEditorPluginInterface::getDependencies().
   */
  function getDependencies(Editor $editor) {
    return array('dialog','fakeobjects');
  }

  /**
   * Implements \Drupal\ckeditor\Plugin\CKEditorPluginInterface::getLibraries().
   */
  function getLibraries(Editor $editor) {
    return array();
  }

  /**
   * Implements \Drupal\ckeditor\Plugin\CKEditorPluginInterface::isInternal().
   */
  function isInternal() {
    return FALSE;
  }

  /**
   * Implements \Drupal\ckeditor\Plugin\CKEditorPluginInterface::getFile().
   */
  function getFile() {
    return drupal_get_path('module', 'ckeditor_plugins') . '/js/plugins/link/plugin.js';
  }

  /**
   * Implements \Drupal\ckeditor\Plugin\CKEditorPluginButtonsInterface::getButtons().
   */
  function getButtons() {
    return array(
      'Link' => array(
        'label' => t('Link'),
        'image' => drupal_get_path('module', 'ckeditor_plugins') . '/js/plugins/link/icons/link.png',
      ),
	  'Anchor' => array(
        'label' => t('Anchor'),
        'image' => drupal_get_path('module', 'ckeditor_plugins') . '/js/plugins/link/icons/anchor.png',
      ),
      );
  }

  /**
   * Implements \Drupal\ckeditor\Plugin\CKEditorPluginInterface::getConfig().
   */
  public function getConfig(Editor $editor) {
    return array();
  }
}
