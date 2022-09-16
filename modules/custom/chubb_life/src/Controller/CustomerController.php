<?php

namespace Drupal\chubb_life\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\chubb_life\Controller\AttributeController;

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
  public static function check_import_customer_existed($cust_ref){
    $connection = Database::getConnection();
    $query = $connection->select('mtrc_customer_import', 'mci');
    $query->fields('mci');
    $query->condition('cust_ref', $cust_ref);
    $record = $query->execute()->fetchAssoc();
    if (isset($record['id'])) {
      return $record['id'];
    }
    return false;
  }
  public static function get_import_customer_by_id($id){
    $connection = Database::getConnection();
    $query = $connection->select('mtrc_customer_import', 'mci')
      ->condition('id', $id)
      ->fields('mci');
    $record = $query->execute()->fetchAssoc();
    return $record;
  }
  public static function list_import_customer(){
    $connection = Database::getConnection();
    $query = $connection->select('mtrc_customer_import', 'mci');
    $query->fields('mci');
    $record = $query->execute()->fetchAll();
    return $record;
  }
  public static function update_import_customer($customer){
    $connection = Database::getConnection();
    $db_fields_ic = $customer;
    $db_record = self::check_import_customer_existed($customer['cust_ref']);
    if($db_record){
      $db_fields_ic['updated_at'] = time();
      $db_fields_ic['updated_by'] = \Drupal::currentUser()->id();
      $connection->update('mtrc_customer_import')
        ->fields($db_fields_ic)
        ->condition('id', $db_record)
        ->execute();
    }else{
      $db_fields_ic['created_at'] = time();
      $db_fields_ic['created_by'] = \Drupal::currentUser()->id();
      $db_fields_ic['updated_at'] = time();
      $db_fields_ic['updated_by'] = \Drupal::currentUser()->id();
      $import_customer_insert_id = $connection->insert('mtrc_customer_import')
        ->fields($db_fields_ic)
        ->execute();
    }
  }
  public static function delete_customer_by_id($imported_customer_id){
    $connection = Database::getConnection();
    $connection->delete('mtrc_customer_import')
      ->condition('id', $imported_customer_id)
      ->execute();
    $connection->delete('mtrc_call')
      ->condition('import_customer_id', $imported_customer_id)
      ->execute();
  }
}
