<?php

namespace Drupal\chubb_life\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\chubb_life\Controller\AttributeController;
use Drupal\chubb_life\Controller\OrderController;
use Drupal\chubb_life\Controller\ProductController;
use Drupal\chubb_life\Controller\CustomerController;
use Drupal\Core\Url;

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
  public $import_customer_id;
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $order_id = NULL) {
    if (isset($order_id)) {
      $this->order_id = $order_id;
    }
    $this->import_customer_id = \Drupal::request()->query->get('customer_id'); 
    $reference_number = 'NEW';
    if (is_numeric($order_id)) {
      $record= OrderController::get_order_by_id($order_id);
      $reference_number = sprintf('TM%06d',$order_id);

      $customer_surname = $record['owner']['surname'];
      $customer_givenName = $record['owner']['givenName'];
      $customer_gender = $record['owner']['gender'];
      $customer_mobile = $record['owner']['mobile'];
      $customer_identityNumber = $record['owner']['identityNumber'];
      $customer_birthDate = $record['owner']['birthDate'];
      $customer_residence_address1 = $record['owner']['residence_address1'];
      $customer_email = $record['owner']['email'];
      $customer_marital = $record['owner']['marital'];
      
    }else if(is_numeric($this->import_customer_id)){
      $customer = CustomerController::get_import_customer_by_id($this->import_customer_id);
      $customer_surname = explode(' ',$customer['name'])[0];
      $customer_givenName = trim(str_replace($customer_surname, "", $customer['name']));
      $customer_gender = $customer['gender'];
      $customer_mobile = $customer['tel_mbl'];
      $customer_identityNumber = $customer['hkid'];
      $customer_birthDate = $customer['dob'];
      $customer_residence_address1 = $customer['address'];
      $customer_email = $customer['email'];
      $customer_marital = $customer['married_status'];
    }else{
      $customer_surname = '';
      $customer_givenName = '';
      $customer_gender = '';
      $customer_mobile = '';
      $customer_identityNumber = '';
      $customer_birthDate = '';
      $customer_residence_address1 = '';
      $customer_email = '';
      $customer_marital = '';
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
    $dda_setup_opt = AttributeController::get_dda_setup_options();
    $plan_code_opt = ProductController::get_plan_code_options();
    $plan_level_opt = ProductController::get_plan_level_options();
    $product_name_opt = ProductController::get_product_name_options();
    $beneficiary_relationship_opt = AttributeController::get_beneficiary_relationship_options();
    $face_amount_opt = AttributeController::get_face_amount_options();
    $promotion_code_arr = AttributeController::get_promotion_code_arr();
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id()); // pass your uid
    $agent_code = $user->field_agentcode->value;
    $agent_name = $user->field_agentname->value;
    $form['agents_statement'] = [
      '#type'  => 'details',
      '#title' => $this->t("Agent's Statement"),
      '#open'  => true,
      '#weight' => '1',
    ];
    $form['agents_statement']['agentCode'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Agent Code'),
      '#default_value' => isset($record['agentCode'])?$record['agentCode']:$agent_code,
      '#maxlength' => 255,
      '#attributes' => [
        'readonly' => 'readonly',
      ],
      '#weight' => '4',
      '#required'=> true,
    ];
    $form['agents_statement']['agentName'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Agent Name'),
      '#default_value' => isset($record['agentName'])?$record['agentName']:$agent_name,
      '#maxlength' => 255,
      '#attributes' => [
        'readonly' => 'readonly',
      ],
      '#weight' => '5',
      '#required'=> true,
    ];
    $form['application'] = [
      '#type'  => 'details',
      '#title' => $this->t('Application Detail'),
      '#open'  => true,
      '#weight' => '2',
    ];
    // $form['application']['referenceNumber'] = [
    //   '#type' => 'markup',
    //   '#title' => $this->t('Reference Number:'),
    //   '#maxlength' => 45,
    //   '#weight' => '1',
    //   '#default_value' => isset($record['referenceNumber'])?$record['referenceNumber']:'',
    //   '#attributes' => [
    //     'readonly' => 'readonly',
    //   ],
    //   '#required'=> true,
    // ];
    $form['application']['aeonRefNumber'] = [
      '#type' => 'textfield',
      '#title' => $this->t('AEONRefNum'),
      '#prefix' => '<div class="form_item_maxwidth">Reference Number: '.$reference_number.'</div>',
      '#maxlength' => 45,
      '#weight' => '2',
      '#default_value' => isset($record['aeonRefNumber'])?$record['aeonRefNumber']:'',
      '#required'=> true,
    ];
    $form['customer_owner'] = [
      '#type'  => 'details',
      '#title' => $this->t('Customer Details (Owner)'),
      '#open'  => true,
      '#weight' => '3',
    ];
    $form['customer_owner']['same_as_owner'] = [
      '#type' => 'select',
      '#title' => $this->t('Insured is same as owner'),
      '#weight' => '4',
      '#options' => $yn_opt,
      '#default_value' => isset($record['same_as_owner'])?$record['same_as_owner']:'Y',
      '#attributes' => [
        'class' => ['noselect2'],
        'id' => 'same_as_owner',
      ],
      '#empty_option' => '--Select--',
      '#weight' => '1',
      '#required'=> true,
    ];
    $form['customer_owner']['surname'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last Name (English)'),
      '#maxlength' => 255,
      '#size' => 64,
      '#weight' => '2',
      '#default_value' => $customer_surname,
      '#required'=> true,
    ];
    $form['customer_owner']['givenName'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First Name (English)'),
      '#maxlength' => 255,
      '#size' => 64,
      '#weight' => '3',
      '#default_value' => $customer_givenName,
      '#required'=> true,
    ];
    $form['customer_owner']['chineseName'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name (Chinese)'),
      '#maxlength' => 255,
      '#size' => 64,
      '#weight' => '4',
      '#default_value' => isset($record['owner']['chineseName'])?$record['owner']['chineseName']:'',
      // '#required'=> true,
    ];
    $form['customer_owner']['relationship'] = [
      '#type' => 'select',
      '#title' => $this->t('Relationship to Proposed Insured'),
      '#options' => $relation_opt,
      '#default_value' => isset($record['owner']['relationship'])?$record['owner']['relationship']:'',
      '#attributes' => [
        'class' => ['noselect2'],
        'id' => 'relationship',
      ],
      '#empty_option' => '--Select--',
      '#weight' => '5',
      '#required'=> true,
    ];
    $form['customer_owner']['identityType'] = [
      '#type' => 'select',
      '#title' => $this->t('ID Type'),
      '#options' => $id_type_opt,
      '#default_value' => isset($record['owner']['identityType'])?$record['owner']['identityType']:'I',
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#empty_option' => '--Select--',
      '#weight' => '6',
      '#required'=> true,
    ];
    $form['customer_owner']['identityNumber'] = [
      '#type' => 'textfield',
      '#title' => $this->t('HKID Card Number'),
      '#default_value' => $customer_identityNumber,
      '#maxlength' => 9,
      '#attributes' => [
        'placeholder' => 'Z1234567',
      ],
      '#weight' => '7',
      '#required'=> true,
    ];
    $form['customer_owner']['issueCountry'] = [
      '#type' => 'select',
      '#title' => $this->t('Country of Issue'),
      '#options' => $country_opt,
      '#default_value' => isset($record['owner']['issueCountry'])?$record['owner']['issueCountry']:'HK',
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
      '#default_value' => $customer_gender,
      '#attributes' => [
        'class' => ['noselect2'],
        'id' => 'owner_gender',
      ],
      '#empty_option' => '--Select--',
      '#weight' => '9',
      '#required'=> true,
    ];
    $form['customer_owner']['isPermanentHkid'] = [
      '#type' => 'select',
      '#title' => $this->t('HK Permanent ID Card holder'),
      '#options' => $yn_opt,
      '#default_value' => isset($record['owner']['isPermanentHkid'])?$record['owner']['isPermanentHkid']:'Y',
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#empty_option' => '--Select--',
      '#weight' => '10',
    ];
    $form['customer_owner']['birthDate'] = [
      '#type' => 'date',
      '#title' => $this->t('Date of Birth'),
      '#default_value' => $customer_birthDate,
      '#attributes' => [
        'id' => 'owner_birthDate',
      ],
      '#weight' => '11',
      '#required'=> true,
    ];
    $form['customer_owner']['marital'] = [
      '#type' => 'select',
      '#title' => $this->t('Marital Status'),
      '#options' => $marital_status_opt,
      '#default_value' => $customer_marital,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#empty_option' => '--Select--',
      '#weight' => '11',
      '#required'=> true,
    ];
    $form['customer_owner']['nationality'] = [
      '#type' => 'select',
      '#title' => $this->t('Nationality'),
      '#options' => $country_opt,
      '#default_value' => isset($record['owner']['nationality'])?$record['owner']['nationality']:'HK',
      '#attributes' => [
        'class' => ['country_select'],
      ],
      '#weight' => '12',
      '#required'=> true,
    ];
    $form['customer_owner']['taxResidency1'] = [
      '#type' => 'select',
      '#title' => $this->t('Tax Residency 1'),
      '#options' => $country_opt,
      '#default_value' => isset($record['owner']['taxResidency1'])?$record['owner']['taxResidency1']:'HK',
      '#attributes' => [
        'class' => ['country_select'],
        'placeholder' => 'Z1234567',
      ],
      '#weight' => '13',
      '#required'=> true,
    ];
    $form['customer_owner']['taxResidencyTin1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Tax Residency TIN 1'),
      '#maxlength' => 9,
      '#default_value' => isset($record['owner']['taxResidencyTin1'])?$record['owner']['taxResidencyTin1']:'',
      '#attributes' => [
        'placeholder' => 'Z1234567',
      ],
      '#weight' => '14',
      '#required'=> true,
    ];
    $owner_taxResidency_showmore_title = 'Show More';
    $owner_taxResidency_showmore_class = ['owner_taxResidency_showmore'];
    if(!empty($record['owner']['taxResidencyTin2'])||!empty($record['owner']['taxResidencyTin3'])
    ){
      $owner_taxResidency_showmore_title = 'Show Less';
    }else{
      $owner_taxResidency_showmore_class[] = 'show_more';
    }

    $form['customer_owner']['owner_taxResidency_showmore'] = [
      '#type' => 'button',
      '#value' => $owner_taxResidency_showmore_title,
      '#attributes' => [
        'onclick' => 'return false;',
        'id' => 'owner_taxResidency_showmore',
        'class' => $owner_taxResidency_showmore_class,
        // 'class' => ['owner_taxResidency_showmore','show_more'],
      ],
      '#prefix' => '<div class="form_item_maxwidth">', '#suffix' => '</div>',
      '#weight' => '15',
    ];
    $form['customer_owner']['taxResidency2'] = [
      '#type' => 'select',
      '#title' => $this->t('Tax Residency 2'),
      '#options' => $country_opt,
      '#default_value' => isset($record['owner']['taxResidency2'])?$record['owner']['taxResidency2']:'',
      '#attributes' => [
        'class' => ['country_select'],
      ],
      '#empty_option' => '--Select--',
      '#weight' => '16',
      // '#required'=> true,
    ];
    $form['customer_owner']['taxResidencyTin2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Tax Residency TIN 2'),
      '#maxlength' => 9,
      '#default_value' => isset($record['owner']['taxResidencyTin2'])?$record['owner']['taxResidencyTin2']:'',
      '#attributes' => [
        'placeholder' => 'Z1234567',
      ],
      '#weight' => '17',
      // '#required'=> true,
    ];
    $form['customer_owner']['taxResidency3'] = [
      '#type' => 'select',
      '#title' => $this->t('Tax Residency 3'),
      '#options' => $country_opt,
      '#default_value' => isset($record['owner']['taxResidency3'])?$record['owner']['taxResidency3']:'',
      '#attributes' => [
        'class' => ['country_select'],
      ],
      '#empty_option' => '--Select--',
      '#weight' => '18',
      // '#required'=> true,
    ];
    $form['customer_owner']['taxResidencyTin3'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Tax Residency TIN 3'),
      '#maxlength' => 9,
      '#default_value' => isset($record['owner']['taxResidencyTin3'])?$record['owner']['taxResidencyTin3']:'',
      '#attributes' => [
        'placeholder' => 'Z1234567',
      ],
      '#weight' => '19',
      // '#required'=> true,
    ];
    $form['customer_owner']['email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email Address'),
      '#default_value' => $customer_email,
      '#maxlength' => 255,
      '#weight' => '20',
      '#required'=> true,
    ];
    $form['customer_owner']['residence_address1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Flat/Floor/Block'),
      '#default_value' => $customer_residence_address1,
      '#prefix' => '<div class="form_item_maxwidth">Residence Address</div>',
      '#weight' => '21',
      '#required'=> true,
    ];
    $form['customer_owner']['residence_address2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Building/Street Number'),
      '#default_value' => isset($record['owner']['residence_address2'])?$record['owner']['residence_address2']:'',
      '#weight' => '22',
      '#required'=> true,
    ];
    $form['customer_owner']['residence_address3'] = [
      '#type' => 'textfield',
      '#title' => $this->t('District'),
      '#default_value' => isset($record['owner']['residence_address3'])?$record['owner']['residence_address3']:'',
      '#weight' => '23',
    ];
    $form['customer_owner']['residence_city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('City'),
      '#default_value' => isset($record['owner']['residence_city'])?$record['owner']['residence_city']:'',
      '#weight' => '24',
      '#required'=> true,
    ];
    $form['customer_owner']['residence_country'] = [
      '#type' => 'select',
      '#title' => $this->t('Country/Region'),
      '#options' => $country_opt,
      '#default_value' => isset($record['owner']['residence_country'])?$record['owner']['residence_country']:'HK',
      '#attributes' => [
        'class' => ['country_select'],
      ],
      '#weight' => '25',
      '#required'=> true,
    ];
    $form['customer_owner']['mailing_same_as_residence'] = [
      '#type' => 'select',
      '#title' => $this->t('Mailling Address same as Residence'),
      '#options' => $yn_opt,
      '#default_value' => isset($record['owner']['mailing_same_as_residence'])?$record['owner']['mailing_same_as_residence']:'Y',
      '#attributes' => [
        'class' => ['noselect2'],
        'id' => 'mailing_same_as_residence',
      ],
      '#empty_option' => '--Select--',
      '#weight' => '26',
    ];
    $form['customer_owner']['mailing_address1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Flat/Floor/Block'),
      '#default_value' => isset($record['owner']['mailing_address1'])?$record['owner']['mailing_address1']:'',
      '#prefix' => '<div class="form_item_maxwidth">Mailing Address</div>',
      '#weight' => '27',
      // '#required'=> true,
    ];
    $form['customer_owner']['mailing_address2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Building/Street Number'),
      '#default_value' => isset($record['owner']['mailing_address2'])?$record['owner']['mailing_address2']:'',
      '#weight' => '28',
      // '#required'=> true,
    ];
    $form['customer_owner']['mailing_address3'] = [
      '#type' => 'textfield',
      '#title' => $this->t('District'),
      '#default_value' => isset($record['owner']['mailing_address3'])?$record['owner']['mailing_address3']:'',
      '#weight' => '29',
    ];
    $form['customer_owner']['mailing_city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('City'),
      '#default_value' => isset($record['owner']['mailing_city'])?$record['owner']['mailing_city']:'',
      '#weight' => '30',
      // '#required'=> true,
    ];
    $form['customer_owner']['mailing_country'] = [
      '#type' => 'select',
      '#title' => $this->t('Country/Region'),
      '#options' => $country_opt,
      '#default_value' => isset($record['owner']['mailing_country'])?$record['owner']['mailing_country']:'HK',
      '#attributes' => [
        'class' => ['country_select'],
      ],
      '#weight' => '31',
      // '#required'=> true,
    ];
    $form['customer_owner']['occupationCode'] = [
      '#type' => 'select',
      '#title' => $this->t('Occupations Code'),
      '#options' => $occupations_opt,
      '#default_value' => isset($record['owner']['occupationCode'])?$record['owner']['occupationCode']:'',
      '#attributes' => [
        'class' => ['occupation_select'],
      ],
      '#weight' => '32',
      '#required'=> true,
    ];
    $form['customer_owner']['mobile'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Mobile Phone Number'),
      '#default_value' => $customer_mobile,
      '#weight' => '33',
      '#required'=> true,
    ];
    $form['customer_owner']['smoker'] = [
      '#type' => 'select',
      '#title' => $this->t('Smoker'),
      '#options' => $yn_opt,
      '#empty_option' => '--Select--',
      '#default_value' => isset($record['owner']['smoker'])?$record['owner']['smoker']:'',
      '#attributes' => [
        'class' => ['noselect2'],
        'id' => 'owner_smoker',
      ],
      '#weight' => '34',
      '#required'=> true,
    ];
    $form['customer_owner']['monthly_income'] = [
      '#type' => 'select',
      '#title' => $this->t('Monthly income'),
      '#options' => $monthly_income_opt,
      '#empty_option' => '--Select--',
      '#default_value' => isset($record['owner']['monthly_income'])?$record['owner']['monthly_income']:'',
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '35',
      '#required'=> true,
    ];
    $form['customer_owner']['solicitation'] = [
      '#type' => 'select',
      '#title' => $this->t('Solicitation (Opt out Indicator)'),
      '#options' => $solicitation_opt,
      '#empty_option' => '--Select--',
      '#default_value' => isset($record['owner']['solicitation'])?$record['owner']['solicitation']:'',
      '#attributes' => [
        'class' => ['noselect2'],
        'id' => 'customer_owner_solicitation',
      ],
      '#weight' => '36',
      '#required'=> true,
    ];
    $form['customer_owner']['opt_out_reason'] = [
      '#type' => 'select',
      '#title' => $this->t('Opt out reason'),
      '#options' => $opt_out_reason_opt,
      '#empty_option' => '--Select--',
      '#default_value' => isset($record['owner']['opt_out_reason'])?$record['owner']['opt_out_reason']:'',
      '#attributes' => [
        'class' => ['noselect2'],
        'id' => 'customer_owner_opt_out_reason',
      ],
      '#weight' => '37',
    ];
    $form['customer_insured'] = [
      '#type'  => 'details',
      '#title' => $this->t('Customer Details (Insured)'),
      '#open'  => true,
      '#weight' => '4',
    ];
    $form['customer_insured']['customer_insured_surname'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last Name (English)'),
      '#default_value' => isset($record['insured']['surname'])?$record['insured']['surname']:'',
      '#maxlength' => 255,
      '#weight' => '2',
    ];
    $form['customer_insured']['customer_insured_givenName'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First Name (English)'),
      '#default_value' => isset($record['insured']['givenName'])?$record['insured']['givenName']:'',
      '#maxlength' => 255,
      '#weight' => '3',
    ];
    $form['customer_insured']['customer_insured_chineseName'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name (Chinese)'),
      '#default_value' => isset($record['insured']['chineseName'])?$record['insured']['chineseName']:'',
      '#maxlength' => 255,
      '#weight' => '4',
    ];
    $form['customer_insured']['customer_insured_identityType'] = [
      '#type' => 'select',
      '#title' => $this->t('ID Type'),
      '#options' => $id_type_opt,
      '#empty_option' => '--Select--',
      '#default_value' => isset($record['insured']['identityType'])?$record['insured']['identityType']:'I',
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '6',
    ];
    $form['customer_insured']['customer_insured_identityNumber'] = [
      '#type' => 'textfield',
      '#title' => $this->t('HKID Card Number'),
      '#default_value' => isset($record['insured']['identityNumber'])?$record['insured']['identityNumber']:'',
      '#attributes' => [
        'placeholder' => 'Z1234567',
      ],
      '#maxlength' => 9,
      '#weight' => '7',
    ];
    $form['customer_insured']['customer_insured_issueCountry'] = [
      '#type' => 'select',
      '#title' => $this->t('Country of Issue'),
      '#options' => $country_opt,
      '#default_value' => isset($record['insured']['issueCountry'])?$record['insured']['issueCountry']:'HK',
      '#attributes' => [
        'class' => ['country_select'],
      ],
      '#weight' => '8',
    ];
    $form['customer_insured']['customer_insured_gender'] = [
      '#type' => 'select',
      '#title' => $this->t('Gender'),
      '#options' => $gender_opt,
      '#empty_option' => '--Select--',
      '#default_value' => isset($record['insured']['gender'])?$record['insured']['gender']:'',
      '#attributes' => [
        'class' => ['noselect2'],
        'id' => 'insured_gender',
      ],
      '#weight' => '9',
    ];
    $form['customer_insured']['customer_insured_isPermanentHkid'] = [
      '#type' => 'select',
      '#title' => $this->t('HK Permanent ID Card holder'),
      '#options' => $yn_opt,
      '#empty_option' => '--Select--',
      '#default_value' => isset($record['insured']['isPermanentHkid'])?$record['insured']['isPermanentHkid']:'',
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '10',
    ];
    $form['customer_insured']['customer_insured_birthDate'] = [
      '#type' => 'date',
      '#title' => $this->t('Date of Birth'),
      '#default_value' => isset($record['insured']['birthDate'])?$record['insured']['birthDate']:'',
      '#attributes' => [
        'id' => 'insured_birthDate',
      ],
      '#weight' => '11',
    ];
    $form['customer_insured']['customer_insured_marital'] = [
      '#type' => 'select',
      '#title' => $this->t('Marital Status'),
      '#options' => $marital_status_opt,
      '#empty_option' => '--Select--',
      '#default_value' => isset($record['insured']['marital'])?$record['insured']['marital']:'',
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '11',
    ];
    $form['customer_insured']['customer_insured_nationality'] = [
      '#type' => 'select',
      '#title' => $this->t('Nationality'),
      '#options' => $country_opt,
      '#default_value' => isset($record['insured']['nationality'])?$record['insured']['nationality']:'HK',
      '#attributes' => [
        'class' => ['country_select'],
      ],
      '#weight' => '12',
    ];
    $form['customer_insured']['customer_insured_taxResidency1'] = [
      '#type' => 'select',
      '#title' => $this->t('Tax Residency 1'),
      '#options' => $country_opt,
      '#default_value' => isset($record['insured']['taxResidency1'])?$record['insured']['taxResidency1']:'HK',
      '#attributes' => [
        'class' => ['country_select'],
      ],
      '#weight' => '13',
    ];
    $form['customer_insured']['customer_insured_taxResidencyTin1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Tax Residency TIN 1'),
      '#default_value' => isset($record['insured']['taxResidencyTin1'])?$record['insured']['taxResidencyTin1']:'',
      '#attributes' => [
        'placeholder' => 'Z1234567',
      ],
      '#maxlength' => 9,
      '#weight' => '14',
    ];
    $insured_taxResidency_showmore_title = 'Show More';
    $insured_taxResidency_showmore_class = ['insured_taxResidency_showmore'];
    if(!empty($record['insured']['taxResidencyTin2'])||!empty($record['insured']['taxResidencyTin3'])
    ){
      $insured_taxResidency_showmore_title = 'Show Less';
    }else{
      $insured_taxResidency_showmore_class[] = 'show_more';
    }

    $form['customer_insured']['insured_taxResidency_showmore'] = [
      '#type' => 'button',
      '#value' => $insured_taxResidency_showmore_title,
      '#attributes' => [
        'onclick' => 'return false;',
        'id' => 'insured_taxResidency_showmore',
        'class' => $insured_taxResidency_showmore_class,
      ],
      '#prefix' => '<div class="form_item_maxwidth">', '#suffix' => '</div>',
      '#weight' => '15',
    ];
    $form['customer_insured']['customer_insured_taxResidency2'] = [
      '#type' => 'select',
      '#title' => $this->t('Tax Residency 2'),
      '#options' => $country_opt,
      '#default_value' => isset($record['insured']['taxResidency2'])?$record['insured']['taxResidency2']:'',
      '#attributes' => [
        'class' => ['country_select'],
      ],
      '#empty_option' => '--Select--',
      '#weight' => '16',
    ];
    $form['customer_insured']['customer_insured_taxResidencyTin2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Tax Residency TIN 2'),
      '#default_value' => isset($record['insured']['taxResidencyTin2'])?$record['insured']['taxResidencyTin2']:'',
      '#attributes' => [
        'placeholder' => 'Z1234567',
      ],
      '#maxlength' => 9,
      '#weight' => '17',
    ];
    $form['customer_insured']['customer_insured_taxResidency3'] = [
      '#type' => 'select',
      '#title' => $this->t('Tax Residency 3'),
      '#options' => $country_opt,
      '#default_value' => isset($record['insured']['taxResidency3'])?$record['insured']['taxResidency3']:'',
      '#attributes' => [
        'class' => ['country_select'],
      ],
      '#empty_option' => '--Select--',
      '#weight' => '18',
    ];
    $form['customer_insured']['customer_insured_taxResidencyTin3'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Tax Residency TIN 3'),
      '#default_value' => isset($record['insured']['taxResidencyTin3'])?$record['insured']['taxResidencyTin3']:'',
      '#attributes' => [
        'placeholder' => 'Z1234567',
      ],
      '#maxlength' => 9,
      '#weight' => '19',
    ];
    $form['customer_insured']['customer_insured_email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email Address'),
      '#default_value' => isset($record['insured']['email'])?$record['insured']['email']:'',
      '#maxlength' => 255,
      '#weight' => '20',
    ];
    
    $form['customer_insured']['customer_insured_residence_address1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Flat/Floor/Block'),
      '#default_value' => isset($record['insured']['residence_address1'])?$record['insured']['residence_address1']:'',
      '#prefix' => '<div class="form_item_maxwidth">Residence Address</div>',
      '#weight' => '21',
    ];
    $form['customer_insured']['customer_insured_residence_address2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Building/Street Number'),
      '#default_value' => isset($record['insured']['residence_address2'])?$record['insured']['residence_address2']:'',
      '#weight' => '22',
    ];
    $form['customer_insured']['customer_insured_residence_address3'] = [
      '#type' => 'textfield',
      '#title' => $this->t('District'),
      '#default_value' => isset($record['insured']['residence_address3'])?$record['insured']['residence_address3']:'',
      '#weight' => '23',
    ];
    $form['customer_insured']['customer_insured_residence_city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('City'),
      '#default_value' => isset($record['insured']['residence_city'])?$record['insured']['residence_city']:'',
      '#weight' => '24',
    ];
    $form['customer_insured']['customer_insured_residence_country'] = [
      '#type' => 'select',
      '#title' => $this->t('Country/Region'),
      '#options' => $country_opt,
      '#default_value' => isset($record['insured']['residence_country'])?$record['insured']['residence_country']:'HK',
      '#attributes' => [
        'class' => ['country_select'],
      ],
      '#weight' => '25',
    ];
    $form['customer_insured']['customer_insured_mailing_same_as_residence'] = [
      '#type' => 'select',
      '#title' => $this->t('Mailling Address same as Residence'),
      '#options' => $yn_opt,
      '#default_value' => isset($record['insured']['customer_insured_mailing_same_as_residence'])?$record['insured']['customer_insured_mailing_same_as_residence']:'Y',
      '#attributes' => [
        'class' => ['noselect2'],
        'id' => 'customer_insured_mailing_same_as_residence',
      ],
      '#empty_option' => '--Select--',
      '#weight' => '26',
    ];
    $form['customer_insured']['customer_insured_mailing_address1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Flat/FloorBlock'),
      '#default_value' => isset($record['insured']['mailing_address1'])?$record['insured']['mailing_address1']:'',
      '#prefix' => '<div class="form_item_maxwidth">Mailing Address</div>',
      '#weight' => '27',
    ];
    $form['customer_insured']['customer_insured_mailing_address2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Building/Street Number'),
      '#default_value' => isset($record['insured']['mailing_address2'])?$record['insured']['mailing_address2']:'',
      '#weight' => '28',
    ];
    $form['customer_insured']['customer_insured_mailing_address3'] = [
      '#type' => 'textfield',
      '#title' => $this->t('District'),
      '#default_value' => isset($record['insured']['mailing_address3'])?$record['insured']['mailing_address3']:'',
      '#weight' => '29',
    ];
    $form['customer_insured']['customer_insured_mailing_city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('City'),
      '#default_value' => isset($record['insured']['mailing_city'])?$record['insured']['mailing_city']:'',
      '#weight' => '30',
    ];
    $form['customer_insured']['customer_insured_mailing_country'] = [
      '#type' => 'select',
      '#title' => $this->t('Country/Region'),
      '#options' => $country_opt,
      '#default_value' => isset($record['insured']['mailing_country'])?$record['insured']['mailing_country']:'HK',
      '#attributes' => [
        'class' => ['country_select'],
      ],
      '#weight' => '31',
    ];
    $form['customer_insured']['customer_insured_occupationCode'] = [
      '#type' => 'select',
      '#title' => $this->t('Occupations Code'),
      '#options' => $occupations_opt,
      '#default_value' => isset($record['insured']['occupationCode'])?$record['insured']['occupationCode']:'',
      '#attributes' => [
        'class' => ['occupation_select'],
      ],
      '#weight' => '32',
    ];
    $form['customer_insured']['customer_insured_mobile'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Mobile Phone Number'),
      '#default_value' => isset($record['insured']['mobile'])?$record['insured']['mobile']:'',
      '#weight' => '33',
    ];
    $form['customer_insured']['customer_insured_smoker'] = [
      '#type' => 'select',
      '#title' => $this->t('Smoker'),
      '#options' => $yn_opt,
      '#empty_option' => '--Select--',
      '#default_value' => isset($record['insured']['smoker'])?$record['insured']['smoker']:'',
      '#attributes' => [
        'class' => ['noselect2'],
        'id' => 'insured_smoker',
      ],
      '#weight' => '34',
    ];
    $form['customer_insured']['customer_insured_monthly_income'] = [
      '#type' => 'select',
      '#title' => $this->t('Monthly income'),
      '#options' => $monthly_income_opt,
      '#empty_option' => '--Select--',
      '#default_value' => isset($record['insured']['monthly_income'])?$record['insured']['monthly_income']:'',
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '35',
    ];
    $form['customer_insured']['customer_insured_solicitation'] = [
      '#type' => 'select',
      '#title' => $this->t('Solicitation (Opt out Indicator)'),
      '#options' => $solicitation_opt,
      '#empty_option' => '--Select--',
      '#default_value' => isset($record['insured']['solicitation'])?$record['insured']['solicitation']:'',
      '#attributes' => [
        'class' => ['noselect2'],
        'id' => 'customer_insured_solicitation',
      ],
      '#weight' => '36',
    ];
    $form['customer_insured']['customer_insured_opt_out_reason'] = [
      '#type' => 'select',
      '#title' => $this->t('Opt out reason'),
      '#options' => $opt_out_reason_opt,
      '#empty_option' => '--Select--',
      '#default_value' => isset($record['insured']['opt_out_reason'])?$record['insured']['opt_out_reason']:'',
      '#attributes' => [
        'class' => ['noselect2'],
        'id' => 'customer_insured_opt_out_reason',
      ],
      '#weight' => '37',
    ];
    $form['customer_payor'] = [
      '#type'  => 'details',
      '#title' => $this->t('Customer Details (Payor)'),
      '#open'  => true,
      '#weight' => '5',
    ];
    $form['customer_payor']['customer_payor_surname'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last Name (English)'),
      '#default_value' => isset($record['payor']['surname'])?$record['payor']['surname']:'',
      '#maxlength' => 255,
      '#weight' => '2',
      // '#default_value' => $default_po_number,
      '#required'=> true,
    ];
    $form['customer_payor']['customer_payor_givenName'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First Name (English)'),
      '#default_value' => isset($record['payor']['givenName'])?$record['payor']['givenName']:'',
      '#maxlength' => 255,
      '#weight' => '3',
      // '#default_value' => $default_po_number,
      '#required'=> true,
    ];
    $form['customer_payor']['customer_payor_identityType'] = [
      '#type' => 'select',
      '#title' => $this->t('ID Type'),
      '#options' => $id_type_opt,
      '#empty_option' => '--Select--',
      '#default_value' => isset($record['payor']['identityType'])?$record['payor']['identityType']:'I',
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '6',
      '#required'=> true,
    ];
    $form['customer_payor']['customer_payor_identityNumber'] = [
      '#type' => 'textfield',
      '#title' => $this->t('HKID Card Number'),
      '#default_value' => isset($record['payor']['identityNumber'])?$record['payor']['identityNumber']:'',
      '#attributes' => [
        'placeholder' => 'Z1234567',
      ],
      '#maxlength' => 9,
      '#weight' => '7',
      '#required'=> true,
    ];
    $form['customer_payor']['customer_payor_gender'] = [
      '#type' => 'select',
      '#title' => $this->t('Gender'),
      '#options' => $gender_opt,
      '#empty_option' => '--Select--',
      '#default_value' => isset($record['payor']['gender'])?$record['payor']['gender']:'',
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '9',
      '#required'=> true,
    ];
    $form['customer_payor']['customer_payor_birthDate'] = [
      '#type' => 'date',
      '#title' => $this->t('Date of Birth'),
      '#default_value' => isset($record['payor']['birthDate'])?$record['payor']['birthDate']:'',
      '#weight' => '11',
      '#required'=> true,
    ];

    $form['beneficiary'] = [
      '#type'  => 'details',
      '#title' => $this->t('Beneficiary'),
      '#open'  => true,
      '#weight' => '6',
    ];
    $form['beneficiary']['beneficiary_relationship'] = [
      '#type' => 'select',
      '#title' => $this->t('Relationship'),
      '#options' => $beneficiary_relationship_opt,
      // '#empty_option' => '--Select--',
      '#default_value' => isset($record['beneficiary_relationship'])?$record['beneficiary_relationship']:'EST',
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '1',
      '#required'=> true,
    ];
    $form['policy'] = [
      '#type'  => 'details',
      '#title' => $this->t('Policy Details'),
      '#open'  => true,
      '#weight' => '7',
    ];
    $form['policy']['paymentMode'] = [
      '#type' => 'select',
      '#title' => $this->t('Payment Mode'),
      '#options' => $payment_mode_opt,
      '#empty_option' => '--Select--',
      '#default_value' => isset($record['paymentMode'])?$record['paymentMode']:'',
      '#attributes' => [
        'class' => ['noselect2'],
        'id' => 'paymentMode',
      ],
      '#weight' => '2',
      '#required'=> true,
    ];
    $form['policy']['pep'] = [
      '#type' => 'select',
      '#title' => $this->t('PEP'),
      '#options' => $yn_opt,
      '#empty_option' => '--Select--',
      '#default_value' => isset($record['pep'])?$record['pep']:'N',
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '3',
      '#required'=> true,
    ];
    $form['policy']['another_person'] = [
      '#type' => 'select',
      '#title' => $this->t('Acting on behalf of another person'),
      '#options' => $yn_opt,
      '#empty_option' => '--Select--',
      '#default_value' => isset($record['another_person'])?$record['another_person']:'N',
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
      '#empty_option' => '--Select--',
      '#default_value' => isset($record['ecopy'])?$record['ecopy']:'Y',
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
      '#weight' => '8',
    ];
    $form['information']['plan_code'] = [
      '#type' => 'select',
      '#title' => $this->t('Plan Code'),
      '#options' => $plan_code_opt,
      '#empty_option' => '--Select--',
      '#default_value' => isset($record['plan_code'])?$record['plan_code']:'',
      '#attributes' => [
        'class' => ['plan_code_select','noselect2'],
        'id'=>'plan_code',
      ],
      '#weight' => '1',
      '#required'=> true,
    ];
    $form['information']['product_name_english'] = [
      '#type' => 'textfield',
      '#title' => 'Product Name (English)',
      '#default_value' => isset($record['product_name_english'])?$record['product_name_english']:'',
      '#maxlength' => 255,
      '#attributes' => [
        'id' => 'product_name_english',
        'readonly' => 'readonly',
      ],
      '#weight' => '2',
    ];
    $form['information']['product_name_chinese'] = [
      '#type' => 'textfield',
      '#title' => 'Product Name (Chinese)',
      '#default_value' => isset($record['product_name_chinese'])?$record['product_name_chinese']:'',
      '#maxlength' => 255,
      '#attributes' => [
        'id' => 'product_name_chinese',
        'readonly' => 'readonly',
      ],
      '#weight' => '3',
    ];
    $form['information']['face_amount'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Face amount'),
      '#default_value' => isset($record['face_amount'])?$record['face_amount']:'',
      '#maxlength' => 255,
      '#attributes' => [
        'id' => 'face_amount',
        'readonly' => 'readonly',
      ],
      '#weight' => '6',
      '#required'=> true,
    ];
    $form['information']['plan_level'] = [
      '#type' => 'select',
      '#title' => $this->t('Plan Level (RS)'),
      '#options' => $plan_level_opt,
      '#default_value' => isset($record['plan_level'])?$record['plan_level']:'',
      '#attributes' => [
        'class' => ['plan_level_select','noselect2'],
        'id'=>'plan_level',
      ],
      '#weight' => '7',
      '#required'=> true,
    ];
    $form['information']['currency'] = [
      '#type' => 'select',
      '#title' => $this->t('Currency'),
      '#options' => $currency_opt,
      '#empty_option' => '--Select--',
      '#default_value' => isset($record['currency'])?$record['currency']:'HKD',
      '#default_value' => 'HKD',
      '#attributes' => [
        'class' => ['noselect2'],
        'id' => 'currency',
      ],
      '#weight' => '8',
      '#required'=> true,
    ];
    $form['information']['face_amount'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Face amount'),
      '#default_value' => isset($record['face_amount'])?$record['face_amount']:'',
      '#maxlength' => 255,
      '#attributes' => [
        'id' => 'face_amount',
        'readonly' => 'readonly',
      ],
      '#weight' => '9',
      '#required'=> true,
    ];
    $form['information']['promotion_code'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Promotion Code'),
      '#default_value' => isset($record['promotion_code'])?$record['promotion_code']:'',
      '#maxlength' => 255,
      '#attributes' => [
        'id' => 'promotion_code',
      ],
      '#weight' => '10',
      // '#required'=> true,
    ];
    $form['micellaneous'] = [
      '#type'  => 'details',
      '#title' => $this->t('Micellaneous'),
      '#open'  => true,
      '#weight' => '9',
    ];
    $form['micellaneous']['replacement_declaration'] = [
      '#type' => 'select',
      '#title' => $this->t('Replacement Declaration'),
      '#options' => $yn_opt,
      '#empty_option' => '--Select--',
      '#default_value' => isset($record['replacement_declaration'])?$record['replacement_declaration']:'',
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
      '#empty_option' => '--Select--',
      '#default_value' => isset($record['fna'])?$record['fna']:'Y',
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
      '#weight' => '10',
    ];
    $form['health_details']['health_details_q_1'] = [
      '#type' => 'radios',
      '#title' => 'Q1. Have any of your immediate family members (parents or siblings) whether living or dead ever suffered from cancer, alzheimer’s disease, parkinson disease, or other hereditary disease at or before the age of 60? 
      <br><br>Q1. 您的直系親屬（父母或兄弟姐妹）是否在 60 歲或之前患有癌症、阿爾滋海默氏症、柏金遜症或其他遺傳病？',
      '#options' => $yn_opt,
      '#default_value' => isset($record['health_details_q_1'])?$record['health_details_q_1']:'',
      // '#required' => TRUE,
    ];
    $form['health_details']['health_details_q_2'] = [
      '#type' => 'radios',
      '#title' => 'Q2. Have you ever had any cancer or carcinoma-in-situ, heart attack, stroke, HIV infection or AIDS related complications?
      <br><br>Q2. 您是否曾患過癌症或原位癌、心臟病發作、中風、愛滋病病毒感染或愛滋病相關併發症?',
      '#options' => $yn_opt,
      '#default_value' => isset($record['health_details_q_2'])?$record['health_details_q_2']:'',
      // '#required' => TRUE,
    ];
    $form['health_details']['health_details_q_3'] = [
      '#type' => 'radios',
      '#title' => 'Q3. In the past 5 years, have you ever suffered from physical disabilities, or illness related to nervous system, musculoskeletal system, skin lesion, autoimmune disease, or hearing disorder?
      <br><br>Q3. 在過去 5 年中，您是否因身體殘疾或被診斷有神經系統、肌肉骨骼系統、皮膚病變、自身免疫性疾病或聽覺障礙?',
      '#options' => $yn_opt,
      '#default_value' => isset($record['health_details_q_3'])?$record['health_details_q_3']:'',
      // '#required' => TRUE,
    ];
    $form['health_details']['health_details_q_4'] = [
      '#type' => 'radios',
      '#title' => 'a. Had any kind of disease or sickness or injury that has not been healed completely, OR
      <br>b. Been in a hospital or sanatorium for surgery, observation or treatment for a period of 14 consecutive days or more ?
      <br><br>a. 任何未完全治癒的疾病或者疾病,受傷未完全康復，或
      <br>b. 有關的疾病住院治療、接受手術或持續接受藥物治療連續 14 天或更長時間？',
      '#options' => $yn_opt,
      '#default_value' => isset($record['health_details_q_4'])?$record['health_details_q_4']:'',
      // '#required' => TRUE,
    ];
    $form['health_details']['health_details_q_5'] = [
      '#type' => 'radios',
      '#title' => 'Q4. In the past 12 months, have you ever undergone any unexplained weight loss of more than 5kgs, persistent fever, unexplained bleeding, any medical sign or symptoms, or medical check up with abnormal result, for which further testing, surgery or treatment was recommended, or have not sought for medical of a registered medical practitioner ?
      <br><br>Q4. 在過去 12 個月內，您是否有任何原因不明的體重減輕超過 5 公斤、持續發燒或不明原因的出血、任何醫學體徵或症狀、或體檢結果異常，您仍在調查中，或等待進一步檢查, 醫療建議或手術治療, 或沒有尋求醫生的醫療建議？',
      '#options' => $yn_opt,
      '#default_value' => isset($record['health_details_q_5'])?$record['health_details_q_5']:'',
      // '#required' => TRUE,
    ];
    $form['billing_info'] = [
      '#type'  => 'details',
      '#title' => $this->t('Billing Info'),
      '#open'  => true,
      '#weight' => '11',
    ];
    $form['billing_info']['billingType'] = [
      '#type' => 'select',
      '#title' => $this->t('Billing Type'),
      '#options' => $bill_type_opt,
      '#empty_option' => '--Select--',
      '#default_value' => isset($record['billingType'])?$record['billingType']:'',
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '1',
      '#required'=> true,
    ];
    $form['billing_info']['authorizationCode'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Tokenized Card Number'),
      '#default_value' => isset($record['authorizationCode'])?$record['authorizationCode']:'',
      '#maxlength' => 255,
      '#weight' => '2',
      '#required'=> true,
    ];
    $form['billing_info']['cardHolderName'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Cardholder Name'),
      '#default_value' => isset($record['cardHolderName'])?$record['cardHolderName']:'',
      '#maxlength' => 255,
      '#weight' => '3',
      '#required'=> true,
    ];
    $form['billing_info']['cardholder_id_number'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Cardholder ID Number'),
      '#default_value' => isset($record['cardholder_id_number'])?$record['cardholder_id_number']:'',
      '#maxlength' => 255,
      '#weight' => '4',
      '#required'=> true,
    ];
    $form['billing_info']['card_expiry_date'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Card Expiry Date'),
      '#default_value' => isset($record['card_expiry_date'])?$record['card_expiry_date']:'',
      '#maxlength' => 7,
      '#attributes' => [
        'placeholder' => t('MM/YYYY'),
        'id' => 'card_expiry_date',
        'onkeyup'=>"addSlashes(this)",
      ],
      '#weight' => '5',
      '#required'=> true,
    ];
    $form['billing_info']['initial_premium'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Initial Premium(includes levy discount)'),
      '#default_value' => isset($record['initial_premium'])?$record['initial_premium']:'',
      '#maxlength' => 255,
      '#attributes' => [
        'readonly' => 'readonly',
        'id' => 'initial_premium',
      ],
      '#weight' => '6',
      '#required'=> true,
    ];
    $form['billing_info']['modal_premium_payment'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Modal Premium Payment'),
      '#default_value' => isset($record['modal_premium_payment'])?$record['modal_premium_payment']:'',
      '#maxlength' => 255,
      '#attributes' => [
        'readonly' => 'readonly',
        'id' => 'modal_premium_payment',
      ],
      '#weight' => '7',
      '#required'=> true,
    ];
    $form['billing_info']['levy'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Levy'),
      '#default_value' => isset($record['levy'])?$record['levy']:'',
      '#maxlength' => 255,
      '#attributes' => [
        'readonly' => 'readonly',
        'id' => 'levy',
      ],
      '#weight' => '8',
      '#required'=> true,
    ];
    $form['billing_info']['calculate'] = [
      '#type' => 'button',
      '#value' => $this->t('Calculate'),
      '#attributes' => [
        'onclick' => 'return false;',
        'id' => 'calculate_premium',
      ],
      '#prefix' => '<div class="form_item_maxwidth">', '#suffix' => '</div>',
      '#weight' => '9',
    ];
    $form['billing_info']['remarks'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Remarks'),
      '#default_value' => isset($record['remarks'])?$record['remarks']:'',
      '#maxlength' => 255,
      '#weight' => '10',
      '#wrapper_attributes' => ['class' => ['form_item_maxwidth']],
      // '#required'=> true,
    ];
    $form['billing_info']['dda_setup'] = [
      '#type' => 'select',
      '#title' => $this->t('DDA setup'),
      '#options' => $dda_setup_opt,
      '#empty_option' => '--Select--',
      '#default_value' => isset($record['dda_setup'])?$record['dda_setup']:'3',
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '11',
      '#required'=> true,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#weight' => '20',
    ];
    $form['#attached']['drupalSettings']['promotion_code_arr'] = $promotion_code_arr;
    $form['#attached']['drupalSettings']['product_name'] = $product_name_opt;
    $form['#attached']['drupalSettings']['plan_level'] = $plan_level_opt;
    $form['#attached']['drupalSettings']['face_amount'] = $face_amount_opt;
    $form['#attached']['library'][] = 'chubb_life/chubb_life';
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
    // $card_expiry_date = $form_state->getValue('card_expiry_date');
    if (!empty($form_state->getValue('email')) &&
      !\Drupal::service('email.validator')->isValid($form_state->getValue('email'))) {
      $form_state->setErrorByName('email', $this->t('Email address is not a valid one.'));
    }
    if (!empty($form_state->getValue('customer_insured_email')) &&
      !\Drupal::service('email.validator')->isValid($form_state->getValue('customer_insured_email'))) {
      $form_state->setErrorByName('customer_insured_email', $this->t('Email address is not a valid one.'));
    }

    if (!preg_match("/^(0[1-9]|1[0-2])\/[0-9]{4}$/",$form_state->getValue('card_expiry_date'))) {
      $form_state->setErrorByName('card_expiry_date','Invalide Card Expiry Date');
    }
    if (!preg_match("/^[A-Z]{1,2}[0-9]{6}[0-9A]$/",$form_state->getValue('identityNumber'))) {
      $form_state->setErrorByName('identityNumber','Invalide HKID');
    }
    if (!empty($form_state->getValue('taxResidencyTin1')) &&
      !preg_match("/^[A-Z]{1,2}[0-9]{6}[0-9A]$/",$form_state->getValue('taxResidencyTin1'))) {
      $form_state->setErrorByName('taxResidencyTin1','Invalide Tax Residency 1');
    }
    // if (!empty($form_state->getValue('taxResidencyTin2')) &&
    //   !preg_match("/^[A-Z]{1,2}[0-9]{6}[0-9A]$/",$form_state->getValue('taxResidencyTin2'))) {
    //   $form_state->setErrorByName('taxResidencyTin2','Invalide Tax Residency 2');
    // }
    // if (!empty($form_state->getValue('taxResidencyTin3')) &&
    //   !preg_match("/^[A-Z]{1,2}[0-9]{6}[0-9A]$/",$form_state->getValue('taxResidencyTin3'))) {
    //   $form_state->setErrorByName('taxResidencyTin3','Invalide Tax Residency 3');
    // }

    if (!empty($form_state->getValue('customer_insured_identityNumber')) &&
      !preg_match("/^[A-Z]{1,2}[0-9]{6}[0-9A]$/",$form_state->getValue('customer_insured_identityNumber'))) {
      $form_state->setErrorByName('customer_insured_identityNumber','Invalide Insured HKID');
    }
    if (!empty($form_state->getValue('customer_insured_taxResidencyTin1')) &&
      !preg_match("/^[A-Z]{1,2}[0-9]{6}[0-9A]$/",$form_state->getValue('customer_insured_taxResidencyTin1'))) {
      $form_state->setErrorByName('customer_insured_taxResidencyTin1','Invalide Insured Tax Residency 1');
    }
    // if (!empty($form_state->getValue('customer_insured_taxResidencyTin2')) &&
    //   !preg_match("/^[A-Z]{1,2}[0-9]{6}[0-9A]$/",$form_state->getValue('customer_insured_taxResidencyTin2'))) {
    //   $form_state->setErrorByName('customer_insured_taxResidencyTin2','Invalide Insured Tax Residency 2');
    // }
    // if (!empty($form_state->getValue('customer_insured_taxResidencyTin3')) &&
    //   !preg_match("/^[A-Z]{1,2}[0-9]{6}[0-9A]$/",$form_state->getValue('customer_insured_taxResidencyTin3'))) {
    //   $form_state->setErrorByName('customer_insured_taxResidencyTin3','Invalide Insured Tax Residency 3');
    // }


    if (!empty($form_state->getValue('customer_payor_identityNumber')) &&
      !preg_match("/^[A-Z]{1,2}[0-9]{6}[0-9A]$/",$form_state->getValue('customer_payor_identityNumber'))) {
      $form_state->setErrorByName('customer_payor_identityNumber','Invalide Payor HKID');
    }

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $order = array();
    // $order['referenceNumber'] = $form_state->getValue('referenceNumber');
    $order['aeonRefNumber'] = $form_state->getValue('aeonRefNumber');
    $order['same_as_owner'] = $form_state->getValue('same_as_owner');
    $order['owner']['surname'] = $form_state->getValue('surname');
    $order['owner']['givenName'] = $form_state->getValue('givenName');
    $order['owner']['chineseName'] = $form_state->getValue('chineseName');
    $order['owner']['relationship'] = $form_state->getValue('relationship');
    $order['owner']['identityType'] = $form_state->getValue('identityType');
    $order['owner']['identityNumber'] = $form_state->getValue('identityNumber');
    $order['owner']['issueCountry'] = $form_state->getValue('issueCountry');
    $order['owner']['gender'] = $form_state->getValue('gender');
    $order['owner']['isPermanentHkid'] = $form_state->getValue('isPermanentHkid');
    $order['owner']['birthDate'] = $form_state->getValue('birthDate');
    $order['owner']['marital'] = $form_state->getValue('marital');
    $order['owner']['nationality'] = $form_state->getValue('nationality');
    $order['owner']['taxResidency1'] = $form_state->getValue('taxResidency1');
    $order['owner']['taxResidencyTin1'] = $form_state->getValue('taxResidencyTin1');
    $order['owner']['taxResidency2'] = $form_state->getValue('taxResidency2');
    $order['owner']['taxResidencyTin2'] = $form_state->getValue('taxResidencyTin2');
    $order['owner']['taxResidency3'] = $form_state->getValue('taxResidency3');
    $order['owner']['taxResidencyTin3'] = $form_state->getValue('taxResidencyTin3');
    $order['owner']['email'] = $form_state->getValue('email');
    $order['owner']['residence_address1'] = $form_state->getValue('residence_address1');
    $order['owner']['residence_address2'] = $form_state->getValue('residence_address2');
    $order['owner']['residence_address3'] = $form_state->getValue('residence_address3');
    $order['owner']['residence_city'] = $form_state->getValue('residence_city');
    $order['owner']['residence_country'] = $form_state->getValue('residence_country');
    $order['owner']['mailing_same_as_residence'] = $form_state->getValue('mailing_same_as_residence');
    $order['owner']['mailing_address1'] = $form_state->getValue('mailing_address1');
    $order['owner']['mailing_address2'] = $form_state->getValue('mailing_address2');
    $order['owner']['mailing_address3'] = $form_state->getValue('mailing_address3');
    $order['owner']['mailing_city'] = $form_state->getValue('mailing_city');
    $order['owner']['mailing_country'] = $form_state->getValue('mailing_country');
    $order['owner']['occupationCode'] = $form_state->getValue('occupationCode');
    $order['owner']['mobile'] = $form_state->getValue('mobile');
    $order['owner']['smoker'] = $form_state->getValue('smoker');
    $order['owner']['monthly_income'] = $form_state->getValue('monthly_income');
    $order['owner']['solicitation'] = $form_state->getValue('solicitation');
    $order['owner']['opt_out_reason'] = $form_state->getValue('opt_out_reason');
    if($order['same_as_owner']=='Y'){
      $order['insured'] = $order['owner'];
    }else{
      $order['insured']['surname'] = $form_state->getValue('customer_insured_surname');
      $order['insured']['givenName'] = $form_state->getValue('customer_insured_givenName');
      $order['insured']['chineseName'] = $form_state->getValue('customer_insured_chineseName');
      $order['insured']['identityType'] = $form_state->getValue('customer_insured_identityType');
      $order['insured']['identityNumber'] = $form_state->getValue('customer_insured_identityNumber');
      $order['insured']['issueCountry'] = $form_state->getValue('customer_insured_issueCountry');
      $order['insured']['gender'] = $form_state->getValue('customer_insured_gender');
      $order['insured']['isPermanentHkid'] = $form_state->getValue('customer_insured_isPermanentHkid');
      $order['insured']['birthDate'] = $form_state->getValue('customer_insured_birthDate');
      $order['insured']['marital'] = $form_state->getValue('customer_insured_marital');
      $order['insured']['nationality'] = $form_state->getValue('customer_insured_nationality');
      $order['insured']['taxResidency1'] = $form_state->getValue('customer_insured_taxResidency1');
      $order['insured']['taxResidencyTin1'] = $form_state->getValue('customer_insured_taxResidencyTin1');
      $order['insured']['taxResidency2'] = $form_state->getValue('customer_insured_taxResidency2');
      $order['insured']['taxResidencyTin2'] = $form_state->getValue('customer_insured_taxResidencyTin2');
      $order['insured']['taxResidency3'] = $form_state->getValue('customer_insured_taxResidency3');
      $order['insured']['taxResidencyTin3'] = $form_state->getValue('customer_insured_taxResidencyTin3');
      $order['insured']['email'] = $form_state->getValue('customer_insured_email');
      $order['insured']['residence_address1'] = $form_state->getValue('customer_insured_residence_address1');
      $order['insured']['residence_address2'] = $form_state->getValue('customer_insured_residence_address2');
      $order['insured']['residence_address3'] = $form_state->getValue('customer_insured_residence_address3');
      $order['insured']['residence_city'] = $form_state->getValue('customer_insured_residence_city');
      $order['insured']['residence_country'] = $form_state->getValue('customer_insured_residence_country');
      $order['insured']['mailing_same_as_residence'] = $form_state->getValue('customer_insured_mailing_same_as_residence');
      $order['insured']['mailing_address1'] = $form_state->getValue('customer_insured_mailing_address1');
      $order['insured']['mailing_address2'] = $form_state->getValue('customer_insured_mailing_address2');
      $order['insured']['mailing_address3'] = $form_state->getValue('customer_insured_mailing_address3');
      $order['insured']['mailing_city'] = $form_state->getValue('customer_insured_mailing_city');
      $order['insured']['mailing_country'] = $form_state->getValue('customer_insured_mailing_country');
      $order['insured']['occupationCode'] = $form_state->getValue('customer_insured_occupationCode');
      $order['insured']['mobile'] = $form_state->getValue('customer_insured_mobile');
      $order['insured']['smoker'] = $form_state->getValue('customer_insured_smoker');
      $order['insured']['monthly_income'] = $form_state->getValue('customer_insured_monthly_income');
      $order['insured']['solicitation'] = $form_state->getValue('customer_insured_solicitation');
      $order['insured']['opt_out_reason'] = $form_state->getValue('customer_insured_opt_out_reason');
    }
    $order['payor']['surname'] = $form_state->getValue('customer_payor_surname');
    $order['payor']['givenName'] = $form_state->getValue('customer_payor_givenName');
    $order['payor']['chineseName'] = $form_state->getValue('customer_payor_chineseName');
    $order['payor']['identityType'] = $form_state->getValue('customer_payor_identityType');
    $order['payor']['identityNumber'] = $form_state->getValue('customer_payor_identityNumber');
    $order['payor']['gender'] = $form_state->getValue('customer_payor_gender');
    $order['payor']['birthDate'] = $form_state->getValue('customer_payor_birthDate');
    $order['beneficiary_relationship'] = $form_state->getValue('beneficiary_relationship');
    $order['currency'] = $form_state->getValue('currency');
    $order['paymentMode'] = $form_state->getValue('paymentMode');
    $order['pep'] = $form_state->getValue('pep');
    $order['another_person'] = $form_state->getValue('another_person');
    $order['ecopy'] = $form_state->getValue('ecopy');
    $order['plan_code'] = $form_state->getValue('plan_code');
    $order['product_name_english'] = $form_state->getValue('product_name_english');
    $order['product_name_chinese'] = $form_state->getValue('product_name_chinese');
    $order['face_amount'] = $form_state->getValue('face_amount');
    $order['plan_level'] = $form_state->getValue('plan_level');
    $order['promotion_code'] = $form_state->getValue('promotion_code');
    $order['replacement_declaration'] = $form_state->getValue('replacement_declaration');
    $order['fna'] = $form_state->getValue('fna');
    $order['health_details_q_1'] = $form_state->getValue('health_details_q_1');
    $order['health_details_q_2'] = $form_state->getValue('health_details_q_2');
    $order['health_details_q_3'] = $form_state->getValue('health_details_q_3');
    $order['health_details_q_4'] = $form_state->getValue('health_details_q_4');
    $order['health_details_q_5'] = $form_state->getValue('health_details_q_5');
    $order['agentCode'] = $form_state->getValue('agentCode');
    $order['agentName'] = $form_state->getValue('agentName');
    $order['billingType'] = $form_state->getValue('billingType');
    $order['authorizationCode'] = $form_state->getValue('authorizationCode');
    $order['cardHolderName'] = $form_state->getValue('cardHolderName');
    $order['cardholder_id_number'] = $form_state->getValue('cardholder_id_number');
    $order['card_expiry_date'] = $form_state->getValue('card_expiry_date');
    $order['initial_premium'] = $form_state->getValue('initial_premium');
    $order['modal_premium_payment'] = $form_state->getValue('modal_premium_payment');
    $order['levy'] = $form_state->getValue('levy');
    $order['remarks'] = $form_state->getValue('remarks');
    $order['dda_setup'] = $form_state->getValue('dda_setup');
    $order['customer_id'] = $this->import_customer_id;
    $order['status'] = 1;
    if (is_numeric($this->order_id)) {
      $order['id'] = $this->order_id;
    }
    OrderController::update_order($order);
    \Drupal::messenger()->addMessage('Order has been updated');
    $form_state->setRedirectUrl(Url::fromRoute('chubb_life.list_order_form'));
  }

}
