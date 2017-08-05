<?php

namespace Drupal\workflows_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Formatter to display the current state.
 *
 * @FieldFormatter(
 *   id = "state_formatter",
 *   label = @Translation("State"),
 *   field_types = {
 *     "workflows_field_item",
 *   },
 * )
 */
class StateFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $workflow_type = $items[0]->getWorkflow()->getTypePlugin();
    foreach ($items as $delta => $item) {
      $state = $workflow_type->getState($item->state);
      $elements[$delta] = [
        '#markup' => $state->label(),
      ];
    }
    return $elements;
  }

}
