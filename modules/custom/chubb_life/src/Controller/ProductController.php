<?php

namespace Drupal\chubb_life\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Utility\Tags;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\chubb_life\Ajax\AjaxCommand;

/**
 * Class ProductController.
 */
class ProductController extends ControllerBase {

  /**
   * Qwe.
   *
   * @return string
   *   Return Hello string.
   */
  public static function list_products(){
    $connection = Database::getConnection();
    $query = $connection->select('mtrc_premium', 'mp');
    $query->fields('mp');
    $record = $query->execute()->fetchAll();
    return $record;
  }
  public static function ajax_datatable_list_products(){
    $results = [];
    $results['draw'] = 1;
    $premium_list = self::list_products();
    $results['recordsTotal'] = count($premium_list);
    $results['recordsFiltered'] = count($premium_list);
    foreach($premium_list as $key => $data){
      $results['data'][] = [
        $data->plan_code,
        $data->plan_level,
        $data->smokers_code,
        $data->gender,
        $data->age,
        $data->currency,
        $data->premium,
      ];
    }
    return new JsonResponse($results);
  }
  public static function get_plan_code_options() {
    $connection = Database::getConnection();
    $query = $connection->select('mtrc_premium', 'mp');
    $query->fields('mp',['plan_code']);
    $record = $query->distinct()->execute()->fetchAll();
    $results = [];
    foreach ($record as $each_data) {
      $results[$each_data->plan_code] = $each_data->plan_code;
    }
    return $results;
  }
  
  public static function get_plan_level_options() {
    $connection = Database::getConnection();
    $query = $connection->select('mtrc_premium', 'mp');
    $query->fields('mp',['plan_code','plan_level']);
    $record = $query->distinct()->execute()->fetchAll();
    $results = [];
    foreach ($record as $each_data) {
      $results[$each_data->plan_code][$each_data->plan_level] = $each_data->plan_level;
    }
    return $results;
  }
  public static function ajax_get_premium($plan_code,$plan_level,$smoker,$gender,$age,$currency) {
    $connection = Database::getConnection();
    $query = $connection->select('mtrc_premium', 'mp');
    $query->fields('mp');
    $query->condition('plan_code', $plan_code);
    $query->condition('plan_level', $plan_level);
    $query->condition('smokers_code', $smoker);
    $query->condition('gender', $gender);
    $query->condition('age', $age);
    $query->condition('currency', $currency);
    $record = $query->execute()->fetchAssoc();
    if (isset($record['id'])) {
      $status = true;
      $result = $record['premium'];
      $message = 'Premium Found';
    }else{
      $status = false;
      $result = null;
      $message = 'Premium Not Found';
    }
    $response = new AjaxResponse();
    $response->addCommand( new AjaxCommand('ajax_get_premium',$status,$result,$message));
    return $response;
  }
}
