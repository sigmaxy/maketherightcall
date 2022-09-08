<?php

namespace Drupal\development\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;

use Drupal\api\Controller\APIController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

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
      case 'phpinfo':
        self::php_info();exit;
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
    echo 'sigma';exit;
    exit;
  }
  public static function php_info(){
    phpinfo();
  }
  public static function clear_data(){
    $connection = Database::getConnection();
    $query = $connection->truncate('mtrc_call')->execute();
    $query = $connection->truncate('mtrc_call_log')->execute();
    $query = $connection->truncate('mtrc_customer_import')->execute();
    $query = $connection->truncate('mtrc_order')->execute();
    $query = $connection->truncate('mtrc_order_client')->execute();
    echo 'call/call_log/import_customer/order/order_client data clear';exit;
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
      if ($key>=2 && !empty($row_data[1])) {
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
