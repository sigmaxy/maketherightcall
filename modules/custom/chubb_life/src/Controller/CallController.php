<?php

namespace Drupal\chubb_life\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\chubb_life\Controller\AttributeController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AlertCommand;
use Drupal\chubb_life\Ajax\AjaxCommand;
use Drupal\datatables\Controller\SSPController;

/**
 * Class CallController.
 */
class CallController extends ControllerBase {

  /**
   * Qwe.
   *
   * @return string
   *   Return Hello string.
   */
  public static function check_call_existed($import_customer_id){
    $connection = Database::getConnection();
    $query = $connection->select('mtrc_call', 'mc');
    $query->fields('mc');
    $query->condition('import_customer_id', $import_customer_id);
    $record = $query->execute()->fetchAssoc();
    if (isset($record['id'])) {
      return $record['id'];
    }
    return false;
  }
  public static function get_call_by_id($call_id){
    $connection = Database::getConnection();
    $query = $connection->select('mtrc_call', 'mc');
    $query->fields('mc');
    $query->condition('id', $call_id);
    $record = $query->execute()->fetchAssoc();
    return $record;
  }
  public static function get_call_by_import_customer_id($customer_id){
    $connection = Database::getConnection();
    $query = $connection->select('mtrc_call', 'mc');
    $query->fields('mc');
    $query->condition('import_customer_id', $customer_id);
    $record = $query->execute()->fetchAssoc();
    return $record;
  }
  public static function list_call(){
    $connection = Database::getConnection();
    $query = $connection->select('mtrc_call', 'mc');
    $query->fields('mc');
    $record = $query->execute()->fetchAll();
    return $record;
  }
  public static function list_call_pager($conditions){
    $connection = Database::getConnection();
    $query = $connection->select('mtrc_call', 'mc');
    $query->fields('mc');
    $query->leftJoin('mtrc_customer_import', 'mci', 'mci.id = mc.import_customer_id');

    if(isset($conditions['assignee_id'])){
      $query->condition('assignee_id', $conditions['assignee_id']);
      unset($conditions['assignee_id']);
    }
    if(isset($conditions['updated_at'])){
      $startdate = strtotime($conditions['updated_at']);
      $enddate = strtotime("+1 day", $startdate);
      $query->condition('updated_at', [$startdate,$enddate], 'BETWEEN');
      unset($conditions['updated_at']);
    }
    if(!empty($conditions)){
      foreach ($conditions as $key => $value) {
        $query->condition($key, '%' . $value . '%', 'LIKE');
      }
    }
    $query->orderBy('id', 'DESC');
    $query = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit(10);
    $record = $query->execute()->fetchAll();
    return $record;
  }
  public static function list_call_by_assignee($assignee_id){
    $connection = Database::getConnection();
    $query = $connection->select('mtrc_call', 'mc');
    $query->fields('mc');
    if(isset($assignee_id)){
      $query->condition('assignee_id', $assignee_id);
    }
    $record = $query->execute()->fetchAll();
    return $record;
  }
  public static function list_call_by_assignee_view($pager,$conditions){
    $connection = Database::getConnection();
    $query = $connection->select('view_mtrc_call_customer', 'vmcc');
    $query->fields('vmcc');
    if(isset($conditions['assignee_id'])){
      $query->condition('assignee_id', $conditions['assignee_id']);
      unset($conditions['assignee_id']);
    }
    if(isset($conditions['updated_at'])){
      $startdate = strtotime($conditions['updated_at']);
      $enddate = strtotime("+1 day", $startdate);
      $query->condition('updated_at', [$startdate,$enddate], 'BETWEEN');
      unset($conditions['updated_at']);
    }
    if(!empty($conditions)){
      foreach ($conditions as $key => $value) {
        $query->condition($key, '%' . $value . '%', 'LIKE');
      }
    }
    $pager = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit(10);
    $record = $pager->execute()->fetchAll();
    return $record;
  }
  public static function update_call($call){
    $connection = Database::getConnection();
    $db_fields_c = $call;
    $db_record = self::check_call_existed($call['import_customer_id']);
    $db_fields_c['updated_at'] = time();
    $db_fields_c['updated_by'] = \Drupal::currentUser()->id();
    if($db_record){
      $connection->update('mtrc_call')
        ->fields($db_fields_c)
        ->condition('id', $db_record)
        ->execute();
    }else{
      $db_fields_c['count'] = 0;
      $db_fields_ic['status'] = 1;
      $call_insert_id = $connection->insert('mtrc_call')
        ->fields($db_fields_c)
        ->execute();
    }
  }

