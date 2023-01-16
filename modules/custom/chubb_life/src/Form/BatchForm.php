<?php

namespace Drupal\chubb_life\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\chubb_life\Controller\CustomerController;

/**
 * Class BatchForm.
 */
class BatchForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'batch_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['batch_filter'] = [
      '#type'  => 'details',
      '#title' => $this->t('Batch'),
      '#open'  => true,
      '#weight' => '2',
    ];
    $form['batch_filter']['fid'] = [
      '#type' => 'textfield',
      '#title' => 'Batch Num',
      // '#default_value' => isset($conditions['cust_ref'])?$conditions['cust_ref']:'',
      '#maxlength' => 255,
      '#weight' => '1',
    ];
    $form['batch_filter']['check_batch'] = [
      '#type' => 'submit',
      '#value' => $this->t('Check'),
      '#attributes' => [   
        'class' => ['next_button'],
      ],
      '#submit' => array('::check_batch'),
      '#weight' => '10',
    ];
    $form['batch_filter']['delete_batch'] = [
      '#type' => 'submit',
      '#value' => $this->t('Delete'),
      '#attributes' => [   
        'class' => ['next_button'],
      ],
      '#submit' => array('::delete_batch'),
      '#weight' => '11',
    ];

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
  }
  public function check_batch(array &$form, FormStateInterface $form_state) {
    $batch_id = $form_state->getValue('fid');
    $num_of_record = CustomerController::count_import_customer_by_batch($batch_id);
    \Drupal::messenger()->addMessage('Batch Num ' . $batch_id.' has '.$num_of_record.' records');
  }

  public function delete_batch(array &$form, FormStateInterface $form_state) {
    $batch_id = $form_state->getValue('fid');
    $num_of_record = CustomerController::count_import_customer_by_batch($batch_id);
    CustomerController::delete_customer_by_batch($batch_id);
    \Drupal::messenger()->addMessage($num_of_record.' Records of Batch Num ' . $batch_id.' have been deleted');
  }

}
