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
    $header_table['cust_ref'] = t('Ref No.');
    $header_table['name'] = t('Name');
    $header_table['gender'] = t('Gender');
    $header_table['tel_mbl'] = t('Mobile');
    $header_table['status'] = t('Status');
    $header_table['opt'] = t('Opt');
    $rows=array();
    $current_uid = \Drupal::currentUser()->id();
    $call_list = CallController::list_call_by_assignee($current_uid);
    foreach($call_list as $key=>$data){
      $edit   = Url::fromUserInput('/chubb_life/form/edit_call/'.$data->id);
      $import_customer_id = $data->import_customer_id;
      $import_customer = CustomerController::get_import_customer_by_id($import_customer_id);
      $row_data['cust_ref'] = $import_customer['cust_ref'];
      $row_data['name'] = $import_customer['name'];
      $row_data['gender'] = $import_customer['gender'];
      $row_data['tel_mbl'] = $import_customer['tel_mbl'];
      $row_data['status'] = $data->status;
      $row_data['opt'] = Link::fromTextAndUrl('View', $edit);
      $rows[$data->id] = $row_data;
    }
    $form['call_filter'] = [
      '#type'  => 'details',
      '#title' => $this->t('Assigned Call'),
      '#open'  => true,
      '#weight' => '2',
    ];
    $form['call_filter']['call_list_table'] = [
      '#type' => 'table',
      '#header' => $header_table,
      '#rows' => $rows,
      '#empty' => t('No Customer found'),
      '#attributes' => [   
        'class' => ['customer_list','table_list_data'],
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
    
    foreach ($form_state->getValues() as $key => $value) {
      \Drupal::messenger()->addMessage($key . ': ' . ($key === 'text_format'?$value['value']:$value));
    }
  }

}
