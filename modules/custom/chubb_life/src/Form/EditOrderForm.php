<?php

namespace Drupal\chubb_life\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\chubb_life\Controller\AttributeController;
use Drupal\chubb_life\Controller\OrderController;

/**
 * Class EditOrderForm.
 */
class EditOrderForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'edit_order_form';
  }
  public $order_id;
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $order_id = NULL) {
    if (isset($order_id)) {
      $this->order_id = $order_id;
    }
    $country_opt = AttributeController::get_country_options();
    $relation_opt = AttributeController::get_relation_options();
    $id_type_opt = AttributeController::get_id_type_options();
    $marital_status_opt = AttributeController::get_marital_status_options();
    $occupations_opt = AttributeController::get_occupations_group_options();
    $monthly_income_opt = AttributeController::get_monthly_income_options();
    $yn_opt = AttributeController::get_yn_options();
    $gender_opt = AttributeController::get_gender_options();
    $solicitation_opt = AttributeController::get_solicitation_options();
    $opt_out_reason_opt = AttributeController::get_opt_out_reason_options();
    $currency_opt = AttributeController::get_currency_options();
    $payment_mode_opt = AttributeController::get_payment_mode_options();
    $bill_type_opt = AttributeController::get_bill_type_options();
    $form['application'] = [
      '#type'  => 'details',
      '#title' => $this->t('Application Detail'),
      '#open'  => true,
      '#weight' => '1',
    ];
    $form['application']['referenceNumber'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Reference Number'),
      '#maxlength' => 45,
      '#weight' => '1',
      '#required'=> true,
    ];
    $form['application']['aeonRefNumber'] = [
      '#type' => 'textfield',
      '#title' => $this->t('AEONRefNum'),
      '#maxlength' => 45,
      '#weight' => '2',
      '#required'=> true,
    ];
    $form['customer_owner'] = [
      '#type'  => 'details',
      '#title' => $this->t('Customer Details (Owner)'),
      '#open'  => true,
      '#weight' => '2',
    ];
    $form['customer_owner']['same_as_owner'] = [
      '#type' => 'select',
      '#title' => $this->t('Insured is same as owner'),
      '#weight' => '4',
      '#options' => $yn_opt,
      // '#default_value' => isset($record['specification_1_id'])?$record['specification_1_id']:0,
      '#attributes' => [
        'class' => ['same_as_owner_select'],
      ],
      '#weight' => '1',
    ];
    $form['customer_owner']['surname'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last Name (English)'),
      '#maxlength' => 255,
      '#size' => 64,
      '#weight' => '2',
      // '#default_value' => $default_po_number,
      '#required'=> true,
    ];
    $form['customer_owner']['givenName'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First Name (English)'),
      '#maxlength' => 255,
      '#size' => 64,
      '#weight' => '3',
      // '#default_value' => $default_po_number,
      '#required'=> true,
    ];
    $form['customer_owner']['chineseName'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name (Chinese)'),
      '#maxlength' => 255,
      '#size' => 64,
      '#weight' => '4',
      // '#default_value' => $default_po_number,
      // '#required'=> true,
    ];
    $form['customer_owner']['relationship'] = [
      '#type' => 'select',
      '#title' => $this->t('Relationship to Proposed Insured'),
      '#options' => $relation_opt,
      // '#default_value' => 'MOT',
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '5',
      '#required'=> true,
    ];
    $form['customer_owner']['identityType'] = [
      '#type' => 'select',
      '#title' => $this->t('ID Type'),
      '#options' => $id_type_opt,
      // '#default_value' => 1,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '6',
      '#required'=> true,
    ];
    $form['customer_owner']['identityNumber'] = [
      '#type' => 'textfield',
      '#title' => $this->t('HKID Card Number'),
      '#maxlength' => 255,
      '#size' => 64,
      '#weight' => '7',
      '#required'=> true,
    ];
    $form['customer_owner']['issueCountry'] = [
      '#type' => 'select',
      '#title' => $this->t('Country of Issue'),
      '#options' => $country_opt,
      '#default_value' => 'HK',
      '#attributes' => [
        'class' => ['country_select'],
      ],
      '#weight' => '8',
      '#required'=> true,
    ];
    $form['customer_owner']['gender'] = [
      '#type' => 'select',
      '#title' => $this->t('Gender'),
      '#options' => $gender_opt,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '9',
      '#required'=> true,
    ];
    $form['customer_owner']['isPermanentHkid'] = [
      '#type' => 'select',
      '#title' => $this->t('HK Permanent ID Card holder'),
      '#options' => $yn_opt,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '10',
    ];
    $form['customer_owner']['birthDate'] = [
      '#type' => 'date',
      '#title' => $this->t('Date of Birth'),
      '#weight' => '11',
      '#required'=> true,
    ];
    $form['customer_owner']['marital_status'] = [
      '#type' => 'select',
      '#title' => $this->t('Marital Status'),
      '#options' => $marital_status_opt,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '11',
      '#required'=> true,
    ];
    $form['customer_owner']['nationality'] = [
      '#type' => 'select',
      '#title' => $this->t('Nationality'),
      '#options' => $country_opt,
      '#default_value' => 'HK',
      '#attributes' => [
        'class' => ['country_select'],
      ],
      '#weight' => '12',
      '#required'=> true,
    ];
    $form['customer_owner']['taxResidency'] = [
      '#type' => 'select',
      '#title' => $this->t('Tax Residency'),
      '#options' => $country_opt,
      '#default_value' => 'HK',
      '#attributes' => [
        'class' => ['country_select'],
      ],
      '#weight' => '13',
      '#required'=> true,
    ];
    $form['customer_owner']['taxResidencyTin'] = [
      '#type' => 'textfield',
      '#title' => $this->t('TIN 1'),
      '#maxlength' => 255,
      '#weight' => '14',
      '#required'=> true,
    ];
    $form['customer_owner']['email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email Address'),
      '#maxlength' => 255,
      '#weight' => '15',
      '#required'=> true,
    ];
    
    $form['customer_owner']['residence_address1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Flat/FloorBlock'),
      '#weight' => '16',
      '#required'=> true,
    ];
    $form['customer_owner']['residence_address2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Building/Street Number'),
      '#weight' => '17',
      '#required'=> true,
    ];
    $form['customer_owner']['residence_address3'] = [
      '#type' => 'textfield',
      '#title' => $this->t('District'),
      '#weight' => '18',
    ];
    $form['customer_owner']['residence_city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('District/City'),
      '#weight' => '19',
      '#required'=> true,
    ];
    $form['customer_owner']['residence_country'] = [
      '#type' => 'select',
      '#title' => $this->t('Country/Region'),
      '#options' => $country_opt,
      '#default_value' => 'HK',
      '#attributes' => [
        'class' => ['country_select'],
      ],
      '#weight' => '20',
      '#required'=> true,
    ];
    $form['customer_owner']['mailing_address1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Flat/FloorBlock'),
      '#weight' => '21',
      '#required'=> true,
    ];
    $form['customer_owner']['mailing_address2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Building/Street Number'),
      '#weight' => '22',
      '#required'=> true,
    ];
    $form['customer_owner']['mailing_address3'] = [
      '#type' => 'textfield',
      '#title' => $this->t('District'),
      '#weight' => '23',
    ];
    $form['customer_owner']['mailing_city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('District/City'),
      '#weight' => '24',
      '#required'=> true,
    ];
    $form['customer_owner']['mailing_country'] = [
      '#type' => 'select',
      '#title' => $this->t('Country/Region'),
      '#options' => $country_opt,
      '#default_value' => 'HK',
      '#attributes' => [
        'class' => ['country_select'],
      ],
      '#weight' => '25',
      '#required'=> true,
    ];
    $form['customer_owner']['occupationCode'] = [
      '#type' => 'select',
      '#title' => $this->t('Occupations Code'),
      '#options' => $occupations_opt,
      '#attributes' => [
        'class' => ['occupation_select'],
      ],
      '#weight' => '26',
      '#required'=> true,
    ];
    $form['customer_owner']['mobile'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Mobile Phone Number'),
      '#weight' => '27',
      '#required'=> true,
    ];
    $form['customer_owner']['smoker'] = [
      '#type' => 'select',
      '#title' => $this->t('Smoker'),
      '#weight' => '4',
      '#options' => $yn_opt,
      // '#default_value' => isset($record['specification_1_id'])?$record['specification_1_id']:0,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '28',
      '#required'=> true,
    ];
    $form['customer_owner']['monthly_income'] = [
      '#type' => 'select',
      '#title' => $this->t('Monthly income'),
      '#options' => $monthly_income_opt,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '29',
      '#required'=> true,
    ];
    $form['customer_owner']['solicitation'] = [
      '#type' => 'select',
      '#title' => $this->t('Solicitation (Opt out Indicator)'),
      '#options' => $solicitation_opt,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '30',
      '#required'=> true,
    ];
    $form['customer_owner']['opt_out_reason'] = [
      '#type' => 'select',
      '#title' => $this->t('Opt out reason'),
      '#options' => $opt_out_reason_opt,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '31',
    ];
    $form['customer_insured'] = [
      '#type'  => 'details',
      '#title' => $this->t('Customer Details (Insured)'),
      '#open'  => true,
      '#weight' => '3',
    ];
    $form['customer_insured']['customer_insured_surname'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last Name (English)'),
      '#maxlength' => 255,
      '#weight' => '2',
    ];
    $form['customer_insured']['customer_insured_givenName'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First Name (English)'),
      '#maxlength' => 255,
      '#weight' => '3',
    ];
    $form['customer_insured']['customer_insured_chineseName'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name (Chinese)'),
      '#maxlength' => 255,
      '#size' => 64,
      '#weight' => '4',
    ];
    $form['customer_insured']['customer_insured_identityType'] = [
      '#type' => 'select',
      '#title' => $this->t('ID Type'),
      '#options' => $id_type_opt,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '6',
    ];
    $form['customer_insured']['customer_insured_identityNumber'] = [
      '#type' => 'textfield',
      '#title' => $this->t('HKID Card Number'),
      '#maxlength' => 255,
      '#size' => 64,
      '#weight' => '7',
    ];
    $form['customer_insured']['customer_insured_issueCountry'] = [
      '#type' => 'select',
      '#title' => $this->t('Country of Issue'),
      '#options' => $country_opt,
      '#default_value' => 'HK',
      '#attributes' => [
        'class' => ['country_select'],
      ],
      '#weight' => '8',
    ];
    $form['customer_insured']['customer_insured_gender'] = [
      '#type' => 'select',
      '#title' => $this->t('Gender'),
      '#options' => $gender_opt,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '9',
    ];
    $form['customer_insured']['customer_insured_isPermanentHkid'] = [
      '#type' => 'select',
      '#title' => $this->t('HK Permanent ID Card holder'),
      '#options' => $yn_opt,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '10',
    ];
    $form['customer_insured']['customer_insured_birthDate'] = [
      '#type' => 'date',
      '#title' => $this->t('Date of Birth'),
      '#weight' => '11',
    ];
    $form['customer_insured']['customer_insured_marital_status'] = [
      '#type' => 'select',
      '#title' => $this->t('Marital Status'),
      '#options' => $marital_status_opt,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '11',
    ];
    $form['customer_insured']['customer_insured_nationality'] = [
      '#type' => 'select',
      '#title' => $this->t('Nationality'),
      '#options' => $country_opt,
      '#default_value' => 'HK',
      '#attributes' => [
        'class' => ['country_select'],
      ],
      '#weight' => '12',
    ];
    $form['customer_insured']['customer_insured_taxResidency'] = [
      '#type' => 'select',
      '#title' => $this->t('Tax Residency'),
      '#options' => $country_opt,
      '#default_value' => 'HK',
      '#attributes' => [
        'class' => ['country_select'],
      ],
      '#weight' => '13',
    ];
    $form['customer_insured']['customer_insured_taxResidencyTin'] = [
      '#type' => 'textfield',
      '#title' => $this->t('TIN 1'),
      '#maxlength' => 255,
      '#weight' => '14',
    ];
    $form['customer_insured']['customer_insured_email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email Address'),
      '#maxlength' => 255,
      '#weight' => '15',
    ];
    
    $form['customer_insured']['customer_insured_residence_address1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Flat/FloorBlock'),
      '#weight' => '16',
    ];
    $form['customer_insured']['customer_insured_residence_address2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Building/Street Number'),
      '#weight' => '17',
    ];
    $form['customer_insured']['customer_insured_residence_address3'] = [
      '#type' => 'textfield',
      '#title' => $this->t('District'),
      '#weight' => '18',
    ];
    $form['customer_insured']['customer_insured_residence_city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('District/City'),
      '#weight' => '19',
    ];
    $form['customer_insured']['customer_insured_residence_country'] = [
      '#type' => 'select',
      '#title' => $this->t('Country/Region'),
      '#options' => $country_opt,
      '#default_value' => 'HK',
      '#attributes' => [
        'class' => ['country_select'],
      ],
      '#weight' => '20',
    ];
    $form['customer_insured']['customer_insured_mailing_address1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Flat/FloorBlock'),
      '#weight' => '21',
    ];
    $form['customer_insured']['customer_insured_mailing_address2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Building/Street Number'),
      '#weight' => '22',
    ];
    $form['customer_insured']['customer_insured_mailing_address3'] = [
      '#type' => 'textfield',
      '#title' => $this->t('District'),
      '#weight' => '23',
    ];
    $form['customer_insured']['customer_insured_mailing_city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('District/City'),
      '#weight' => '24',
    ];
    $form['customer_insured']['customer_insured_mailing_country'] = [
      '#type' => 'select',
      '#title' => $this->t('Country/Region'),
      '#options' => $country_opt,
      '#default_value' => 'HK',
      '#attributes' => [
        'class' => ['country_select'],
      ],
      '#weight' => '25',
    ];
    $form['customer_insured']['customer_insured_occupationCode'] = [
      '#type' => 'select',
      '#title' => $this->t('Occupations Code'),
      '#options' => $occupations_opt,
      '#attributes' => [
        'class' => ['occupation_select'],
      ],
      '#weight' => '26',
    ];
    $form['customer_insured']['customer_insured_mobile'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Mobile Phone Number'),
      '#weight' => '27',
    ];
    $form['customer_insured']['customer_insured_smoker'] = [
      '#type' => 'select',
      '#title' => $this->t('Smoker'),
      '#weight' => '4',
      '#options' => $yn_opt,
      // '#default_value' => isset($record['specification_1_id'])?$record['specification_1_id']:0,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '28',
    ];
    $form['customer_insured']['customer_insured_monthly_income'] = [
      '#type' => 'select',
      '#title' => $this->t('Monthly income'),
      '#options' => $monthly_income_opt,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '29',
    ];
    $form['customer_insured']['customer_insured_solicitation'] = [
      '#type' => 'select',
      '#title' => $this->t('Solicitation (Opt out Indicator)'),
      '#options' => $solicitation_opt,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '30',
    ];
    $form['customer_insured']['customer_insured_opt_out_reason'] = [
      '#type' => 'select',
      '#title' => $this->t('Opt out reason'),
      '#options' => $opt_out_reason_opt,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '31',
    ];
    $form['customer_payor'] = [
      '#type'  => 'details',
      '#title' => $this->t('Customer Details (Payor) (TBC)'),
      '#open'  => true,
      '#weight' => '4',
    ];
    $form['customer_payor']['customer_payor_surname'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last Name (English)'),
      '#maxlength' => 255,
      '#weight' => '2',
      // '#default_value' => $default_po_number,
      '#required'=> true,
    ];
    $form['customer_payor']['customer_payor_givenName'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First Name (English)'),
      '#maxlength' => 255,
      '#weight' => '3',
      // '#default_value' => $default_po_number,
      '#required'=> true,
    ];
    $form['customer_payor']['customer_payor_chineseName'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name (Chinese)'),
      '#maxlength' => 255,
      '#weight' => '4',
      // '#default_value' => $default_po_number,
    ];
    $form['customer_payor']['customer_payor_identityType'] = [
      '#type' => 'select',
      '#title' => $this->t('ID Type'),
      '#options' => $id_type_opt,
      // '#default_value' => 1,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '6',
      '#required'=> true,
    ];
    $form['customer_payor']['customer_payor_identityNumber'] = [
      '#type' => 'textfield',
      '#title' => $this->t('HKID Card Number'),
      '#maxlength' => 255,
      '#size' => 64,
      '#weight' => '7',
      '#required'=> true,
    ];
    $form['customer_payor']['customer_payor_gender'] = [
      '#type' => 'select',
      '#title' => $this->t('Gender'),
      '#options' => $gender_opt,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '9',
      '#required'=> true,
    ];
    $form['customer_payor']['customer_payor_birthDate'] = [
      '#type' => 'date',
      '#title' => $this->t('Date of Birth'),
      '#weight' => '11',
      '#required'=> true,
    ];

    $form['beneficiary'] = [
      '#type'  => 'details',
      '#title' => $this->t('Beneficiary'),
      '#open'  => true,
      '#weight' => '5',
      '#required'=> true,
    ];
    $form['beneficiary']['beneficiary_relationship'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Relationship'),
      '#maxlength' => 255,
      '#weight' => '1',
      '#default_value' => 'Estate',
      '#required'=> true,
    ];
    $form['policy'] = [
      '#type'  => 'details',
      '#title' => $this->t('Policy Details'),
      '#open'  => true,
      '#weight' => '6',
    ];
    $form['policy']['currency'] = [
      '#type' => 'select',
      '#title' => $this->t('Currency'),
      '#options' => $currency_opt,
      '#default_value' => 'HKD',
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '1',
      '#required'=> true,
    ];
    $form['policy']['paymentMode'] = [
      '#type' => 'select',
      '#title' => $this->t('Payment Mode'),
      '#options' => $payment_mode_opt,
      // '#default_value' => 'MOT',
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '2',
      '#required'=> true,
    ];
    $form['policy']['pep'] = [
      '#type' => 'textfield',
      '#title' => $this->t('PEP'),
      '#maxlength' => 255,
      '#weight' => '3',
      '#required'=> true,
    ];
    $form['policy']['another_person'] = [
      '#type' => 'select',
      '#title' => $this->t('Acting on behalf of another person'),
      '#options' => $yn_opt,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '4',
      '#required'=> true,
    ];
    $form['policy']['ecopy'] = [
      '#type' => 'select',
      '#title' => $this->t('eCopy'),
      '#options' => $yn_opt,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '5',
      '#required'=> true,
    ];
    $form['information'] = [
      '#type'  => 'details',
      '#title' => $this->t('Coverage Information'),
      '#open'  => true,
      '#weight' => '7',
    ];
    $form['information']['plan_code'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Plan Code'),
      '#maxlength' => 255,
      '#weight' => '1',
      '#required'=> true,
    ];
    $form['information']['face_amount'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Face amount (TBC)'),
      '#maxlength' => 255,
      '#weight' => '2',
      '#required'=> true,
    ];
    $form['information']['plan_level'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Plan level (RS)'),
      '#maxlength' => 255,
      '#weight' => '3',
      '#required'=> true,
    ];
    $form['information']['family_package'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Family Package (TBC)'),
      '#maxlength' => 255,
      '#weight' => '4',
      '#required'=> true,
    ];
    $form['micellaneous'] = [
      '#type'  => 'details',
      '#title' => $this->t('Micellaneous'),
      '#open'  => true,
      '#weight' => '8',
    ];
    $form['micellaneous']['replacement_declaration'] = [
      '#type' => 'select',
      '#title' => $this->t('Replacement Declaration'),
      '#options' => $yn_opt,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '1',
      '#required'=> true,
    ];
    $form['micellaneous']['fna'] = [
      '#type' => 'select',
      '#title' => $this->t('FNA'),
      '#options' => $yn_opt,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '2',
      '#required'=> true,
    ];
    $form['health_details'] = [
      '#type'  => 'details',
      '#title' => $this->t('Health Details'),
      '#open'  => true,
      '#weight' => '9',
    ];
    $form['health_details']['health_details_q_1'] = [
      '#type' => 'radios',
      '#title' => 'Q1. Have any of your immediate family members (parents or siblings) whether living or dead ever suffered from cancer, alzheimer’s disease, parkinson disease, or other hereditary disease at or before the age of 60? 
      <br><br>Q1. 您的直系親屬（父母或兄弟姐妹）是否在 60 歲或之前患有癌症、阿爾滋海默氏症、柏金遜症或其他遺傳病？',
      '#options' => $yn_opt,
      // '#required' => TRUE,
    ];
    $form['health_details']['health_details_q_2'] = [
      '#type' => 'radios',
      '#title' => 'Q1. Have any of your immediate family members (parents or siblings) whether living or dead ever suffered from cancer, alzheimer’s disease, parkinson disease, or other hereditary disease at or before the age of 60? 
      <br><br>Q1. 您的直系親屬（父母或兄弟姐妹）是否在 60 歲或之前患有癌症、阿爾滋海默氏症、柏金遜症或其他遺傳病？',
      '#options' => $yn_opt,
      // '#required' => TRUE,
    ];
    $form['health_details']['health_details_q_3'] = [
      '#type' => 'radios',
      '#title' => 'Q3. In the past 5 years, have you ever suffered from physical disabilities, or illness related to nervous system, musculoskeletal system, skin lesion, autoimmune disease, or hearing disorder?
      <br><br>Q3. 在過去 5 年中，您是否因身體殘疾或被診斷有神經系統、肌肉骨骼系統、皮膚病變、自身免疫性疾病或聽覺障礙?',
      '#options' => $yn_opt,
      // '#required' => TRUE,
    ];
    $form['health_details']['health_details_q_4'] = [
      '#type' => 'radios',
      '#title' => 'a. Had any kind of disease or sickness or injury that has not been healed completely, OR
      <br>b. Been in a hospital or sanatorium for surgery, observation or treatment for a period of 14 consecutive days or more ?
      <br><br>a. 任何未完全治癒的疾病或者疾病,受傷未完全康復，或
      <br>b. 有關的疾病住院治療、接受手術或持續接受藥物治療連續 14 天或更長時間？',
      '#options' => $yn_opt,
      // '#required' => TRUE,
    ];
    $form['health_details']['health_details_q_5'] = [
      '#type' => 'radios',
      '#title' => 'Q4. In the past 12 months, have you ever undergone any unexplained weight loss of more than 5kgs, persistent fever, unexplained bleeding, any medical sign or symptoms, or medical check up with abnormal result, for which further testing, surgery or treatment was recommended, or have not sought for medical of a registered medical practitioner ?
      <br><br>Q4. 在過去 12 個月內，您是否有任何原因不明的體重減輕超過 5 公斤、持續發燒或不明原因的出血、任何醫學體徵或症狀、或體檢結果異常，您仍在調查中，或等待進一步檢查, 醫療建議或手術治療, 或沒有尋求醫生的醫療建議？',
      '#options' => $yn_opt,
      // '#required' => TRUE,
    ];
    $form['agents_statement'] = [
      '#type'  => 'details',
      '#title' => $this->t("Agent's Statement"),
      '#open'  => true,
      '#weight' => '10',
    ];
    $form['agents_statement']['agents_code'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Agent Code'),
      '#maxlength' => 255,
      '#weight' => '4',
      '#required'=> true,
    ];
    $form['billing_info'] = [
      '#type'  => 'details',
      '#title' => $this->t('Billing Info'),
      '#open'  => true,
      '#weight' => '11',
    ];
    $form['billing_info']['billing_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Billing Type'),
      '#options' => $bill_type_opt,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '1',
      '#required'=> true,
    ];
    $form['billing_info']['tokenized_card_number'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Tokenized Card Number'),
      '#maxlength' => 255,
      '#weight' => '2',
      '#required'=> true,
    ];
    $form['billing_info']['cardHolderName'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Cardholder Name'),
      '#maxlength' => 255,
      '#weight' => '3',
      '#required'=> true,
    ];
    $form['billing_info']['cardholder_id_number'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Cardholder ID Number'),
      '#maxlength' => 255,
      '#weight' => '4',
      '#required'=> true,
    ];
    $form['billing_info']['card_expiry_date'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Card Expiry Date'),
      '#maxlength' => 255,
      '#weight' => '5',
      '#required'=> true,
    ];
    $form['billing_info']['initial_premium'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Initial Premium (includes levy and discount)'),
      '#maxlength' => 255,
      '#weight' => '6',
      '#required'=> true,
    ];
    $form['billing_info']['modal_premium_payment'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Modal Premium Payment '),
      '#maxlength' => 255,
      '#weight' => '7',
      '#required'=> true,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#weight' => '20',
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
    $order = array();
    $order['referenceNumber'] = $form_state->getValue('referenceNumber');
    $order['aeonRefNumber'] = $form_state->getValue('aeonRefNumber');
    $order['same_as_owner'] = $form_state->getValue('same_as_owner');
    $order['surname'] = $form_state->getValue('surname');
    $order['givenName'] = $form_state->getValue('givenName');
    $order['chineseName'] = $form_state->getValue('chineseName');
    $order['relationship'] = $form_state->getValue('relationship');
    $order['identityType'] = $form_state->getValue('identityType');
    $order['identityNumber'] = $form_state->getValue('identityNumber');
    $order['issueCountry'] = $form_state->getValue('issueCountry');
    $order['gender'] = $form_state->getValue('gender');
    $order['isPermanentHkid'] = $form_state->getValue('isPermanentHkid');
    $order['birthDate'] = $form_state->getValue('birthDate');
    $order['marital_status'] = $form_state->getValue('marital_status');
    $order['nationality'] = $form_state->getValue('nationality');
    $order['taxResidency'] = $form_state->getValue('taxResidency');
    $order['taxResidencyTin'] = $form_state->getValue('taxResidencyTin');
    $order['email'] = $form_state->getValue('email');
    $order['residence_address1'] = $form_state->getValue('residence_address1');
    $order['residence_address2'] = $form_state->getValue('residence_address2');
    $order['residence_address3'] = $form_state->getValue('residence_address3');
    $order['residence_city'] = $form_state->getValue('residence_city');
    $order['residence_country'] = $form_state->getValue('residence_country');
    $order['mailing_address1'] = $form_state->getValue('mailing_address1');
    $order['mailing_address2'] = $form_state->getValue('mailing_address2');
    $order['mailing_address3'] = $form_state->getValue('mailing_address3');
    $order['mailing_city'] = $form_state->getValue('mailing_city');
    $order['mailing_country'] = $form_state->getValue('mailing_country');
    $order['occupationCode'] = $form_state->getValue('occupationCode');
    $order['mobile'] = $form_state->getValue('mobile');
    $order['smoker'] = $form_state->getValue('smoker');
    $order['monthly_income'] = $form_state->getValue('monthly_income');
    $order['solicitation'] = $form_state->getValue('solicitation');
    $order['opt_out_reason'] = $form_state->getValue('opt_out_reason');
    $order['customer_insured_surname'] = $form_state->getValue('customer_insured_surname');
    $order['customer_insured_givenName'] = $form_state->getValue('customer_insured_givenName');
    $order['customer_insured_chineseName'] = $form_state->getValue('customer_insured_chineseName');
    $order['customer_insured_identityType'] = $form_state->getValue('customer_insured_identityType');
    $order['customer_insured_identityNumber'] = $form_state->getValue('customer_insured_identityNumber');
    $order['customer_insured_issueCountry'] = $form_state->getValue('customer_insured_issueCountry');
    $order['customer_insured_gender'] = $form_state->getValue('customer_insured_gender');
    $order['customer_insured_isPermanentHkid'] = $form_state->getValue('customer_insured_isPermanentHkid');
    $order['customer_insured_birthDate'] = $form_state->getValue('customer_insured_birthDate');
    $order['customer_insured_marital_status'] = $form_state->getValue('customer_insured_marital_status');
    $order['customer_insured_nationality'] = $form_state->getValue('customer_insured_nationality');
    $order['customer_insured_taxResidency'] = $form_state->getValue('customer_insured_taxResidency');
    $order['customer_insured_taxResidencyTin'] = $form_state->getValue('customer_insured_taxResidencyTin');
    $order['customer_insured_email'] = $form_state->getValue('customer_insured_email');
    $order['customer_insured_residence_address1'] = $form_state->getValue('customer_insured_residence_address1');
    $order['customer_insured_residence_address2'] = $form_state->getValue('customer_insured_residence_address2');
    $order['customer_insured_residence_address3'] = $form_state->getValue('customer_insured_residence_address3');
    $order['customer_insured_residence_city'] = $form_state->getValue('customer_insured_residence_city');
    $order['customer_insured_residence_country'] = $form_state->getValue('customer_insured_residence_country');
    $order['customer_insured_mailing_address1'] = $form_state->getValue('customer_insured_mailing_address1');
    $order['customer_insured_mailing_address2'] = $form_state->getValue('customer_insured_mailing_address2');
    $order['customer_insured_mailing_address3'] = $form_state->getValue('customer_insured_mailing_address3');
    $order['customer_insured_mailing_city'] = $form_state->getValue('customer_insured_mailing_city');
    $order['customer_insured_mailing_country'] = $form_state->getValue('customer_insured_mailing_country');
    $order['customer_insured_occupations_code'] = $form_state->getValue('customer_insured_occupations_code');
    $order['customer_insured_mobile'] = $form_state->getValue('customer_insured_mobile');
    $order['customer_insured_smoker'] = $form_state->getValue('customer_insured_smoker');
    $order['customer_insured_monthly_income'] = $form_state->getValue('customer_insured_monthly_income');
    $order['customer_insured_solicitation'] = $form_state->getValue('customer_insured_solicitation');
    $order['customer_insured_opt_out_reason'] = $form_state->getValue('customer_insured_opt_out_reason');
    $order['customer_payor_surname'] = $form_state->getValue('customer_payor_surname');
    $order['customer_payor_givenName'] = $form_state->getValue('customer_payor_givenName');
    $order['customer_payor_chineseName'] = $form_state->getValue('customer_payor_chineseName');
    $order['customer_payor_identityType'] = $form_state->getValue('customer_payor_identityType');
    $order['customer_payor_identityNumber'] = $form_state->getValue('customer_payor_identityNumber');
    $order['customer_payor_gender'] = $form_state->getValue('customer_payor_gender');
    $order['customer_payor_birthDate'] = $form_state->getValue('customer_payor_birthDate');
    $order['beneficiary_relationship'] = $form_state->getValue('beneficiary_relationship');
    $order['currency'] = $form_state->getValue('currency');
    $order['paymentMode'] = $form_state->getValue('paymentMode');
    $order['pep'] = $form_state->getValue('pep');
    $order['another_person'] = $form_state->getValue('another_person');
    $order['ecopy'] = $form_state->getValue('ecopy');
    $order['plan_code'] = $form_state->getValue('plan_code');
    $order['face_amount'] = $form_state->getValue('face_amount');
    $order['plan_level'] = $form_state->getValue('plan_level');
    $order['family_package'] = $form_state->getValue('family_package');
    $order['replacement_declaration'] = $form_state->getValue('replacement_declaration');
    $order['fna'] = $form_state->getValue('fna');
    $order['health_details_q_1'] = $form_state->getValue('health_details_q_1');
    $order['health_details_q_2'] = $form_state->getValue('health_details_q_2');
    $order['health_details_q_3'] = $form_state->getValue('health_details_q_3');
    $order['health_details_q_4'] = $form_state->getValue('health_details_q_4');
    $order['health_details_q_5'] = $form_state->getValue('health_details_q_5');
    $order['agents_code'] = $form_state->getValue('agents_code');
    $order['billing_type'] = $form_state->getValue('billing_type');
    $order['tokenized_card_number'] = $form_state->getValue('tokenized_card_number');
    $order['cardHolderName'] = $form_state->getValue('cardHolderName');
    $order['cardholder_id_number'] = $form_state->getValue('cardholder_id_number');
    $order['card_expiry_date'] = $form_state->getValue('card_expiry_date');
    $order['initial_premium'] = $form_state->getValue('initial_premium');
    $order['modal_premium_payment'] = $form_state->getValue('modal_premium_payment');
    $order['customer_id'] = 12;
    $order['status'] = 1;
    $order['created_at'] = time();
    $order['created_by'] = \Drupal::currentUser()->id();
    $order['updated_at'] = time();
    $order['updated_by'] = \Drupal::currentUser()->id();
    OrderController::update_order($order);
    \Drupal::messenger()->addMessage('Order has been updated');
  }

}
