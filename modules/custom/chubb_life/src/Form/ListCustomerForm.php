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
    $header_table['cust_ref'] = t('Ref No.');
    $header_table['name'] = t('Name');
    $header_table['gender'] = t('Gender');
    $header_table['tel_mbl'] = t('Mobile');
    $header_table['status'] = t('Status');
    $header_table['assignee'] = t('Assignee');
    $header_table['created_at'] = t('Assignee');
    $rows=array();
    $import_customer_list = CustomerController::list_import_customer();
    $call_status_opt = AttributeController::get_call_status_options();
    foreach($import_customer_list as $key=>$data){
      // $edit   = Url::fromUserInput('/chubb_life/form/editcall/'.$data->id);
      $db_call = CallController::get_call_by_import_customer_id($data->id);
      if (isset($db_call['id'])) {
        $row_data['status'] = $call_status_opt[$db_call['status']];
        $user = \Drupal\user\Entity\User::load($db_call['assignee_id']);
        $row_data['assignee'] = $user->getEmail();
      }else{
        $row_data['status'] = 'Not Assigned';
        $row_data['assignee'] = '';
      }
      $row_data['cust_ref'] = $data->cust_ref;
      $row_data['name'] = $data->name;
      $row_data['gender'] = $data->gender;
      $row_data['tel_mbl'] = $data->tel_mbl;
      $row_data['tel_hom'] = $data->tel_hom;
      $row_data['created_at'] = date('Y-m-d',$data->created_at);
      
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
    // // $form['filters']['load_data'] = [
    // //   '#type' => 'button',
    // //   '#value' => $this->t('Load PO Data'),
    // //   '#attributes' => [   
    // //     'class' => ['next_button','load_data_po'],
    // //     'onclick' => 'return (false);',
    // //   ],
    // //   '#weight' => '2',
    // // ];
    $form['import_customer_filter'] = [
      '#type'  => 'details',
      '#title' => $this->t('Import Customer'),
      '#open'  => true,
      '#weight' => '2',
    ];
    $form['import_customer_filter']['import_customer_list_table'] = [
      '#type' => 'tableselect',
      '#header' => $header_table,
      // '#rows' => $rows,
      '#options' => $rows,
      '#empty' => t('No Customer found'),
      '#attributes' => [   
        'class' => ['table_list_data','import_customer_list'],
        'col_sort_index' => 5,
        // 'col_sort_type' => 'asc',
      ],
    ];
    $form['function_filters'] = [
      '#type'  => 'details',
      '#title' => $this->t('Function'),
      '#open'  => true,
      '#weight' => '3',
    ];
    $assignee_opts = AssigneeController::list_assignee();
    $form['function_filters']['assignee'] = [
      '#title' => $this->t('Assignee'),
      '#type' => 'select',
      '#options' => $assignee_opts,
      '#attributes' => [
        'class' => ['assignee_select'],
      ],
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
    $customer_selected = $form_state->getValue('import_customer_list_table');
    $assignee_uid = $form_state->getValue('assignee');
    foreach ($customer_selected as $imported_customer_id => $checked) {
      if ($checked) {
        $call = array();
        $call['import_customer_id'] = $imported_customer_id;
        $call['assignee_id'] = $assignee_uid;
        $call['status'] = 1;
        CallController::update_call($call);
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

}
