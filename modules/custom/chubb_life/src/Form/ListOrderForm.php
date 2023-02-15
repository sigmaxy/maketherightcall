<?php

namespace Drupal\chubb_life\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\chubb_life\Controller\OrderController;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Symfony\Component\Filesystem;
use Drupal\file\Entity\File;
use phpseclib3\Net\SFTP;
use Drupal\Core\Site\Settings;
use Drupal\chubb_life\Controller\AttributeController;

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
    $header_table['status'] = t('Status');
    $header_table['updated_at'] = t('Updated At');
    $header_table['updated_by'] = t('Updated By');
    $header_table['json_generated'] = t('Json AT');
    $header_table['opt'] = t('Operation');
    $rows=array();
    $roles = \Drupal::currentUser()->getRoles();
    $uid = \Drupal::currentUser()->id();
    $order_status = AttributeController::get_order_status_options();
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
      $row_data['status'] = $order_status[$data->status];
      $row_data['updated_at'] = date('Y-m-d H:i:s',$data->updated_at);
      $updated_user = \Drupal\user\Entity\User::load($data->updated_by);
      $row_data['updated_by'] = $updated_user->field_agentname->value;
      // $row_data['json_generated'] = date('Y-m-d H:i:s',$data->json_generated);
      if($data->json_generated){
        $row_data['json_generated'] = [
          'class'=>['json_already_generated'],
          'id'=>'json_generated_'.$data->id,
          'data' => date('Y-m-d H:i:s',$data->json_generated),
        ];
      }else{
        $row_data['json_generated'] = [
          'class'=>[''],
          'id'=>'json_generated_'.$data->id,
          'data' => '',
        ];
      }
      $row_data['opt'] = Link::fromTextAndUrl('Edit', $edit);
      // $rows[$data->id] = [
      //   'data'=>$row_data,
      //   // '#attributes' => array('class' => array('page-row')),
      // ];
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
        'class' => ['order_list'],
        'col_sort_index' => 7,
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
      '#weight' => '4',
    ];
    $form['function_filters']['post_sftp'] = [
      '#type' => 'submit',
      '#value' => $this->t('Post to SFTP'),
      '#attributes' => [   
        'class' => ['next_button'],
      ],
      '#submit' => array('::post_to_sftp'),
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

  /**
   * {@inheritdoc}
   */
  public function prepare_json($order_selected) {
    $json = array();
    
    $json_arr = array(
      'batchDate' => date("Y-m-d"),
      'applications' => array(),
    );
    foreach ($order_selected as $order_id => $checked) {
      if ($checked) {
        $call = array();
        $db_order = OrderController::get_order_by_id($order_id);
        OrderController::update_order_json_generated($order_id);
        $one_json_record = OrderController::order_format_json($db_order);
        $json_arr['applications'][] = $one_json_record;
      }
    }
    $json_file_name = 'TM_APP_'.date('Ymd').'.txt';
    $json_file_prefix = 'public://temp/'.date('Ymdhis').'/';
    \Drupal::service('file_system')->prepareDirectory($json_file_prefix, \Drupal\Core\File\FileSystemInterface::CREATE_DIRECTORY);
    $json['path'] = $json_file_prefix.$json_file_name;
    $json['data'] = json_encode($json_arr,JSON_PRETTY_PRINT);
    $json['name'] = $json_file_name;
    $file = file_save_data($json['data'], $json['path'], 1);
    return $json;
  }
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $order_selected = $form_state->getValue('order_list_table');
    $json = self::prepare_json($order_selected);
    $link = file_create_url($json['path']); 
    \Drupal::messenger()->addMessage(t('Download json File <a href="@link" target="_blank">Click Here</a>', array('@link' => $link)));
  }
  public function post_to_sftp(array &$form, FormStateInterface $form_state) {
    $order_selected = $form_state->getValue('order_list_table');
    $json = self::prepare_json($order_selected);
    $sftp_config = Settings::get('sftp');
    $sftp = new SFTP($sftp_config['url']);
    if (!$sftp->login($sftp_config['username'], $sftp_config['password'])) {
        exit('Login Failed');
    }
    $file_uri = \Drupal::service('file_system')->realpath($json['path']);
    $sftp->put($json['name'], $file_uri, SFTP::SOURCE_LOCAL_FILE);
    \Drupal::messenger()->addMessage($json['name']. ' has been post to SFTP');
  }

}
