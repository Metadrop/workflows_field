<?php

namespace Drupal\Tests\workflows_field\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Base class for testing.
 *
 * @group workflows_field
 */
abstract class WorkflowsTestBase extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'system',
    'user',
    'node',
    'options',
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

}
