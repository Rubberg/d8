<?php

/**
 * @file
 * Definition of \Drupal\ckeditor_plugins\Plugin\CKEditorPlugin\Fakeobjects.
 */

namespace Drupal\ckeditor_plugins\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginInterface;
use Drupal\Component\Plugin\PluginBase;
use Drupal\editor\Entity\Editor;

/**
 * Defines the "Dialog" plugin.
 *
 * @CKEditorPlugin(
 *   id = "fakeobjects",
 *   label = @Translation("Fakeobjects")
 * )
 */
class Fakeobjects extends PluginBase implements CKEditorPluginInterface {

    /**
     * Implements \Drupal\ckeditor\Plugin\CKEditorPluginInterface::getDependencies().
     */
    function getDependencies(Editor $editor) {
        return array();
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
        return drupal_get_path('module', 'ckeditor_plugins') . '/js/plugins/fakeobjects/plugin.js';
    }

    /**
     * Implements \Drupal\ckeditor\Plugin\CKEditorPluginInterface::getConfig().
     */
    public function getConfig(Editor $editor) {
        return array();
    }
}
