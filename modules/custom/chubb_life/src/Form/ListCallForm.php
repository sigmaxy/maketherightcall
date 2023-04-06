<?php

namespace Drupal\chubb_life\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\chubb_life\Controller\AttributeController;
use Drupal\chubb_life\Controller\CustomerController;
use Drupal\chubb_life\Controller\AssigneeController;
use Drupal\chubb_life\Controller\CallController;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Class ListCallForm.
 */
class ListCallForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'list_call_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $conditions = array();
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
    if(\Drupal::request()->query->get('updated_at_start')){
      $conditions['updated_at_start']=\Drupal::request()->query->get('updated_at_start');
    }
    if(\Drupal::request()->query->get('updated_at_end')){
      $conditions['updated_at_end']=\Drupal::request()->query->get('updated_at_end');
    }
    if(\Drupal::request()->query->get('status')){
      $conditions['status']=\Drupal::request()->query->get('status');
    }
    if(\Drupal::request()->query->get('record_per_page')){
      $conditions['record_per_page']=\Drupal::request()->query->get('record_per_page');
    }
    $header_table['fid'] = t('Batch');
    $header_table['cust_ref'] = t('Ref No.');
    $header_table['name'] = t('Name');
    $header_table['age'] = t('Age');
    // $header_table['tel_mbl'] = t('Mobile');
    $header_table['status'] = t('Status');
    $header_table['count'] = t('Attempts');
    $header_table['assignee'] = t('Assignee');
    $header_table['updated_at'] = t('Lastest Modify');
    $header_table['updated_by'] = t('Updated By');
    $header_table['opt'] = t('Opt');
    $rows=array();
    $current_uid = \Drupal::currentUser()->id();


    $roles = \Drupal::currentUser()->getRoles();
    if(in_array('manager', $roles)||in_array('administrator', $roles)) {
      
    }else{
      $conditions['assignee_id'] = $current_uid;
    }
    $call_list = CallController::list_call_pager($conditions);
    // $call_list = CallController::list_call_by_assignee_view($pager,$conditions);

    // $connection = Database::getConnection();
    // $query = $connection->select('mtrc_call', 'mc');
    // $query->fields('mc');
    // if(isset($assignee_id)){
    //   $query->condition('assignee_id', $assignee_id);
    // }
    // $pager = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit(10);
    // $call_list = $pager->execute()->fetchAll();
    // return $record;


    $call_status_opt = AttributeController::get_call_status_options();
    $record_per_page_opt = AttributeController::get_record_per_page_options();
    $filter_call_status_opt = $call_status_opt;
    $filter_call_status_opt['null'] = 'Not Assigned';
    foreach($call_list as $key=>$data){
      $url = Url::fromUserInput('/chubb_life/form/edit_call/'.$data->id);
      $link_options = array(
        'attributes' => array(
          // 'class' => array(
          //   'button',
          //   'bg-green'
          // ),
          'onclick' => array(
            'window.open (this.href,"targetWindow","menubar=1,resizable=1,width=1200,height=800");return false;',
          ),
        ),
      );
      $url->setOptions($link_options);
      $view_log = Url::fromUserInput('/chubb_life/form/list_call_log/'.$data->id);
      $import_customer_id = $data->import_customer_id;
      $import_customer = CustomerController::get_import_customer_by_id($import_customer_id);
      $row_data['fid'] = $import_customer['fid'];
      $row_data['cust_ref'] = $import_customer['cust_ref'];
      $row_data['name'] = $import_customer['name'];
      // $date = DrupalDateTime::createFromDateTime("Y-m-d", $import_customer['dob']);
      $birthday = \DateTime::createFromFormat("m/d/Y", $import_customer['dob']);
      $row_data['age'] = $birthday?date_diff(date_create($import_customer['dob']), date_create('now'))->y:'0';
      // $row_data['tel_mbl'] = $import_customer['tel_mbl'];
      $row_data['status'] = $call_status_opt[$data->status];
      $row_data['count'] = [
        'class'=>['call_count'],
        'data-call_id'=>$data->id,
        // 'data' => $data->count,
        'data' => Link::fromTextAndUrl($data->count, $view_log),
      ];
      $user = \Drupal\user\Entity\User::load($data->assignee_id);
      $agent_code = $user->field_agentcode->value;
      $row_data['assignee'] = $user->getEmail();
      if(!empty($agent_code)){
        $row_data['assignee'] = $agent_code;
      }
      $row_data['updated_at'] = date('Y-m-d H:i:s',$data->updated_at);
      $updated_user = \Drupal\user\Entity\User::load($data->updated_by);
      $row_data['updated_by'] = $updated_user->field_agentname->value;
      $row_data['opt'] = [
        'data'=> Link::fromTextAndUrl('View', $url),
        'title'=> $data->remark
      ];
      $rows[$data->id] = $row_data;
    }
    $form['call_filter'] = [
      '#type'  => 'details',
      '#title' => $this->t('Assigned Call'),
      '#open'  => true,
      '#weight' => '2',
    ];
    $form['call_filter']['cust_ref'] = [
      '#type' => 'textfield',
      '#title' => 'Customer Ref',
      '#default_value' => isset($conditions['cust_ref'])?$conditions['cust_ref']:'',
      '#maxlength' => 255,
      '#weight' => '1',
    ];
    $form['call_filter']['name'] = [
      '#type' => 'textfield',
      '#title' => 'Customer Name',
      '#default_value' => isset($conditions['name'])?$conditions['name']:'',
      '#maxlength' => 255,
      '#weight' => '2',
    ];
    $form['call_filter']['fid'] = [
      '#type' => 'textfield',
      '#title' => 'Batch Num',
      '#default_value' => isset($conditions['fid'])?$conditions['fid']:'',
      '#maxlength' => 255,
      '#weight' => '3',
    ];
    $form['call_filter']['tel_mbl'] = [
      '#type' => 'textfield',
      '#title' => 'Mobile',
      '#default_value' => isset($conditions['tel_mbl'])?$conditions['tel_mbl']:'',
      '#maxlength' => 255,
      '#weight' => '4',
    ];
    $form['call_filter']['updated_at_start'] = [
      '#type' => 'date',
      '#title' => 'Updated At Start',
      '#default_value' => isset($conditions['updated_at_start'])?$conditions['updated_at_start']:'',
      '#maxlength' => 255,
      '#weight' => '5',
    ];
    $form['call_filter']['updated_at_end'] = [
      '#type' => 'date',
      '#title' => 'Updated At End',
      '#default_value' => isset($conditions['updated_at_end'])?$conditions['updated_at_end']:'',
      '#maxlength' => 255,
      '#weight' => '6',
    ];
    $form['call_filter']['status'] = [
      '#type' => 'select',
      '#title' => $this->t('Status'),
      '#options' => $filter_call_status_opt,
      '#empty_option' => '--Select--',
      '#default_value' => isset($conditions['status'])?$conditions['status']:'',
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '7',
    ];
    $form['call_filter']['record_per_page'] = [
      '#type' => 'select',
      '#title' => $this->t('Record Per Page'),
      '#options' => $record_per_page_opt,
      // '#empty_option' => '--Select--',
      '#default_value' => isset($conditions['record_per_page'])?$conditions['record_per_page']:'10',
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '8',
    ];
    $form['call_filter']['filter_customer'] = [
      '#type' => 'submit',
      '#value' => $this->t('Filter'),
      '#attributes' => [   
        'class' => ['next_button'],
      ],
      '#submit' => array('::filter_customer'),
      '#weight' => '10',
    ];
    $form['call_filter']['clear_filter'] = [
      '#type' => 'submit',
      '#value' => $this->t('Clear'),
      '#attributes' => [   
        'class' => ['next_button'],
      ],
      '#submit' => array('::clear_filter'),
      '#weight' => '11',
    ];
    $form['call_filter']['call_list_table'] = [
      '#type' => 'table',
      '#header' => $header_table,
      '#rows' => $rows,
      '#empty' => t('No Customer found'),
      '#attributes' => [   
        'class' => ['customer_list1','traditional_data_tale'],
        'col_sort_index' => 1,
        // 'col_sort_type' => 'asc',
      ],
      '#weight' => '20',
    ];
    $form['call_filter']['pager'] = array(
      '#type' => 'pager',
      '#weight' => '21',
    );
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
    
    foreach ($form_state->getValues() as $key => $value) {
      \Drupal::messenger()->addMessage($key . ': ' . ($key === 'text_format'?$value['value']:$value));
    }
  }
  public function filter_customer(array &$form, FormStateInterface $form_state) {
    $url = Url::fromUserInput('/chubb_life/form/list_call');
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
    if(!empty($form_state->getValue('updated_at_start'))){
      $args['updated_at_start'] = $form_state->getValue('updated_at_start');
    }
    if(!empty($form_state->getValue('updated_at_end'))){
      $args['updated_at_end'] = $form_state->getValue('updated_at_end');
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
    $url = Url::fromUserInput('/chubb_life/form/list_call');
    $form_state->setRedirectUrl($url);
  }

}
