<?php

namespace Drupal\workflows_field\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\workflows\Entity\Workflow;

/**
 *   constraints = {"WorkflowsFieldValidStateTransition" = {}}
 *
 * @FieldType(
 *   id = "workflows_field_item",
 *   label = @Translation("Workflows"),
 *   description = @Translation("Allows you to store a workflow state."),
 *   constraints = {"WorkflowsFieldConstraint" = {}},
 *   default_formatter = "state_formatter",
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
  public static function calculateStorageDependencies(FieldStorageDefinitionInterface $field_definition) {
    $dependencies['config'][] = sprintf('workflows.workflow.%s', $field_definition->getSetting('workflow'));
    return $dependencies;
  }

  /**
   * Get the workflow associated with this field.
   */
  public function getWorkflow() {
    return Workflow::load($this->getSetting('workflow'));
  }

}
