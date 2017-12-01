<?php

/**
 * @file
 * Enables modules and site configuration for a Adimeo site installation.
 */

use Drupal\Core\Form\FormStateInterface;
use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\adimeo\Config\ConfigBit;
use Drupal\adimeo\Form\ConfigureMultilingualForm;
use Drupal\adimeo\Form\AssemblerForm;
use Drupal\adimeo\Form\DevelopmentToolsAssemblerForm;

/**
 * Implements hook_form_FORM_ID_alter() for install_configure_form().
 *
 * Allows the profile to alter the site configuration form.
 */
function adimeo_form_install_configure_form_alter(&$form, FormStateInterface $form_state) {
    
    // install libraries ckeditor 
    exec(" drupal ckeditor_media_embed:install");
    // Add a placeholder as example that one can choose an arbitrary site name.
    $form['site_information']['site_name']['#attributes']['placeholder'] = t('Drupal 8');

    // Default site email abedjih@adimeo.com .
    $form['site_information']['site_mail']['#default_value'] = 'abedjih@adimeo.com';
    $form['site_information']['site_mail']['#attributes']['style'] = 'width: 25em;';

    // Default user 1 username should be 'adimeo'.
    $form['admin_account']['account']['name']['#default_value'] = 'adimeo';

    $form['admin_account']['account']['name']['#attributes']['disabled'] = TRUE;
    $form['admin_account']['account']['mail']['#default_value'] = 'abedjih@adimeo.com';
    $form['admin_account']['account']['mail']['#description'] = t('In most case, and for <a target="_blank" href="@link">Vardot</a> specific use, we recommend this to always be <em>abedjih@adimeo.com</em>.', array('@link' => 'http://adimeo.com'));
    $form['regional_settings']['site_default_country']['#default_value'] = 'FR';
    $form['regional_settings']['date_default_timezone']['#default_value'] = 'Europe/Paris';
}

/**
 * Implements hook_install_tasks().
 */
function adimeo_install_tasks(&$install_state) {
    // Determine whether the enable multiligual option is selected during the
    // Multilingual configuration task.
    $needs_configure_multilingual = (isset($install_state['adimeo']['enable_multilingual']) && $install_state['adimeo']['enable_multilingual'] == TRUE);

    return array(
        'adimeo_multilingual_configuration_form' => array(
            'display_name' => t('Multilingual configuration'),
            'display' => TRUE,
            'type' => 'form',
            'function' => ConfigureMultilingualForm::class,
        ),
        'adimeo_configure_multilingual' => array(
            'display_name' => t('Configure multilingual'),
            'display' => $needs_configure_multilingual,
            'type' => 'batch',
        ),
        'adimeo_extra_components' => array(
            'display_name' => t('Extra components'),
            'display' => TRUE,
            'type' => 'form',
            'function' => AssemblerForm::class,
        ),
        'adimeo_assemble_extra_components' => array(
            'display_name' => t('Assemble extra components'),
            'display' => TRUE,
            'type' => 'batch',
        ),
        'adimeo_development_tools' => array(
            'display_name' => t('Development tools'),
            'display' => TRUE,
            'type' => 'form',
            'function' => DevelopmentToolsAssemblerForm::class,
        ),
        'adimeo_assemble_development_tools' => array(
            'display_name' => t('Assemble development tools'),
            'display' => TRUE,
            'type' => 'batch',
        ),
    );
}

/**
 * Implements hook_install_tasks_alter().
 */
function adimeo_install_tasks_alter(array &$tasks, array $install_state) {
    $tasks['install_finished']['function'] = 'adimeo_after_install_finished';
}

/**
 * Batch job to assemble adimeo extra components.
 *
 * @param array $install_state
 *   The current install state.
 *
 * @return array
 *   The batch job definition.
 */
