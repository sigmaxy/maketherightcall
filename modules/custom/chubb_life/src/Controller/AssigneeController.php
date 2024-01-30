<?php

namespace Drupal\chubb_life\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Utility\Tags;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AlertCommand;
use Drupal\user\Entity\User;

/**
 * Class AssigneeController.
 */
class AssigneeController extends ControllerBase {

  /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   */
  public static function list_assignee() {
    $userlist = [];
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id()); // pass your uid
    $teams = [];
    foreach ($user->get('field_team')->getValue() as $key => $value) {
      $teams[] = $value['value'];
    }
    $ids = \Drupal::entityQuery('user')
      ->condition('status', 1)
      ->condition('field_team', $teams, 'IN')
      ->execute();
    $users = User::loadMultiple($ids);
    foreach($users as $user){
      $uid = $user->get('uid')->getString();
      $mail =  $user->get('mail')->getString();
      $agent_code = $user->field_agentcode->value;
      if(!empty($agent_code)){
        $userlist[$uid] = $agent_code;
      }else{
        $userlist[$uid] = $mail;
      }
      
    }
    return $userlist;
  }
  // public static function autocomplete_assignee(Request $request, $count) {
    

  //   // Get the typed string from the URL, if it exists.
  //   if ($input = $request->query->get('q')) {
  //     $typed_string = Tags::explode($input);
  //     $typed_string = mb_strtolower(array_pop($typed_string));
  //     $connection = Database::getConnection();
  //     $query = $connection->select('f4f_product_simple', 'fps');
  //     $query->fields('fps');
  //     $query->condition('sku', '%'.$typed_string.'%','LIKE');
  //     $records = $query->execute()->fetchAll();
  //     $i = 0;
  //     foreach ($records as $each_data) {
  //       if ($i< $count) {
  //         $results[] = [
  //           'value' => $each_data->sku,
  //           'label' => $each_data->sku,
  //         ];
  //         $i++;
  //       }
        
  //     }
  //   }
  //   return new JsonResponse($results);
  // }
}
