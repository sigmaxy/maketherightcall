<?php

namespace Drupal\chubb_life\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the chubb_life module.
 */
class ProductControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "chubb_life ProductController's controller functionality",
      'description' => 'Test Unit for module chubb_life and controller ProductController.',
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
  public function testProductController() {
    // Check that the basic functions of module chubb_life.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
