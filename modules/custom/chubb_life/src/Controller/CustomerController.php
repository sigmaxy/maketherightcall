<?php

namespace Drupal\chubb_life\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\chubb_life\Controller\AttributeController;
use Drupal\chubb_life\Controller\CallController;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Utility\Tags;

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
  public static function list_import_customer_pager($conditions){
    $connection = Database::getConnection();
    $query = $connection->select('view_mtrc_customer_call', 'vmcc');
    if(isset($conditions['created_at'])){
      $startdate = strtotime($conditions['created_at']);
      $enddate = strtotime("+1 day", $startdate);
      $query->condition('created_at', [$startdate,$enddate], 'BETWEEN');
      unset($conditions['created_at']);
    }
    if(isset($conditions['status'])&&$conditions['status']=='null'){
      $query->condition('status', NULL, 'IS NULL');
      unset($conditions['status']);
    }
    if(!empty($conditions)){
      foreach ($conditions as $key => $value) {
        $query->condition($key, '%' . $value . '%', 'LIKE');
      }
    }
    $query->fields('vmcc');
    \Drupal::logger('chubb_life')->notice('list customer 1: '.date('H:i:s'));
    $page_display = 10;
    $query = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit(10);
    // $query->extend('Drupal\Core\Database\Query\PagerSelectExtender')->range(0,10);
    // $query = $query->range(0,10);
    \Drupal::logger('chubb_life')->notice('list customer 2: '.date('H:i:s'));
    $record = $query->execute()->fetchAll();
    \Drupal::logger('chubb_life')->notice('list customer 3: '.date('H:i:s'));
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
  public static function ase_encrypt($plaintext){
    $ivlen = openssl_cipher_iv_length($cipher="AES-256-CBC");
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
    $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
    $ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );
    return $ciphertext;
  }
  public static function ase_decrypt($plaintext){
    $c = base64_decode($ciphertext);
    $ivlen = openssl_cipher_iv_length($cipher="AES-256-CBC");
    $iv = substr($c, 0, $ivlen);
    $hmac = substr($c, $ivlen, $sha2len=32);
    $ciphertext_raw = substr($c, $ivlen+$sha2len);
    $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
    $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
    if (hash_equals($hmac, $calcmac))// timing attack safe comparison
    {
        return $original_plaintext;
    }else{
      return null;
    }
  }
  public static function update_import_customer_happy_client($customer){
    $connection = Database::getConnection();
    $db_fields_ic = $customer;
    $encrypt_db_field = array();
    foreach ($db_fields_ic as $key => $data) {
      $encrypt_db_field[$key]= self::ase_encrypt($data);
    }
    $db_fields_ic['created_at'] = time();
    $db_fields_ic['created_by'] = \Drupal::currentUser()->id();
    $db_fields_ic['updated_at'] = time();
    $db_fields_ic['updated_by'] = \Drupal::currentUser()->id();
    $import_customer_insert_id = $connection->insert('mtrc_happy_client')
      ->fields($encrypt_db_field)
      ->execute();
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

  public static function sample_pdo_function(){
    //professional way
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
    }


    //beginer
    $servername = "localhost";
    $username = "username";
    $password = "password";
    $dbname = "mtrc";
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT id, firstname, lastname FROM mtrc_happy_client";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      // output data of each row
      while($row = $result->fetch_assoc()) {
        echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
      }
    } else {
      echo "0 results";
    }
    $conn->close();


  }
  public static function ajax_datatable_list_customer1(){
    $results = [];
    $results['draw'] = 1;
    $customer_list = self::list_import_customer();
    $results['recordsTotal'] = count($customer_list);
    $results['recordsFiltered'] = count($customer_list);
    foreach($customer_list as $key => $data){
      $results['data'][] = [
        $data->id,
        $data->cust_ref,
        $data->name,
        $data->gender,
        $data->tel_mbl,
        '',
        $data->fid,
        '',
        $data->created_at,
        $data->updated_by,
      ];
    }
    return new JsonResponse($results);
  }

  public static function ajax_datatable_list_customer() {
    $results = [];
    $results['draw'] = 1;
    $customer_list = self::list_import_customer();
    $results['recordsTotal'] = count($customer_list);
    $results['recordsFiltered'] = count($customer_list);
    $call_status_opt = AttributeController::get_call_status_options();



    foreach($customer_list as $key=>$data){
      // $edit   = Url::fromUserInput('/chubb_life/form/editcall/'.$data->id);
      $db_call = CallController::get_call_by_import_customer_id($data->id);
      if (isset($db_call['id'])) {
        $row_data['status'] = $call_status_opt[$db_call['status']];
        $user = \Drupal\user\Entity\User::load($db_call['assignee_id']);
        $agent_code = $user->field_agentcode->value;
        $row_data['assignee'] = $user->getEmail();
        if(!empty($agent_code)){
          $row_data['assignee'] = $agent_code;
        }
      }else{
        $row_data['status'] = 'Not Assigned';
        $row_data['assignee'] = '';
      }
      $row_data['cust_ref'] = $data->cust_ref;
      $row_data['name'] = $data->name;
      $row_data['gender'] = $data->gender;
      $row_data['tel_mbl'] = $data->tel_mbl;
      $row_data['fid'] = $data->fid;
      $row_data['created_at'] = date('Y-m-d',$data->created_at);
      $updated_user = \Drupal\user\Entity\User::load($data->updated_by);
      $row_data['updated_by'] = $updated_user->field_agentname->value;
      

      $results['data'][] = [
        'id'=>$data->id,
        'cust_ref'=>$data->cust_ref,
        'name'=>$data->name,
        'gender'=>$data->gender,
        'tel_mbl'=>$data->tel_mbl,
        'status'=>$row_data['status'],
        'fid'=>$data->fid,
        'assignee'=>$row_data['assignee'],
        'created_at'=>date('Y-m-d',$data->created_at),
        'updated_by'=>$updated_user->field_agentname->value,
      ];
    }
    return new JsonResponse($results);
  }
}