function adimeo_assemble_extra_components(array &$install_state) {

    // Default adimeo components, which must be installed.
    $default_components = ConfigBit::getList('configbit/default.components.adimeo.bit.yml', 'install_default_components', TRUE, 'dependencies', 'profile', 'adimeo');

    $batch = [];

    // Install default components first.
    foreach ($default_components as $default_component) {
        $batch['operations'][] = ['adimeo_assemble_extra_component_then_install', (array) $default_component];
    }

    // Install selected extra features.
    $selected_extra_features = [];
    $selected_extra_features_configs = [];

    if (isset($install_state['adimeo']['extra_features_values'])) {
        $selected_extra_features = $install_state['adimeo']['extra_features_values'];
    }

    if (isset($install_state['adimeo']['extra_features_configs'])) {
        $selected_extra_features_configs = $install_state['adimeo']['extra_features_configs'];
    }

    // Get the list of extra features config bits.
    $extraFeatures = ConfigBit::getList('configbit/extra.components.adimeo.bit.yml', 'show_extra_components', TRUE, 'dependencies', 'profile', 'adimeo');

    // If we do have selected extra features.
    if (count($selected_extra_features) && count($extraFeatures)) {
        // Have batch processes for each selected extra features.
        foreach ($selected_extra_features as $extra_feature_key => $extra_feature_checked) {
            if ($extra_feature_checked) {

                // If the extra feature was a module and not enabled, then enable it.
                if (!\Drupal::moduleHandler()->moduleExists($extra_feature_key)) {
                    // Add the checked extra feature to the batch process to be enabled.
                    $batch['operations'][] = ['adimeo_assemble_extra_component_then_install', (array) $extra_feature_key];
                }

                if (count($selected_extra_features_configs) &&
                    isset($extraFeatures[$extra_feature_key]['config_form']) &&
                    $extraFeatures[$extra_feature_key]['config_form'] == TRUE &&
                    isset($extraFeatures[$extra_feature_key]['formbit'])) {

                    $formbit_file_name = drupal_get_path('profile', 'adimeo') . '/' . $extraFeatures[$extra_feature_key]['formbit'];

                    if (file_exists($formbit_file_name)) {

                        // Added the selected extra feature configs to the batch process
                        // with the same function name in the formbit.
                        $batch['operations'][] = ['adimeo_save_editable_config_values',
                            (array) [
                                $extra_feature_key,
                                $formbit_file_name,
                                $selected_extra_features_configs,
                            ],
                        ];
                    }
                }
            }
        }


        // Hide Wornings and status messages.
        $batch['operations'][] = ['adimeo_hide_warning_and_status_messages', (array) TRUE];

        // Fix entity updates to clear up any mismatched entity.
        $batch['operations'][] = ['adimeo_fix_entity_update', (array) TRUE];
    }



    return $batch;
}

/**
 * Batch job to assemble adimeo extra components.
 *
 * @param array $install_state
 *   The current install state.
 *
 * @return array
 *   The batch job definition.
 */
function adimeo_assemble_development_tools(array &$install_state) {

    $batch = [];

    // Install selected Development tools.
    $selected_development_tools = [];
    $selected_development_configs = [];

    if (isset($install_state['adimeo']['development_tools_values'])) {
        $selected_development_tools = $install_state['adimeo']['development_tools_values'];
    }

    if (isset($install_state['adimeo']['development_tools_configs'])) {
        $selected_development_configs = $install_state['adimeo']['development_tools_configs'];
    }

    // Development tools.
    $developmentTools = ConfigBit::getList('configbit/development.tools.adimeo.bit.yml', 'show_development_tools', TRUE, 'dependencies', 'profile', 'adimeo');

    // If we do have development tools and we have selected development tools.
    if (count($selected_development_tools) && count($developmentTools)) {
        // Have batch processes for each selected development tool.
        foreach ($selected_development_tools as $development_tool_key => $development_tool_checked) {
            if ($development_tool_checked) {

                // If the development tool was a module and not enabled, then enable it.
                if (!\Drupal::moduleHandler()->moduleExists($development_tool_key)) {
                    // Add checked development tool to the batch process to be enabled.
                    $batch['operations'][] = ['adimeo_assemble_extra_component_then_install', (array) $development_tool_key];
                }

                if (count($selected_development_configs) &&
                    isset($developmentTools[$development_tool_key]['config_form']) &&
                    $developmentTools[$development_tool_key]['config_form'] == TRUE &&
                    isset($developmentTools[$development_tool_key]['formbit'])) {

                    $formbit_file_name = drupal_get_path('profile', 'adimeo') . '/' . $developmentTools[$development_tool_key]['formbit'];
                    if (file_exists($formbit_file_name)) {

                        // Added the selected development configs to the batch process
                        // with the same function name in the formbit.
                        $batch['operations'][] = ['adimeo_save_editable_config_values',
                            (array) [
                                $development_tool_key,
                                $formbit_file_name,
                                $selected_development_configs,
                            ],
                        ];
                    }
                }
            }
        }

        // Hide Wornings and status messages.
        $batch['operations'][] = ['adimeo_hide_warning_and_status_messages', (array) TRUE];

        // Fix entity updates to clear up any mismatched entity.
        $batch['operations'][] = ['adimeo_fix_entity_update', (array) TRUE];
    }

    return $batch;
}

/**
 * Batch job to configure multilingual components.
 *
 * @param array $install_state
 *   The current install state.
 *
 * @return array
 *   The batch job definition.
 */
function adimeo_configure_multilingual(array &$install_state) {
    $batch = array();

    // If the multiligual config checkbox were checked.
    if (isset($install_state['adimeo']['enable_multilingual'])
        && $install_state['adimeo']['enable_multilingual'] == TRUE) {

        // Install the adimeo internationalization feature module.
        $batch['operations'][] = ['adimeo_assemble_extra_component_then_install', (array) 'adimeo_internationalization'];

        // Add all selected languages and then translatadimeo_hide_messagesion
        // will fetched for theme.
        foreach ($install_state['adimeo']['multilingual_languages'] as $language_code) {
            $batch['operations'][] = ['adimeo_configure_language_and_fetch_traslation', (array) $language_code];
        }

        // Hide Wornings and status messages.
        $batch['operations'][] = ['adimeo_hide_warning_and_status_messages', (array) TRUE];

        // Change configurations to work with enable_multilingual.
        $batch['operations'][] = ['adimeo_config_bit_for_multilingual', (array) TRUE];

    }
    else {
        // Change configurations to work with NO multilingual.
        $batch['operations'][] = ['adimeo_config_bit_for_multilingual', (array) FALSE];
    }

    // Fix entity updates to clear up any mismatched entity.
    $batch['operations'][] = ['adimeo_fix_entity_update', (array) TRUE];

    return $batch;
}

