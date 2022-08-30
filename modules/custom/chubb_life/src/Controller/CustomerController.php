<?php

namespace Drupal\chubb_life\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class CustomerController.
 */
class CustomerController extends ControllerBase {

  /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   */
  public function hello($name) {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: hello with parameter(s): $name'),
    ];
  }

}
