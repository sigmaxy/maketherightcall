<?php

namespace Drupal\chubb_life\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\chubb_life\Controller\CallController;
use Drupal\chubb_life\Controller\CustomerController;
use Drupal\Core\Render\Markup;
use Drupal\chubb_life\Controller\AttributeController;
use Drupal\chubb_life\Controller\ProductController;
use Drupal\Core\Url;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\MessageCommand;

/**
 * Class EditCallForm.
 */
class EditCallForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'edit_call_form';
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
    // print_r($db_call);exit;
    $this->import_customer_id = $db_call['import_customer_id'];
    $import_customer = CustomerController::get_import_customer_by_id($db_call['import_customer_id']);
    // $customer_detail = '
    // <table class="customer_detail">
    // <tr><td>Cust Ref</td><td>'.$import_customer['cust_ref'].'</td></tr>
    // <tr><td>Name</td><td>'.$import_customer['name'].'</td></tr>
    // <tr><td>Gender</td><td>'.$import_customer['gender'].'</td></tr>
    // <tr><td>Mobile</td><td>'.$import_customer['tel_mbl'].'</td></tr>
    // <tr><td>Tel</td><td>'.$import_customer['tel_hom'].'</td></tr>
    // <tr><td>HKID</td><td>'.$import_customer['hkid'].'</td></tr>
    // <tr><td>ACC NO</td><td>'.$import_customer['acc_no'].'</td></tr>
    // <tr><td>Card Brand</td><td>'.$import_customer['card_brand'].'</td></tr>
    // <tr><td>Card Type</td><td>'.$import_customer['card_type'].'</td></tr>
    // <tr><td>Date of Birth</td><td>'.$import_customer['dob'].'</td></tr>
    // <tr><td>Married Status</td><td>'.$import_customer['married_status'].'</td></tr>
    // <tr><td>Address</td><td>'.$import_customer['address'].'</td></tr>
    // <tr><td>Living Person</td><td>'.$import_customer['living_person'].'</td></tr>
    // <tr><td>Email</td><td>'.$import_customer['email'].'</td></tr>
    // <tr><td>Member Since</td><td>'.$import_customer['member_since'].'</td></tr>
    // <tr><td>Occupation</td><td>'.$import_customer['occupation'].'</td></tr>
    // <tr><td>Position</td><td>'.$import_customer['position'].'</td></tr>
    // </table>';
    $customer_detail = '
    <table class="customer_detail">
    <tr><td><b>Cust Ref</b></td><td>'.$import_customer['cust_ref'].'</td><td><b>Name</b></td><td>'.$import_customer['name'].'</td></tr>
    <tr><td><b>Gender</b></td><td>'.$import_customer['gender'].'</td><td><b>Mobile</b></td><td>'.$import_customer['tel_mbl'].'</td></tr>
    <tr><td><b>Tel</b></td><td>'.$import_customer['tel_hom'].'</td><td><b>HKID</b></td><td>'.$import_customer['hkid'].'</td></tr>
    <tr><td><b>ACC NO</b></td><td>'.$import_customer['acc_no'].'</td><td><b>Card Brand</b></td><td>'.$import_customer['card_brand'].'</td></tr>
    <tr><td><b>Card Type</b></td><td>'.$import_customer['card_type'].'</td><td><b>Date of Birth</b></td><td>'.$import_customer['dob'].'</td></tr>
    <tr><td><b>Married Status</b></td><td>'.$import_customer['married_status'].'</td><td><b>Address</b></td><td>'.$import_customer['address'].'</td></tr>
    <tr><td><b>Living Person</b></td><td>'.$import_customer['living_person'].'</td><td><b>Email</b></td><td>'.$import_customer['email'].'</td></tr>
    <tr><td><b>Member Since</b></td><td>'.$import_customer['member_since'].'</td><td><b>Occupation</b></td><td>'.$import_customer['occupation'].'</td></tr>
    <tr><td><b>Position</b></td><td>'.$import_customer['position'].'</td><td></td><td></td></tr>
    </table>';
    $form['customer_detail'] = [
      '#type'  => 'details',
      '#title' => $this->t('Customer Detail'),
      '#open'  => true,
      '#weight' => '1',
    ];
    
    
    $form['customer_detail']['detail'] = [
      '#markup' => Markup::create($customer_detail),
      '#weight' => '1',
    ];
    $header_table['plan_code'] = t('Code');
    $header_table['plan_level'] = t('Level');
    $header_table['smokers_code'] = t('Smoker');
    $header_table['gender'] = t('Gender');
    $header_table['age'] = t('Age');
    $header_table['currency'] = t('Currency');
    $header_table['premium'] = t('Premium');
    $rows=array();
    $premium_list = ProductController::list_products();
    foreach($premium_list as $key=>$data){
      $row_data['plan_code'] = $data->plan_code;
      $row_data['plan_level'] = $data->plan_level;
      $row_data['smokers_code'] = $data->smokers_code;
      $row_data['gender'] = $data->gender;
      $row_data['age'] = $data->age;
      $row_data['currency'] = $data->currency;
      $row_data['premium'] = $data->premium;
      $rows[] = $row_data;
    }
    $form['product_detail'] = [
      '#type'  => 'details',
      '#title' => $this->t('Product Detail'),
      '#open'  => true,
      '#weight' => '3',
    ];
    $form['product_detail']['premium_list_table'] = [
      '#type' => 'table',
      '#header' => $header_table,
      // '#footer' => $header_table,
      '#footer' => array(
        array(
          'class' => array('footer-class'),
          'data' => $header_table,
        ),
      ),
      // '#rows' => $rows,
      '#empty' => t('No Premium found'),
      '#attributes' => [   
        'class' => ['premium_list'],
        'col_sort_index' => 1,
        // 'col_sort_type' => 'asc',
      ],
      '#weight' => '2',
    ];
    $form['call_detail'] = [
      '#type'  => 'details',
      '#title' => $this->t('Call Functions'),
      '#open'  => true,
      '#weight' => '2',
    ];
    $call_status_opt = AttributeController::get_call_status_options();
    $form['call_detail']['status'] = [
      '#type' => 'select',
      '#title' => $this->t('Call Status'),
      '#weight' => '3',
      '#options' => $call_status_opt,
      '#default_value' => isset($db_call['status'])?$db_call['status']:0,
      '#attributes' => [   
        'class' => ['noselect2'],
      ],
    ];
    $form['call_detail']['remark'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Remarks'),
      '#default_value' => $db_call['remark']?$db_call['remark']:'',
      '#weight' => '4',
    );
    
    $form['call_detail']['makecall'] = [
      '#type' => 'button',
      '#value' => $this->t('Make Call'),
      '#ajax' => [
        'callback' => '::make_call',
      ],
      '#weight' => '5',
    ];
    $form['call_detail']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#weight' => '6',
    ];
    $form['call_detail']['sales'] = [
      '#type' => 'submit',
      '#value' => $this->t('Sales'),
      '#attributes' => [
        'class' => ['next_button'],
      ],
      '#submit' => array('::sales_call'),
      '#weight' => '7',
    ];
    $form['#attached']['drupalSettings']['mobile'] = $import_customer['tel_mbl'];
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
    // $call_status = $form_state->getValue('status');
    // $remark = $form_state->getValue('remark');
    $call = CallController::get_call_by_id($this->call_id);
    // print_r($call);
    // $call['id'] = $this->call_id;
    $call['status'] = $form_state->getValue('status');
    $call['remark'] = $form_state->getValue('remark');
    CallController::update_call($call);
    \Drupal::messenger()->addMessage('Call has been updated');
    $form_state->setRedirectUrl(Url::fromRoute('chubb_life.list_call_form'));
  }
  public function make_call(array $form, FormStateInterface $form_state) {
    $field=$form_state->getValues();
    $call_status = $field['status'];
    CallController::make_call($this->call_id);
    $response = new AjaxResponse();
    $pickup_call_url = \Drupal\Core\Site\Settings::get('pickup_call_url');
    $response->addCommand(new InvokeCommand(NULL, 'open_new_tab', [$pickup_call_url]));
    return $response;
  }
  public function sales_call(array &$form, FormStateInterface $form_state) {
    $url = Url::fromRoute('chubb_life.edit_order_form', [
      'order_id' => 'add',
      'customer_id' => $this->import_customer_id,
    ]);
    $form_state->setRedirectUrl($url);
  }

}
