<?php

namespace Drupal\dev_d8\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use \Symfony\Component\HttpFoundation\Request;

/**
 * Class FormationController.
 */
class FormationController extends ControllerBase {

  /**
   * Listearticle.
   *
   * @return string
   *   Return Hello string.
   */
  public function listeArticle(Request $request) {
      
      $render[] = \Drupal::formBuilder()->getForm('Drupal\dev_d8\Form\ArticleFilterForm');
      
      //recuperation de $_GET['tag']
      $tag = $request->get('tag');
      
      $nodesTotal = \drupal::entityQuery('node')
              ->condition('type', 'article')
              ->condition('field_tags', $tag)
              ->count()->execute();
      
      $nids = \drupal::entityQuery('node')
              ->condition('type', 'article')
              ->condition('field_tags', $tag)
              ->pager(5)
              ->execute();
      
      $nodes = Node::loadMultiple($nids);
      
      $view_builder = \Drupal::entityTypeManager()->getViewBuilder('node');
      
      $render[] = $view_builder->viewMultiple($nodes, 'teaser');
      
      pager_default_initialize($nodesTotal, 5);
      
      $render[] = array('#type' => 'pager');
      
    return $render;
  }

}
