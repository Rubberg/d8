<?php

namespace DrupalCodeGenerator\Command\Drupal_8\Plugin;

use DrupalCodeGenerator\Command\BaseGenerator;
use DrupalCodeGenerator\Utils;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Implements d8:plugin:action command.
 */
class Action extends BaseGenerator {

  protected $name = 'd8:plugin:action';
  protected $description = 'Generates action plugin';
  protected $alias = 'action';

  /**
   * {@inheritdoc}
   */
  protected function interact(InputInterface $input, OutputInterface $output) {
    $questions = Utils::defaultPluginQuestions() + [
      'category' => new Question('Action category', 'Custom'),
      'configurable' => new ConfirmationQuestion('Make the action configurable?', FALSE),
    ];
    // Plugin label should declare an action.
    $questions['plugin_label'] = new Question('Action label', 'Update node title');

    $vars = $this->collectVars($input, $output, $questions);
    $vars['class'] = Utils::camelize($vars['plugin_label']);

    $path = 'src/Plugin/Action/' . $vars['class'] . '.php';
    $this->setFile($path, 'd8/plugin/action.twig', $vars);

    if ($vars['configurable']) {
      $this->files['config/schema/' . $vars['machine_name'] . '.schema.yml'] = [
        'content' => $this->render('d8/plugin/action-schema.twig', $vars),
        'action' => 'append',
      ];
    }

  }

}
