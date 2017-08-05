<?php

namespace Drupal\Tests\workflows_field\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;

/**
 * Test the workflows field.
 *
 * @group workflows_field
 */
class WorkflowsFieldTest extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'system',
    'user',
    'node',
    'workflows',
    'workflows_field',
    'field',
    'workflows_field_test_workflows',
  ];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    $this->installEntitySchema('workflow');
    $this->installConfig(['workflows_field_test_workflows']);
  }

  /**
   * @covers \Drupal\workflows_field\Plugin\Validation\Constraint\WorkflowsFieldContraint
   * @covers \Drupal\workflows_field\Plugin\Validation\Constraint\WorkflowsFieldContraintValidator
   */
  public function testWorkflowsConstraint() {
    $node = Node::create([
      'title' => 'Foo',
      'type' => 'project',
      'field_status' => 'in_discussion',
    ]);
    $node->save();

    // Same state does not cause a violation.
    $node->field_status->state = 'in_discussion';
    $violations = $node->validate();
    $this->assertCount(0, $violations);

    // A valid state does not cause a violation.
    $node->field_status->state = 'approved';
    $violations = $node->validate();
    $this->assertCount(0, $violations);

    // Violation exists during invalid transition.
    $node->field_status->state = 'planning';
    $violations = $node->validate();
    $this->assertCount(1, $violations);
    $this->assertEquals('No transition exists to move from <em class="placeholder">in_discussion</em> to <em class="placeholder">planning</em>.', $violations[0]->getMessage());
  }

}
