<?php

namespace Drupal\chubb_life\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\chubb_life\Controller\CustomerController;
use Drupal\chubb_life\Controller\AssigneeController;
use Drupal\chubb_life\Controller\CallController;

/**
 * Class ListCallLogForm.
 */
class ListCallLogForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'list_call_log_form';
  }
  public $call_id;
  public $import_customer_id;
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $call_id = NULL) {
    if (isset($call_id)) {
      $this->call_id = $call_id;
    }
    $db_call = CallController::get_call_by_id($call_id);
    $this->import_customer_id = $db_call['import_customer_id'];
    $import_customer = CustomerController::get_import_customer_by_id($db_call['import_customer_id']);
    $header_table['fid'] = t('Batch');
    $header_table['cust_ref'] = t('Ref No.');
    $header_table['name'] = t('Name');
    $header_table['tel_mbl'] = t('Mobile');
    $header_table['assignee'] = t('Assignee');
    $header_table['dial_time'] = t('Time');
    $rows=array();
    $call_log_list = CallController::list_call_log($this->call_id);
    foreach($call_log_list as $key=>$data){
      $user = \Drupal\user\Entity\User::load($data->assignee_id); // pass your uid
      $agent_code = $user->field_agentcode->value;
      $row_data['fid'] = $import_customer['fid'];
      $row_data['cust_ref'] = $import_customer['cust_ref'];
      $row_data['name'] = $import_customer['name'];
      $row_data['tel_mbl'] = $import_customer['tel_mbl'];
      $row_data['assignee'] = $user->getEmail();
      if(!empty($agent_code)){
        $row_data['assignee'] = $agent_code;
      }
      $row_data['dial_time'] = date('Y-m-d H:i:s',$data->dial_time);
      $rows[$data->id] = $row_data;
    }
    $form['call_filter'] = [
      '#type'  => 'details',
      '#title' => $this->t('Call Log'),
      '#open'  => true,
      '#weight' => '2',
    ];
    $form['call_filter']['call_log_list_table'] = [
      '#type' => 'table',
      '#header' => $header_table,
      '#rows' => $rows,
      '#empty' => t('No Customer found'),
      '#attributes' => [   
        'class' => ['call_log_list','table_list_data'],
        'col_sort_index' => 1,
        // 'col_sort_type' => 'asc',
      ],
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
    // Display result.
    foreach ($form_state->getValues() as $key => $value) {
      \Drupal::messenger()->addMessage($key . ': ' . ($key === 'text_format'?$value['value']:$value));
    }
  }

}
