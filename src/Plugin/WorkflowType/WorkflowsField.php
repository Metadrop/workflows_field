<?php

namespace Drupal\workflows_field\Plugin\WorkflowType;

use Drupal\workflows\Annotation\WorkflowType;
use Drupal\workflows\Plugin\WorkflowTypeBase;

/**
 * @WorkflowType(
 *   id = "workflows_field",
 *   label = @Translation("Workflows Field"),
 *   required_states = {},
 *   forms = {},
 * )
 */
class WorkflowsField extends WorkflowTypeBase {
}
