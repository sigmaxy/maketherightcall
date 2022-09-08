<?php

namespace Drupal\chubb_life\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Utility\Tags;

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
  public static function autocomplete_product_plan_code(Request $request, $count) {
    $results = [];
    $connection = Database::getConnection();
    $query = $connection->select('mtrc_premium', 'mp');
    $query->fields('mp',['plan_code']);
    $record = $query->distinct()->execute()->fetchAll();
    print_r($record);
    
    
    exit;



    // Get the typed string from the URL, if it exists.
    if ($input = $request->query->get('q')) {
      $typed_string = Tags::explode($input);
      $typed_string = mb_strtolower(array_pop($typed_string));
      $connection = Database::getConnection();
      $query = $connection->select('mtrc_premium', 'mp');
      $query->fields('mp');
      $query->condition('plan_code', '%'.$typed_string.'%','LIKE');
      $query->distinct()->groupBy('mp.plan_code')->orderBy('mp.plan_code');
      $records = $query->execute()->fetchAll();
      $i = 0;
      foreach ($records as $each_data) {
        if ($i< $count) {
          $results[] = [
            'value' => $each_data->plan_code,
            'label' => $each_data->plan_code,
          ];
          $i++;
        }
        
      }
    }
    return new JsonResponse($results);
  }
}
