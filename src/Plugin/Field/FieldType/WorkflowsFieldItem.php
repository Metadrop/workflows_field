<?php

namespace Drupal\workflows_field\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\workflows\Entity\Workflow;

/**
 * @FieldType(
 *   id = "workflows_field_item",
 *   label = @Translation("Workflows"),
 *   description = @Translation("Allows you to store a workflow state."),
 *   constraints = {"WorkflowsFieldValidStateTransition" = {}}
 * )
 */
class WorkflowsFieldItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['state'] = DataDefinition::create('string')
      ->setLabel(t('State'))
      ->setRequired(TRUE);
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'state' => [
          'type' => 'varchar',
          'length' => 64,
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    $settings = [
      'workflow' => NULL,
    ];
    return $settings + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    // @todo, technically we could use any workflow?
    $workflows = Workflow::loadMultipleByType('workflows_field');
    $options = [];
    foreach ($workflows as $workflow) {
      $options[$workflow->id()] = $workflow->label();
    }

    $element = [];
    $element['workflow'] = [
      '#title' => $this->t('Workflow'),
      '#required' => TRUE,
      '#default_value' => $this->getSetting('workflow'),
      '#type' => 'select',
      '#options' => $options,
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function calculateDependencies(FieldDefinitionInterface $field_definition) {
//    $dependencies = parent::calculateDependencies($field_definition);
//    $dependencies['config'][] =
    return [];
  }

}