/**
 * Batch function to assemble and install needed extra components.
 *
 * @param string|array $extra_component
 *   Name of the extra component.
 */
function adimeo_assemble_extra_component_then_install($extra_component) {
    \Drupal::service('module_installer')->install((array) $extra_component, TRUE);
}

/**
 * Batch function to save editable config values for extra components.
 *
 * @param string|array $extra_component_machine_name
 *   Machine name key of the extra component.
 * @param string|array $formbit_file_name
 *   FormBit file name.
 * @param string|array $editable_config_values
 *   Editable config values.
 */
function adimeo_save_editable_config_values($extra_component_machine_name, $formbit_file_name, $editable_config_values) {
    include_once $formbit_file_name;
    call_user_func_array($extra_component_machine_name . "_submit_formbit", array($editable_config_values));
}

/**
 * Batch function to add selected langauges then fetch all traslation.
 *
 * @param string|array $language_code
 *   Language code to install and fetch all traslation.
 */
function adimeo_configure_language_and_fetch_traslation($language_code) {
    ConfigurableLanguage::createFromLangcode($language_code)->save();
}

/**
 * Batch function to fix entity updates to clear up any mismatched entity.
 *
 * Entity and/or field definitions, The following changes were detected in
 * the entity type and field definitions.
 *
 * @param string|array $entity_update
 *   To entity update or not.
 */
function adimeo_fix_entity_update($entity_update) {
    if ($entity_update) {
        \Drupal::entityDefinitionUpdateManager()->applyUpdates();
    }
}

/**
 * Batch function to update configs with config bit configurations.
 *
 * @param bool $enable_multilingual
 *   Use multilignual in the site.
 */
function adimeo_config_bit_for_multilingual($enable_multilingual) {

    // Change configurations to work with enable_multilingual.
    if ($enable_multilingual) {

        // Put back the language config if it was not in before installation.
        ConfigBit::actionUnArchiveFiles('configbit/language.action.adimeo.bit.yml', 'enable_multilingual', TRUE, 'profile', 'adimeo');

        // Put back the language config bit
        ConfigBit::actionAdd('configbit/adimeo_media.info.bit.yml', 'enable_multilingual', TRUE, 'dependencies', 'profile', 'adimeo');
    }
    else {
        // Change configurations to work with NO multilingual.
        // Archive the language config out before installation.
        ConfigBit::actionArchiveFiles('configbit/language.action.adimeo.bit.yml', 'enable_multilingual', FALSE, 'profile', 'adimeo');

        // Remove language config bit before installation.
        ConfigBit::actionRemove('configbit/adimeo_media.info.bit.yml', 'enable_multilingual', FALSE, 'dependencies', 'profile', 'adimeo');
    }

}

/**
 * Batch function to Uninstall list of not needed modules after the config had been loaded.
 *
 * @param string|array $uninstall_component
 *   Name of the extra component.
 */
function adimeo_uninstall_component($uninstall_component) {
    if (\Drupal::moduleHandler()->moduleExists($uninstall_component)) {
        \Drupal::service('module_installer')->uninstall((array) $uninstall_component, FALSE);
    }
}

/**
 * adimeo after install finished.
 *
 * Lanuch auto adimeo Tour auto launch after install.
 *
 * @param array $install_state
 *   The current install state.
 *
 * @return array
 *   A renderable array with a redirect header.
 */
function adimeo_after_install_finished(array &$install_state) {
    /*global $base_url;

    // After install direction.
    $after_install_direction = $base_url . '/?welcome';

    install_finished($install_state);
    $output = [];

    // Clear all messages.
    drupal_get_messages();

    $output = [
        '#title' => t('adimeo'),
        'info' => [
            '#markup' => t('<p>Congratulations, you have installed adimeo!</p><p>If you are not redirected to the front page in 5 seconds, Please <a href="@url">click here</a> to proceed to your insalled site.</p>', [
                '@url' => $after_install_direction,
            ]),
        ],
        '#attached' => [
            'http_header' => [
                ['Cache-Control', 'no-cache'],
            ],
        ],
    ];

    $meta_redirect = [
        '#tag' => 'meta',
        '#attributes' => [
            'http-equiv' => 'refresh',
            'content' => '0;url=' . $after_install_direction,
        ],
    ];
    $output['#attached']['html_head'][] = [$meta_redirect, 'meta_redirect'];

    return $output; */
}

/**
 * Batch function to hide warning messages.
 *
 * @param bool $hide
 *   To hide or not.
 */
function adimeo_hide_warning_and_status_messages($hide) {
    if ($hide && !isset($_SESSION['messages']['error'])) {
        unset($_SESSION['messages']);
    }
}

