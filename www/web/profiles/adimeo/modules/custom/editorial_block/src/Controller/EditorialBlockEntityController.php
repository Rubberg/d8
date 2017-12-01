<?php

namespace Drupal\editorial_block\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\editorial_block\Entity\EditorialBlockEntityInterface;

/**
 * Class EditorialBlockEntityController.
 *
 *  Returns responses for Editorial block routes.
 */
class EditorialBlockEntityController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Editorial block  revision.
   *
   * @param int $editorial_block_entity_revision
   *   The Editorial block  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($editorial_block_entity_revision) {
    $editorial_block_entity = $this->entityManager()->getStorage('editorial_block_entity')->loadRevision($editorial_block_entity_revision);
    $view_builder = $this->entityManager()->getViewBuilder('editorial_block_entity');

    return $view_builder->view($editorial_block_entity);
  }

  /**
   * Page title callback for a Editorial block  revision.
   *
   * @param int $editorial_block_entity_revision
   *   The Editorial block  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($editorial_block_entity_revision) {
    $editorial_block_entity = $this->entityManager()->getStorage('editorial_block_entity')->loadRevision($editorial_block_entity_revision);
    return $this->t('Revision of %title from %date', ['%title' => $editorial_block_entity->label(), '%date' => format_date($editorial_block_entity->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Editorial block .
   *
   * @param \Drupal\editorial_block\Entity\EditorialBlockEntityInterface $editorial_block_entity
   *   A Editorial block  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(EditorialBlockEntityInterface $editorial_block_entity) {
    $account = $this->currentUser();
    $langcode = $editorial_block_entity->language()->getId();
    $langname = $editorial_block_entity->language()->getName();
    $languages = $editorial_block_entity->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $editorial_block_entity_storage = $this->entityManager()->getStorage('editorial_block_entity');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $editorial_block_entity->label()]) : $this->t('Revisions for %title', ['%title' => $editorial_block_entity->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all editorial block revisions") || $account->hasPermission('administer editorial block entities')));
    $delete_permission = (($account->hasPermission("delete all editorial block revisions") || $account->hasPermission('administer editorial block entities')));

    $rows = [];

    $vids = $editorial_block_entity_storage->revisionIds($editorial_block_entity);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\editorial_block\EditorialBlockEntityInterface $revision */
      $revision = $editorial_block_entity_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $editorial_block_entity->getRevisionId()) {
          $link = $this->l($date, new Url('entity.editorial_block_entity.revision', ['editorial_block_entity' => $editorial_block_entity->id(), 'editorial_block_entity_revision' => $vid]));
        }
        else {
          $link = $editorial_block_entity->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => \Drupal::service('renderer')->renderPlain($username),
              'message' => ['#markup' => $revision->getRevisionLogMessage(), '#allowed_tags' => Xss::getHtmlTagList()],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.editorial_block_entity.translation_revert', ['editorial_block_entity' => $editorial_block_entity->id(), 'editorial_block_entity_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.editorial_block_entity.revision_revert', ['editorial_block_entity' => $editorial_block_entity->id(), 'editorial_block_entity_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.editorial_block_entity.revision_delete', ['editorial_block_entity' => $editorial_block_entity->id(), 'editorial_block_entity_revision' => $vid]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['editorial_block_entity_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
