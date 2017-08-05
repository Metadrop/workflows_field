<?php

namespace Drupal\workflows_field\Plugin\Validation\Constraint;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the workflows field.
 */
class WorkflowsFieldContraintValidator extends ConstraintValidator implements ContainerInjectionInterface {

  /**
   * Creates an instance of WorkflowsFieldContraintValidator.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('entity_type.manager'));
  }

  /**
   * {@inheritdoc}
   */
  public function validate($field, Constraint $constraint) {
    $entity = $field->getEntity();
    $workflow_type = $field->getWorkflow()->getTypePlugin();

    if (!isset($field->state) || $entity->isNew()) {
      return;
    }

    $original_entity = $this->entityTypeManager->getStorage($entity->getEntityTypeId())->loadRevision($entity->getLoadedRevisionId());
    if (!$entity->isDefaultTranslation() && $original_entity->hasTranslation($entity->language()->getId())) {
      $original_entity = $original_entity->getTranslation($entity->language()->getId());
    }
    $previous_state = $original_entity->{$field->getFieldDefinition()->getName()}->state;

    if ($previous_state === $field->state) {
      return;
    }

    if (!$workflow_type->hasTransitionFromStateToState($previous_state, $field->state)) {
      $this->context->addViolation($constraint->message, [
        '%state' => $field->state,
        '%previous_state' => $previous_state,
      ]);
    }
  }

}
