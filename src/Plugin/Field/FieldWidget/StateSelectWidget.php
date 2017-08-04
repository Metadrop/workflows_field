<?php

namespace Drupal\workflows_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\workflows\Entity\Workflow;

/**
 * @FieldWidget(
 *   id = "state_select_widget",
 *   label = @Translation("State Select  List"),
 *   field_types = {
 *     "workflows_field_item",
 *   }
 * )
 */
class StateSelectWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\workflows\WorkflowTypeInterface $type */
    $workflow = Workflow::load($items->getSetting('workflow'));
    $type = $workflow->getTypePlugin();

    // If no state is set, use the initial state from the type plugin.
    $existing_state = empty($items[$delta]->state) ? $type->getInitialState($workflow) : $type->getState($items[$delta]->state);

    // Allow the state not to change, but offer a list of possible to-states to
    // transition to.
    $options[$existing_state->id()] = $existing_state->label();
    foreach ($type->getTransitionsForState($existing_state->id()) as $transition) {
      $options[$transition->to()->id()] = $transition->to()->label();
    }

    $element['state'] = $element + [
      '#type' => 'select',
      '#options' => $options,
      '#default_value' => $existing_state->id(),
    ];
    return $element;
  }

}