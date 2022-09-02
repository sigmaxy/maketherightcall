<?php

namespace Drupal\chubb_life\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\chubb_life\Controller\OrderController;

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
    $header_table['last_name'] = t('Last Name');
    $header_table['first_name'] = t('First Name');
    $header_table['mobile'] = t('Mobile');
    $header_table['plan_code'] = t('Plan Code');
    $header_table['created_at'] = t('Created At');
    $rows=array();
    $order_list = OrderController::list_order();
    foreach($order_list as $key=>$data){
      // $edit   = Url::fromUserInput('/chubb_life/form/editcall/'.$data->id);
      $row_data['last_name'] = $data->last_name;
      $row_data['first_name'] = $data->first_name;
      $row_data['mobile'] = $data->mobile;
      $row_data['plan_code'] = $data->plan_code;
      $row_data['created_at'] = date('Y-m-d',$data->created_at);
      
      // $row_data['opt'] = Link::fromTextAndUrl('Edit', $edit);
      $rows[$data->id] = $row_data;
    }
    $form['order_filter'] = [
      '#type'  => 'fieldset',
      '#title' => $this->t('Import Customer'),
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
        'col_sort_index' => 5,
        // 'col_sort_type' => 'asc',
      ],
    ];
    $form['function_filters'] = [
      '#type'  => 'fieldset',
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
    $json_arr = array();
    foreach ($order_selected as $order_id => $checked) {
      if ($checked) {
        $call = array();
        $db_order = OrderController::get_order_by_id($order_id);
        $json_arr[] = $db_order;
      }
    }
    $contents = json_encode($json_arr);
    $json_file_name = 'smart_'.time().'.txt';
    $json_file_path = 'public://temp/'.$json_file_name;
    $file = file_save_data(json_encode($json_arr), $json_file_path, 1);
    $link = file_create_url($json_file_path); 
    \Drupal::messenger()->addMessage(t('Download json File <a href="@link">Right Click and Save Link As</a>', array('@link' => $link)));
  }

}
