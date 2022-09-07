<?php

namespace Drupal\chubb_life\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\chubb_life\Controller\OrderController;
use Drupal\Core\Url;
use Drupal\Core\Link;
/**
 * Class ListOrderForm.
 */
class ListOrderForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'list_order_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $header_table['referenceNumber'] = t('Ref Num');
    $header_table['last_name'] = t('Last Name');
    $header_table['first_name'] = t('First Name');
    $header_table['mobile'] = t('Mobile');
    $header_table['plan_code'] = t('Plan Code');
    $header_table['updated_at'] = t('Updated At');
    $header_table['opt'] = t('Operation');
    $rows=array();
    $roles = \Drupal::currentUser()->getRoles();
    $uid = \Drupal::currentUser()->id();
    if(in_array('manager', $roles)||in_array('administrator', $roles)) {
      $order_list = OrderController::list_order(null);
    }else{
      $order_list = OrderController::list_order($uid);
    }
    foreach($order_list as $key=>$data){
      $edit   = Url::fromUserInput('/chubb_life/form/edit_order/'.$data->id);
      $client_owner = OrderController::get_order_client_by_type($data->id,1);
      $row_data['referenceNumber'] = sprintf('TM%06d',$data->id);
      $row_data['last_name'] = $client_owner['surname'];
      $row_data['first_name'] = $client_owner['givenName'];
      $row_data['mobile'] = $client_owner['mobile'];
      $row_data['plan_code'] = $data->plan_code;
      $row_data['updated_at'] = date('Y-m-d H:i:s',$data->updated_at);
      $row_data['opt'] = Link::fromTextAndUrl('Edit', $edit);
      $rows[$data->id] = $row_data;
    }
    $form['order_filter'] = [
      '#type'  => 'details',
      '#title' => $this->t('Sales Order List'),
      '#open'  => true,
      '#weight' => '2',
    ];
    $form['order_filter']['order_list_table'] = [
      '#type' => 'tableselect',
      '#header' => $header_table,
      // '#rows' => $rows,
      '#options' => $rows,
      '#empty' => t('No Customer found'),
      '#attributes' => [   
        'class' => ['table_list_data'],
        'col_sort_index' => 6,
        'col_sort_type' => 'desc',
      ],
    ];
    $form['function_filters'] = [
      '#type'  => 'details',
      '#title' => $this->t('Function'),
      '#open'  => true,
      '#weight' => '3',
    ];
    $form['function_filters']['assign_customer'] = [
      '#type' => 'submit',
      '#value' => $this->t('Generate Json'),
      '#attributes' => [   
        'class' => ['next_button'],
      ],
      '#weight' => '2',
    ];
    // $form['#attributes'] = array('class' => 'wide_form');
    $form['#attached']['library'][] = 'chubb_life/chubb_life';
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    foreach ($form_state->getValues() as $key => $value) {
      // @TODO: Validate fields.
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $order_selected = $form_state->getValue('order_list_table');
    $json_arr = array(
      'batchDate' => date("Y-m-d"),
      'applications' => array(),
    );
    foreach ($order_selected as $order_id => $checked) {
      if ($checked) {
        $call = array();
        $db_order = OrderController::get_order_by_id($order_id);
        $one_json_record = OrderController::order_format_json($db_order);
        $json_arr['applications'][] = $one_json_record;
      }
    }
    $json_file_name = 'TM_APP_'.date('YmdHis').'.txt';
    $json_file_path = 'public://temp/'.$json_file_name;
    $file = file_save_data(json_encode($json_arr,JSON_PRETTY_PRINT), $json_file_path, 1);
    $link = file_create_url($json_file_path); 
    \Drupal::messenger()->addMessage(t('Download json File <a href="@link" target="_blank">Click Here</a>', array('@link' => $link)));
  }

}
