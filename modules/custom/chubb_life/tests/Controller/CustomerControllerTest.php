<?php

namespace Drupal\chubb_life\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the chubb_life module.
 */
class CustomerControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "chubb_life CustomerController's controller functionality",
      'description' => 'Test Unit for module chubb_life and controller CustomerController.',
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
   * Tests chubb_life functionality.
   */
  public function testCustomerController() {
    // Check that the basic functions of module chubb_life.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
