<?php

namespace Drupal\datatables\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the datatables module.
 */
class SSPControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "datatables SSPController's controller functionality",
      'description' => 'Test Unit for module datatables and controller SSPController.',
      'group' => 'Other',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Tests datatables functionality.
   */
  public function testSSPController() {
    // Check that the basic functions of module datatables.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
