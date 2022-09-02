<?php

namespace Drupal\chubb_life\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\chubb_life\Controller\AttributeController;

/**
 * Class OrderController.
 */
class OrderController extends ControllerBase {

  /**
   * Werew.
   *
   * @return string
   *   Return Hello string.
   */
  public static function check_order_existed($order_id){
    $connection = Database::getConnection();
    $query = $connection->select('mtrc_order', 'mo');
    $query->fields('mo');
    $query->condition('id', $order_id);
    $record = $query->execute()->fetchAssoc();
    if (isset($record['id'])) {
      return $record['id'];
    }
    return false;
  }
  public static function get_order_by_id($order_id){
    $connection = Database::getConnection();
    $query = $connection->select('mtrc_order', 'mo');
    $query->fields('mo');
    $query->condition('id', $order_id);
    $record = $query->execute()->fetchAssoc();
    return $record;
  }
  public static function list_order(){
    $connection = Database::getConnection();
    $query = $connection->select('mtrc_order', 'mo');
    $query->fields('mo');
    $record = $query->execute()->fetchAll();
    return $record;
  }

  public static function update_order($order){
    $connection = Database::getConnection();
    $db_record = self::check_order_existed($order['id']);
    $order['updated_at'] = time();
    $order['updated_by'] = \Drupal::currentUser()->id();
    if($db_record){
      $connection->update('mtrc_order')
        ->fields($order)
        ->condition('id', $db_record)
        ->execute();
    }else{
      $call_insert_id = $connection->insert('mtrc_order')
        ->fields($order)
        ->execute();
    }
  }

}
