<?php

namespace Drupal\chubb_life\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class AttributeController.
 */
class AttributeController extends ControllerBase {

  /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   */
  public static function get_list() {
    $connection = Database::getConnection();
    $query = $connection->select('f4f_mapping_license_relation', 'fmlr');
    $query->fields('fmlr');
    $record = $query->execute()->fetchAll();
    return $record;
  }

}
