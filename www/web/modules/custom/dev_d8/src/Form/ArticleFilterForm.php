<?php

namespace Drupal\dev_d8\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ArticleFilterForm.
 */
class ArticleFilterForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'article_filter_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['keywords'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Mots clés'),
    ];
    
    $form['tags'] = [
      '#type' => 'select',
      '#title' => $this->t('Tags'),
      '#options' => ['3' => 'Terme 1', '2' => 'Terme 2'],
    ];
    
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Filter'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $tag = $form_state->getValue('tags');
    
    $form_state->setRedirect('dev_d8.formation_controller_listeArticle', array('tag' => $tag));
  }

}
