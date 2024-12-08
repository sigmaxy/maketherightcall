<?php

namespace Drupal\chubb_life\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\chubb_life\Controller\ReportController;


/**
 * Class BatchForm.
 */
class ReportForm extends FormBase {

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
    $form['call_report'] = [
        '#type'  => 'details',
        '#title' => $this->t('Call Report'),
        '#open'  => true,
        '#weight' => '2',
    ];
    $form['call_report']['call_report_submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Generated Call Report'),
        '#attributes' => [   
            'class' => ['next_button'],
        ],
        '#submit' => array('::call_report'),
        '#weight' => '10',
    ];
    $form['sales_report'] = [
        '#type'  => 'details',
        '#title' => $this->t('Sales Report'),
        '#open'  => true,
        '#weight' => '3',
    ];
    $form['sales_report']['sales_report_submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Generated Sales Report'),
        '#attributes' => [   
          'class' => ['next_button'],
        ],
        '#submit' => array('::sales_report'),
        '#weight' => '10',
    ];
    $form['call_lead_status_report'] = [
        '#type'  => 'details',
        '#title' => $this->t('Call Lead Status Report'),
        '#open'  => true,
        '#weight' => '4',
    ];
    $form['call_lead_status_report']['call_lead_status_report'] = [
        '#type' => 'submit',
        '#value' => $this->t('Generated Call Lead Status Report'),
        '#attributes' => [   
          'class' => ['next_button'],
        ],
        '#submit' => array('::call_lead_status_report'),
        '#weight' => '10',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // foreach ($form_state->getValues() as $key => $value) {
    //   // @TODO: Validate fields.
    // }
    // parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // echo 'test1';
    // $excel = ReportController::prepare_sales_report_data();
    // echo 'test';exit;
  }
    public function call_report(array &$form, FormStateInterface $form_state) {
        $excel = ReportController::prepare_call_report_data();
        \Drupal::messenger()->addMessage(t('Download Report File <a href="@link" target="_blank">Click Here</a>', array('@link' => file_create_url($excel['path']))));
    }
    public function sales_report(array &$form, FormStateInterface $form_state) {
        $excel = ReportController::prepare_sales_report_data();
        \Drupal::messenger()->addMessage(message: t('Download1 Report File <a href="@link" target="_blank">Click Here</a>', array('@link' => file_create_url($excel['path']))));
    }
    public function call_lead_status_report(array &$form, FormStateInterface $form_state) {
        $excel = ReportController::prepare_call_lead_status_report_data();
        \Drupal::messenger()->addMessage(t('Download Report File <a href="@link" target="_blank">Click Here</a>', array('@link' => file_create_url($excel['path']))));
    }


}
