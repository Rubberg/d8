<?php

/**
 * @file
 * Contains editorial_block_entity.page.inc.
 *
 * Page callback for Editorial block entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Editorial block templates.
 *
 * Default template: editorial_block_entity.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_editorial_block_entity(array &$variables) {
  // Fetch EditorialBlockEntity Entity Object.
  $editorial_block_entity = $variables['elements']['#editorial_block_entity'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
