<?php

namespace Drupal\development\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the development module.
 */
class DeveloperControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "development DeveloperController's controller functionality",
      'description' => 'Test Unit for module development and controller DeveloperController.',
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
   * Tests development functionality.
   */
  public function testDeveloperController() {
    // Check that the basic functions of module development.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
