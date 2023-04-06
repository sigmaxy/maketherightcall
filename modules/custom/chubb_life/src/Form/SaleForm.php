<?php

namespace Drupal\chubb_life\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\chubb_life\Controller\AttributeController;
use Drupal\chubb_life\Controller\OrderController;
use Drupal\Core\Url;

/**
 * Class SaleForm.
 */
class SaleForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'sale_form';
  }
  public $customer_id;
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $customer_id = NULL) {
    if (isset($customer_id)) {
      $this->customer_id = $customer_id;
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
    $currency_opt = AttributeController::get_currency_options();
    $form['owner'] = [
      '#type'  => 'details',
      '#title' => $this->t('Customer Details (Owner)'),
      '#open'  => true,
      '#weight' => '1',
    ];
    
    $form['owner']['same_as_owner'] = [
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
    $form['owner']['last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last Name (English)'),
      '#maxlength' => 255,
      '#size' => 64,
      '#weight' => '2',
      // '#default_value' => $default_po_number,
      '#required'=> true,
    ];
    $form['owner']['first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First Name (English)'),
      '#maxlength' => 255,
      '#size' => 64,
      '#weight' => '2',
      // '#default_value' => $default_po_number,
      '#required'=> true,
    ];
    $form['owner']['chinese_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name (Chinese)'),
      '#maxlength' => 255,
      '#size' => 64,
      '#weight' => '3',
      // '#default_value' => $default_po_number,
      // '#required'=> true,
    ];
    $form['owner']['relation'] = [
      '#type' => 'select',
      '#title' => $this->t('Relationship to Proposed Insured'),
      '#options' => $relation_opt,
      // '#default_value' => 'MOT',
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '4',
    ];
    $form['owner']['id_type'] = [
      '#type' => 'select',
      '#title' => $this->t('ID Type'),
      '#options' => $id_type_opt,
      // '#default_value' => 1,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '5',
    ];
    $form['owner']['hkid'] = [
      '#type' => 'textfield',
      '#title' => $this->t('HKID Card Number'),
      '#maxlength' => 255,
      '#size' => 64,
      '#weight' => '6',
    ];
    $form['owner']['country_issue'] = [
      '#type' => 'select',
      '#title' => $this->t('Country of Issue'),
      '#options' => $country_opt,
      '#default_value' => 'HK',
      '#attributes' => [
        'class' => ['country_select'],
      ],
      '#weight' => '7',
    ];
    $form['owner']['gender'] = [
      '#type' => 'select',
      '#title' => $this->t('Gender'),
      '#options' => $gender_opt,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '8',
    ];
    $form['owner']['hk_permanet'] = [
      '#type' => 'select',
      '#title' => $this->t('HK Permanent ID Card holder'),
      '#options' => $yn_opt,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '9',
    ];
    $form['owner']['dob'] = [
      '#type' => 'date',
      '#title' => $this->t('Date of Birth'),
      '#weight' => '10',
    ];
    $form['owner']['marital_status'] = [
      '#type' => 'select',
      '#title' => $this->t('Marital Status'),
      '#options' => $marital_status_opt,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '11',
    ];
    $form['owner']['nationality'] = [
      '#type' => 'select',
      '#title' => $this->t('Nationality'),
      '#options' => $country_opt,
      '#default_value' => 'HK',
      '#attributes' => [
        'class' => ['country_select'],
      ],
      '#weight' => '12',
    ];
    $form['owner']['tax_residency'] = [
      '#type' => 'select',
      '#title' => $this->t('Tax Residency'),
      '#options' => $country_opt,
      '#default_value' => 'HK',
      '#attributes' => [
        'class' => ['country_select'],
      ],
      '#weight' => '13',
    ];
    $form['owner']['email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email Address'),
      '#weight' => '14',
    ];
    $form['address'] = [
      '#type'  => 'details',
      '#title' => $this->t('Residential Address (in English)'),
      '#open'  => true,
      '#weight' => '2',
    ];
    $form['address']['flat'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Flat/room'),
      '#weight' => '1',
    ];
    $form['address']['floor'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Floor'),
      '#weight' => '2',
    ];
    $form['address']['block'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Block'),
      '#weight' => '3',
    ];
    $form['address']['building'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Building/estate name'),
      '#weight' => '4',
    ];
    $form['address']['street'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Street name and number'),
      '#weight' => '5',
    ];
    $form['address']['district'] = [
      '#type' => 'textfield',
      '#title' => $this->t('District'),
      '#weight' => '6',
    ];
    $form['address']['address_country'] = [
      '#type' => 'select',
      '#title' => $this->t('Country/Region'),
      '#options' => $country_opt,
      '#default_value' => 'HK',
      '#attributes' => [
        'class' => ['country_select'],
      ],
      '#weight' => '7',
    ];
    $form['address']['occupations_code'] = [
      '#type' => 'select',
      '#title' => $this->t('Occupations Code'),
      '#options' => $occupations_opt,
      '#attributes' => [
        'class' => ['occupation_select'],
      ],
      '#weight' => '8',
    ];
    $form['address']['mobile'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Mobile Phone Number'),
      '#weight' => '9',
    ];
    $form['address']['monthly_income'] = [
      '#type' => 'select',
      '#title' => $this->t('Monthly income'),
      '#options' => $monthly_income_opt,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '10',
    ];
    $form['address']['solicitation'] = [
      '#type' => 'select',
      '#title' => $this->t('Solicitation (Opt out Indicator)'),
      '#options' => $solicitation_opt,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '11',
    ];
    $form['address']['opt_out_reason'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Opt out reason'),
      '#weight' => '12',
    ];
    $form['payor'] = [
      '#type'  => 'details',
      '#title' => $this->t('Customer Details (Payor)'),
      '#open'  => true,
      '#weight' => '3',
    ];
    $form['payor']['payor_last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last Name (English)'),
      '#maxlength' => 255,
      '#size' => 64,
      '#weight' => '1',
      // '#default_value' => $default_po_number,
      '#required'=> true,
    ];
    $form['payor']['payor_first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First Name (English)'),
      '#maxlength' => 255,
      '#size' => 64,
      '#weight' => '2',
      // '#default_value' => $default_po_number,
      '#required'=> true,
    ];
    $form['payor']['payor_id_type'] = [
      '#type' => 'select',
      '#title' => $this->t('ID Type'),
      '#options' => $id_type_opt,
      // '#default_value' => 1,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '3',
    ];
    $form['payor']['payor_hkid'] = [
      '#type' => 'textfield',
      '#title' => $this->t('HKID Card Number'),
      '#maxlength' => 255,
      '#size' => 64,
      '#weight' => '4',
    ];
    $form['payor']['payor_gender'] = [
      '#type' => 'select',
      '#title' => $this->t('Gender'),
      '#options' => $gender_opt,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '5',
    ];
    $form['payor']['payor_dob'] = [
      '#type' => 'date',
      '#title' => $this->t('Date of Birth'),
      '#weight' => '6',
    ];
    $form['beneficiary'] = [
      '#type'  => 'details',
      '#title' => $this->t('Beneficiary'),
      '#open'  => true,
      '#weight' => '4',
    ];
    $form['beneficiary']['beneficiary_relation'] = [
      '#type' => 'select',
      '#title' => $this->t('Relationship'),
      '#options' => $relation_opt,
      // '#default_value' => 'MOT',
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '1',
    ];
    $form['policy'] = [
      '#type'  => 'details',
      '#title' => $this->t('Policy Details'),
      '#open'  => true,
      '#weight' => '5',
    ];
    $form['policy']['currency'] = [
      '#type' => 'select',
      '#title' => $this->t('Currency'),
      '#options' => $currency_opt,
      // '#default_value' => 'MOT',
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '1',
    ];
    $form['policy']['payment_mode'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Payment Mode'),
      '#maxlength' => 255,
      '#size' => 64,
      '#weight' => '2',
    ];
    $form['policy']['pep'] = [
      '#type' => 'textfield',
      '#title' => $this->t('PEP'),
      '#maxlength' => 255,
      '#size' => 64,
      '#weight' => '3',
    ];
    $form['policy']['another_person'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Acting on behalf of another person'),
      '#maxlength' => 255,
      '#size' => 64,
      '#weight' => '4',
    ];
    $form['policy']['ecopy'] = [
      '#type' => 'select',
      '#title' => $this->t('eCopy'),
      '#options' => $yn_opt,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '5',
    ];
    $form['information'] = [
      '#type'  => 'details',
      '#title' => $this->t('Coverage Information'),
      '#open'  => true,
      '#weight' => '6',
    ];
    $form['information']['plan_code'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Plan Code'),
      '#maxlength' => 255,
      '#size' => 64,
      '#weight' => '1',
    ];
    $form['information']['face_amount'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Face amount (TBC)'),
      '#maxlength' => 255,
      '#size' => 64,
      '#weight' => '2',
    ];
    $form['information']['plan_level'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Plan level (RS)'),
      '#maxlength' => 255,
      '#size' => 64,
      '#weight' => '3',
    ];
    $form['information']['family_package'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Family Package (TBC)'),
      '#maxlength' => 255,
      '#size' => 64,
      '#weight' => '4',
    ];
    $form['micellaneous'] = [
      '#type'  => 'details',
      '#title' => $this->t('Micellaneous'),
      '#open'  => true,
      '#weight' => '7',
    ];
    $form['micellaneous']['replacement_declaration'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Replacement Declaration'),
      '#maxlength' => 255,
      '#size' => 64,
      '#weight' => '1',
    ];
    $form['micellaneous']['fna'] = [
      '#type' => 'select',
      '#title' => $this->t('FNA'),
      '#options' => $yn_opt,
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#weight' => '5',
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#weight' => '8',
    ];
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
    $sale_data = array();
    $sale_data['same_as_owner'] = $form_state->getValue('same_as_owner');
    $sale_data['last_name'] = $form_state->getValue('last_name');
    $sale_data['first_name'] = $form_state->getValue('first_name');
    $sale_data['chinese_name'] = $form_state->getValue('chinese_name');
    $sale_data['relation'] = $form_state->getValue('relation');
    $sale_data['id_type'] = $form_state->getValue('id_type');
    $sale_data['hkid'] = $form_state->getValue('hkid');
    $sale_data['country_issue'] = $form_state->getValue('country_issue');
    $sale_data['gender'] = $form_state->getValue('gender');
    $sale_data['hk_permanet'] = $form_state->getValue('hk_permanet');
    $sale_data['dob'] = $form_state->getValue('dob');
    $sale_data['marital_status'] = $form_state->getValue('marital_status');
    $sale_data['nationality'] = $form_state->getValue('nationality');
    $sale_data['tax_residency'] = $form_state->getValue('tax_residency');
    $sale_data['email'] = $form_state->getValue('email');
    $sale_data['flat'] = $form_state->getValue('flat');
    $sale_data['floor'] = $form_state->getValue('floor');
    $sale_data['block'] = $form_state->getValue('block');
    $sale_data['building'] = $form_state->getValue('building');
    $sale_data['street'] = $form_state->getValue('street');
    $sale_data['district'] = $form_state->getValue('district');
    $sale_data['address_country'] = $form_state->getValue('address_country');
    $sale_data['occupations_code'] = $form_state->getValue('occupations_code');
    $sale_data['mobile'] = $form_state->getValue('mobile');
    $sale_data['monthly_income'] = $form_state->getValue('monthly_income');
    $sale_data['solicitation'] = $form_state->getValue('solicitation');
    $sale_data['opt_out_reason'] = $form_state->getValue('opt_out_reason');
    $sale_data['payor_last_name'] = $form_state->getValue('payor_last_name');
    $sale_data['payor_first_name'] = $form_state->getValue('payor_first_name');
    $sale_data['payor_id_type'] = $form_state->getValue('payor_id_type');
    $sale_data['payor_hkid'] = $form_state->getValue('payor_hkid');
    $sale_data['payor_gender'] = $form_state->getValue('payor_gender');
    $sale_data['payor_dob'] = $form_state->getValue('payor_dob');
    $sale_data['beneficiary_relation'] = $form_state->getValue('beneficiary_relation');
    $sale_data['currency'] = $form_state->getValue('currency');
    $sale_data['payment_mode'] = $form_state->getValue('payment_mode');
    $sale_data['pep'] = $form_state->getValue('pep');
    $sale_data['another_person'] = $form_state->getValue('another_person');
    $sale_data['ecopy'] = $form_state->getValue('ecopy');
    $sale_data['plan_code'] = $form_state->getValue('plan_code');
    $sale_data['face_amount'] = $form_state->getValue('face_amount');
    $sale_data['plan_level'] = $form_state->getValue('plan_level');
    $sale_data['family_package'] = $form_state->getValue('family_package');
    $sale_data['replacement_declaration'] = $form_state->getValue('replacement_declaration');
    $sale_data['fna'] = $form_state->getValue('fna');

    $sale_data['customer_id'] = $this->customer_id;
    $sale_data['status'] = 1;
    $sale_data['created_at'] = time();
    $sale_data['created_by'] = \Drupal::currentUser()->id();
    $sale_data['updated_at'] = time();
    $sale_data['updated_by'] = \Drupal::currentUser()->id();
    OrderController::update_order($sale_data);
    \Drupal::messenger()->addMessage('Order has been updated');
    $form_state->setRedirectUrl(Url::fromRoute('chubb_life.close_window_form'));
  }

}
