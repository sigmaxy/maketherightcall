<?php

namespace Drupal\chubb_life\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\chubb_life\Controller\AttributeController;
use Drupal\chubb_life\Controller\CustomerController;
use Drupal\chubb_life\Controller\AssigneeController;
use Drupal\chubb_life\Controller\CallController;
use Drupal\development\Controller\DeveloperController;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Class ListCustomerForm.
 */
class ListCustomerForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'list_customer_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $conditions = array();
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id()); // pass your uid
    $teams = [];
    foreach ($user->get('field_team')->getValue() as $key => $value) {
      $teams[] = $value['value'];
    }
    $conditions['teams'] = $teams;
    if(\Drupal::request()->query->get('fid')){
      $conditions['fid']=\Drupal::request()->query->get('fid');
    }
    if(\Drupal::request()->query->get('name')){
      $conditions['name']=\Drupal::request()->query->get('name');
    }
    if(\Drupal::request()->query->get('cust_ref')){
      $conditions['cust_ref']=\Drupal::request()->query->get('cust_ref');
    }
    if(\Drupal::request()->query->get('tel_mbl')){
      $conditions['tel_mbl']=\Drupal::request()->query->get('tel_mbl');
    }
    if(\Drupal::request()->query->get('created_at_start')){
      $conditions['created_at_start']=\Drupal::request()->query->get('created_at_start');
    }
    if(\Drupal::request()->query->get('created_at_end')){
      $conditions['created_at_end']=\Drupal::request()->query->get('created_at_end');
    }
    if(\Drupal::request()->query->get('assignee_id')){
      $conditions['assignee_id']=\Drupal::request()->query->get('assignee_id');
    }
    if(\Drupal::request()->query->get('status')){
      $conditions['status']=\Drupal::request()->query->get('status');
    }
    if(\Drupal::request()->query->get('record_per_page')){
      $conditions['record_per_page']=\Drupal::request()->query->get('record_per_page');
    }
    $header_table['cust_ref'] = t('Ref No.');
    $header_table['name'] = t('Name');
    $header_table['gender'] = t('Gender');
    $header_table['tel_mbl'] = t('Mobile');
    $header_table['status'] = t('Status');
    $header_table['team'] = t('Team');
    $header_table['fid'] = t('Batch');
    $header_table['assignee'] = t('Assignee');
    $header_table['created_at'] = t('Created At');
    $header_table['updated_by'] = t('Updated By');
    $rows=array();
    $import_customer_list = CustomerController::list_import_customer_pager($conditions);

    $call_status_opt = AttributeController::get_call_status_options();
    $record_per_page_opt = AttributeController::get_record_per_page_options();
    $assignee_opts = AssigneeController::list_assignee();
    $filter_call_status_opt = $call_status_opt;
    $filter_call_status_opt['null'] = 'Not Assigned';
    // DeveloperController::running_check();
    foreach($import_customer_list as $key=>$data){
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
      $row_data['team'] = $data->team;
      $row_data['cust_ref'] = $data->cust_ref;
      $row_data['name'] = $data->name;
      $row_data['gender'] = $data->gender;
      $row_data['tel_mbl'] = $data->tel_mbl;
      $row_data['fid'] = $data->fid;
      $row_data['created_at'] = date('Y-m-d',$data->created_at);
      $updated_user = \Drupal\user\Entity\User::load($data->updated_by);
      $row_data['updated_by'] = $updated_user->field_agentname->value;
      
      // $row_data['opt'] = Link::fromTextAndUrl('Edit', $edit);
      $rows[$data->id] = $row_data;
    }
    $form['upload_filters'] = [
      '#type'  => 'details',
      '#title' => $this->t('Function'),
      '#open'  => true,
      '#weight' => '1',
    ];
    $form['upload_filters']['upload_customer'] = [
      '#type' => 'submit',
      '#value' => $this->t('Upload Customer'),
      '#attributes' => [   
        'class' => ['next_button'],
      ],
      '#submit' => array('::upload_customer'),
      '#weight' => '1',
    ];
    $form['import_customer_filter'] = [
      '#type'  => 'details',
      '#title' => $this->t('Import Customer'),
      '#open'  => true,
      '#weight' => '2',
    ];
    $form['import_customer_filter']['cust_ref'] = [
      '#type' => 'textfield',
      '#title' => 'Customer Ref',
      '#default_value' => isset($conditions['cust_ref'])?$conditions['cust_ref']:'',
      '#maxlength' => 255,
      '#weight' => '1',
    ];
    $form['import_customer_filter']['name'] = [
      '#type' => 'textfield',
      '#title' => 'Customer Name',
      '#default_value' => isset($conditions['name'])?$conditions['name']:'',
      '#maxlength' => 255,
      '#weight' => '2',
    ];
    $form['import_customer_filter']['fid'] = [
      '#type' => 'textfield',
      '#title' => 'Batch Num',
      '#default_value' => isset($conditions['fid'])?$conditions['fid']:'',
      '#maxlength' => 255,
      '#weight' => '3',
    ];
    $form['import_customer_filter']['tel_mbl'] = [
      '#type' => 'textfield',
      '#title' => 'Mobile',
      '#default_value' => isset($conditions['tel_mbl'])?$conditions['tel_mbl']:'',
      '#maxlength' => 255,
      '#weight' => '4',
    ];
    $form['import_customer_filter']['created_at_start'] = [
      '#type' => 'date',
      '#title' => 'Created At Start',
      '#default_value' => isset($conditions['created_at_start'])?$conditions['created_at_start']:'',
      '#maxlength' => 255,
      '#weight' => '5',
    ];
    $form['import_customer_filter']['created_at_end'] = [
      '#type' => 'date',
      '#title' => 'Created At End',
      '#default_value' => isset($conditions['created_at_end'])?$conditions['created_at_end']:'',
      '#maxlength' => 255,
      '#weight' => '6',
    ];
    $form['import_customer_filter']['assignee_id'] = [
      '#title' => $this->t('Assignee'),
      '#type' => 'select',
      '#options' => $assignee_opts,
      '#empty_option' => '--Select--',
      '#default_value' => isset($conditions['assignee_id'])?$conditions['assignee_id']:'',
      '#attributes' => [
        'class' => ['assignee_select_filter'],
      ],
      '#weight' => '7',
    ];
    $form['import_customer_filter']['status'] = [
      '#type' => 'select',
      '#title' => $this->t('Status'),
      '#options' => $filter_call_status_opt,
      '#empty_option' => '--Select--',
      '#default_value' => isset($conditions['status'])?$conditions['status']:'',
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '8',
    ];
    $form['import_customer_filter']['record_per_page'] = [
      '#type' => 'select',
      '#title' => $this->t('Record Per Page'),
      '#options' => $record_per_page_opt,
      // '#empty_option' => '--Select--',
      '#default_value' => isset($conditions['record_per_page'])?$conditions['record_per_page']:'10',
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '9',
    ];
    $form['import_customer_filter']['filter_customer'] = [
      '#type' => 'submit',
      '#value' => $this->t('Filter'),
      '#attributes' => [   
        'class' => ['next_button'],
      ],
      '#submit' => array('::filter_customer'),
      '#weight' => '10',
    ];
    $form['import_customer_filter']['clear_filter'] = [
      '#type' => 'submit',
      '#value' => $this->t('Clear'),
      '#attributes' => [   
        'class' => ['next_button'],
      ],
      '#submit' => array('::clear_filter'),
      '#weight' => '11',
    ];
    $form['import_customer_filter']['import_customer_list_table'] = [
      '#type' => 'tableselect',
      '#header' => $header_table,
      '#options' => $rows,
      '#empty' => t('No Customer found'),
      '#attributes' => [   
        'class' => ['import_customer_list1','traditional_data_tale'],
        'col_sort_index' => 8,
        'col_sort_type' => 'desc',
      ],
      '#weight' => '20',
    ];
    $form['import_customer_filter']['pager'] = array(
      '#type' => 'pager',
      '#weight' => '21',
    );
    $form['function_filters'] = [
      '#type'  => 'details',
      '#title' => $this->t('Function'),
      '#open'  => true,
      '#weight' => '3',
    ];
    
    $form['function_filters']['assignee'] = [
      '#title' => $this->t('Assignee'),
      '#type' => 'select',
      '#options' => $assignee_opts,
      '#attributes' => [
        'class' => ['assignee_select'],
      ],
      '#multiple' => TRUE,
      '#wrapper_attributes' => ['class' => ['form_item_maxwidth']],
      '#weight' => '1',
    ];
    $form['function_filters']['assign_customer'] = [
      '#type' => 'submit',
      '#value' => $this->t('Assign'),
      '#attributes' => [   
        'class' => ['next_button'],
      ],
      '#submit' => array('::assign_customer'),
      '#weight' => '2',
    ];
    $form['function_filters']['delete_customer'] = [
      '#type' => 'submit',
      '#value' => $this->t('Delete'),
      '#attributes' => [   
        'class' => ['next_button'],
      ],
      '#submit' => array('::delete_customer'),
      '#weight' => '3',
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
  public function assign_customer(array &$form, FormStateInterface $form_state) {
    // Display result.
    $assignee_uid = $form_state->getValue('assignee');
    
    
    $customer_selected = $form_state->getValue('import_customer_list_table');
    
    $customer_checked = array();
    foreach ($customer_selected as $imported_customer_id => $checked) {
      if ($checked) {
        $customer_checked[] = $imported_customer_id;
      }
    }
    $customer_count = count($customer_checked);

    $assignee_checked = array();
    foreach ($assignee_uid as $each_data) {
        $assignee_checked[] = $each_data;
    }
    $assignee_count = count($assignee_uid);

    $assignee_index = 0;
    $assignee_update_list = array();
    while (count($customer_checked) > 0) {
      $random_key = array_rand($customer_checked, 1);
      $assignee_update_list[$assignee_checked[$assignee_index]][] = $customer_checked[$random_key];
      unset($customer_checked[$random_key]);
      if($assignee_index < ($assignee_count - 1)){
        $assignee_index++;
      }else{
        $assignee_index=0;
      }
    }
    foreach ($assignee_update_list as $each_assignee_uid=>$assigned_customer_list) {
      foreach ($assigned_customer_list as $imported_customer_id) {
        // echo 'assignee id:'.$each_assignee_uid.' customer id:'.$imported_customer_id."\n";
        $call = array();
        $call['import_customer_id'] = $imported_customer_id;
        $call['assignee_id'] = $each_assignee_uid;
        $call['status'] = 1;
        CallController::update_call($call);
      }
    }
    \Drupal::messenger()->addMessage('Call has been assigned');
  }
  public function delete_customer(array &$form, FormStateInterface $form_state) {
    // Display result.
    $customer_selected = $form_state->getValue('import_customer_list_table');
    $assignee_uid = $form_state->getValue('assignee');
    foreach ($customer_selected as $imported_customer_id => $checked) {
      if ($checked) {
        CustomerController::delete_customer_by_id($imported_customer_id);
      }
    }
    \Drupal::messenger()->addMessage('Call has been assigned');
  }
  public function upload_customer(array &$form, FormStateInterface $form_state) {
    $url = Url::fromUserInput('/chubb_life/form/upload_customer');
    $form_state->setRedirectUrl($url);
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
  public function filter_customer(array &$form, FormStateInterface $form_state) {
    $url = Url::fromUserInput('/chubb_life/form/list_customer');
    if(!empty($form_state->getValue('cust_ref'))){
      $args['cust_ref'] = $form_state->getValue('cust_ref');
    }
    if(!empty($form_state->getValue('name'))){
      $args['name'] = $form_state->getValue('name');
    }
    if(!empty($form_state->getValue('fid'))){
      $args['fid'] = $form_state->getValue('fid');
    }
    if(!empty($form_state->getValue('tel_mbl'))){
      $args['tel_mbl'] = $form_state->getValue('tel_mbl');
    }
    if(!empty($form_state->getValue('created_at_start'))){
      $args['created_at_start'] = $form_state->getValue('created_at_start');
    }
    if(!empty($form_state->getValue('created_at_end'))){
      $args['created_at_end'] = $form_state->getValue('created_at_end');
    }
    if(!empty($form_state->getValue('assignee_id'))){
      $args['assignee_id'] = $form_state->getValue('assignee_id');
    }
    if(!empty($form_state->getValue('status'))){
      $args['status'] = $form_state->getValue('status');
    }
    if(!empty($form_state->getValue('record_per_page'))){
      $args['record_per_page'] = $form_state->getValue('record_per_page');
    }
    $url->setOptions(array('query' => $args));
    $form_state->setRedirectUrl($url);
  }
  public function clear_filter(array &$form, FormStateInterface $form_state) {
    $url = Url::fromUserInput('/chubb_life/form/list_customer');
    $form_state->setRedirectUrl($url);
  }

}
