<?php

namespace Drupal\Tests\workflows_field\Kernel;

use Drupal\Core\Form\FormState;
use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\workflows\Entity\Workflow;
use Drupal\workflows_field\Plugin\Field\FieldType\WorkflowsFieldItem;

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

  /**
   * @covers \Drupal\workflows_field\Plugin\Field\FieldFormatter\StateFormatter
   */
  public function testFormatter() {
    $node = Node::create([
      'title' => 'Foo',
      'type' => 'project',
      'field_status' => 'in_discussion',
    ]);
    $node->save();

    $this->assertEquals([
      '#markup' => 'In Discussion',
    ], $node->field_status->view()[0]);
  }

  /**
   * @covers \Drupal\workflows_field\Plugin\Field\FieldWidget\StateSelectWidget
   */
  public function testWidget() {
    $node = Node::create([
      'title' => 'Foo',
      'type' => 'project',
    ]);
    $node->save();

    /** @var \Drupal\Core\Field\WidgetPluginManager $widget */
    $widget_manager = \Drupal::service('plugin.manager.field.widget');
    $widget = $widget_manager->createInstance('state_select_widget', [
      'field_definition' => $node->field_status->getFieldDefinition(),
      'settings' => [],
      'third_party_settings' => [],
    ]);

    $form_state = new FormState();
    $empty = [];

    // Test with an empty field item list to begin with.
    $form = $widget->formElement($node->field_status, 0, $empty, $empty, $form_state);
    $this->assertEquals([
      '#type' => 'select',
      '#options' => [
        'in_discussion' => 'In Discussion',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
      ],
      '#default_value' => 'in_discussion',
    ], $form['state']);

    // Test with an existing status.
    $node->field_status->state = 'approved';
    $form = $widget->formElement($node->field_status, 0, $empty, $empty, $form_state);
    $this->assertEquals([
      '#type' => 'select',
      '#options' => [
        'approved' => 'Approved',
        'planning' => 'Planning',
      ],
      '#default_value' => 'approved',
    ], $form['state']);
  }

  /**
   * @covers \Drupal\workflows_field\Plugin\Field\FieldType\WorkflowsFieldItem
   */
  public function testFieldType() {
    $node = Node::create([
      'title' => 'Foo',
      'type' => 'project',
      'field_status' => 'in_discussion',
    ]);
    $node->save();

    // Test the dependencies calculation.
    $this->assertEquals([
      'config' => [
        'workflows.workflow.bureaucracy_workflow',
      ],
    ], WorkflowsFieldItem::calculateStorageDependencies($node->field_status->getFieldDefinition()->getFieldStorageDefinition()));

    // Test the getWorkflow method.
    $this->assertEquals('bureaucracy_workflow', $node->field_status[0]->getWorkflow()->id());
  }

  /**
   * @covers \Drupal\workflows_field\Plugin\WorkflowType\WorkflowsField
   */
  public function testWorkflowType() {
    // Test the initial state based on the config, despite the state weights.
    $type = Workflow::load('bureaucracy_workflow')->getTypePlugin();
    $this->assertEquals('in_discussion', $type->getInitialState()->id());
  }

}