  public static function make_call($call_id){
    $connection = Database::getConnection();
    $db_record = self::get_call_by_id($call_id);
    $db_record['count'] = $db_record['count'] + 1;
    $connection->update('mtrc_call')
      ->fields($db_record)
      ->condition('id', $call_id)
      ->execute();
    $call_log_fields = [
      'call_id' => $call_id,
      'assignee_id' => \Drupal::currentUser()->id(),
      'dial_time' => time(),
    ];
    $call_log_insert_id = $connection->insert('mtrc_call_log')
      ->fields($call_log_fields)
      ->execute();
  }
  public static function ajax_call_log($call_id) {
    $connection = Database::getConnection();
    $query = $connection->select('mtrc_call_log', 'mcl');
    $query->fields('mcl');
    $query->condition('call_id', $call_id);
    $record = $query->execute()->fetchAll();
    $results = [];
    $results['call_id'] = $call_id;
    foreach($record as $key => $data){
      $results['data'][] = [
        'assignee_id'=>$data->assignee_id,
        'dial_time'=>$data->dial_time,
      ];
    }
    $status = true;
    $message = 'Call Log Found';
     // Create AJAX Response object.
    $response = new AjaxResponse();
     // Call the readMessage javascript function.
    $response->addCommand( new AjaxCommand('ajax_call_log',$status,$results,$message));
    // Return ajax response.
    return $response;
  }
  public static function list_call_log($call_id) {
    $connection = Database::getConnection();
    $query = $connection->select('mtrc_call_log', 'mcl');
    $query->fields('mcl');
    $query->condition('call_id', $call_id);
    $record = $query->execute()->fetchAll();
    return $record;
  }
  public static function ajax_datatable_list_call2(){
    $results = [];
    $table = 'view_mtrc_call_customer_ajax';
    $primaryKey = 'id';
    $columns = array(
      array( 'db' => 'fid', 'dt' => 0 ),
      array( 'db' => 'cust_ref', 'dt' => 1 ),
      array( 'db' => 'name',  'dt' => 2 ),
      array( 'db' => 'gender',   'dt' => 3 ),
      array( 'db' => 'tel_mbl',     'dt' => 4 ),
      array( 'db' => 'status',     'dt' => 5 ),
      array(
        'db'        => 'count',
        'dt'        => 6,
        'formatter' => function( $d, $row ) {
          return '<a href="/chubb_life/form/list_call_log/'.$row['id'].'">'.$d.'</a>';
        }
      ),
      array( 'db' => 'assignee',     'dt' => 7 ),
      array( 'db' => 'updated_at',     'dt' => 8 ),
      array( 'db' => 'updated_by_name',     'dt' => 9 ),
      array(
        'db'        => 'id',
        'dt'        => 10,
        'formatter' => function( $d, $row ) {
          return '<a href="/chubb_life/form/edit_call/'.$d.'">View</a>';
        }
      ),
    );
    $roles = \Drupal::currentUser()->getRoles();
    $current_uid = \Drupal::currentUser()->id();
    if(in_array('manager', $roles)||in_array('administrator', $roles)) {
      $results = SSPController::simple($table, $primaryKey, $columns);
    }else{
      $where = "assignee_id=".$current_uid;
      $results = SSPController::complex($table, $primaryKey, $columns, null, $where);
    }
    return new JsonResponse($results);
  }

}
