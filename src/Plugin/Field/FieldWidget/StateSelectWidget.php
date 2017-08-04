<?php

namespace Drupal\workflows_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @FieldWidget(
 *   id = "state_select_widget",
 *   label = @Translation("State Select"),
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
    
  }

}
