<?php

namespace Drupal\chubb_life\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\chubb_life\Controller\AttributeController;

/**
 * Class OrderController.
 */
class OrderController extends ControllerBase {

  /**
   * Werew.
   *
   * @return string
   *   Return Hello string.
   */
  public static function check_order_existed($order_id){
    $connection = Database::getConnection();
    $query = $connection->select('mtrc_order', 'mo');
    $query->fields('mo');
    $query->condition('id', $order_id);
    $record = $query->execute()->fetchAssoc();
    if (isset($record['id'])) {
      return $record['id'];
    }
    return false;
  }
  public static function get_order_by_id($order_id){
    $connection = Database::getConnection();
    $query = $connection->select('mtrc_order', 'mo');
    $query->fields('mo');
    $query->condition('id', $order_id);
    $record = $query->execute()->fetchAssoc();
    $record['owner'] = self::get_order_client_by_type($record['id'],1);
    $record['insured'] = self::get_order_client_by_type($record['id'],2);
    $record['payor'] = self::get_order_client_by_type($record['id'],3);
    return $record;
  }
  public static function get_order_client_by_type($order_id,$client_type){
    $connection = Database::getConnection();
    $query = $connection->select('mtrc_order_client', 'moc');
    $query->fields('moc');
    $query->condition('order_id', $order_id);
    $query->condition('client_type', $client_type);
    $record = $query->execute()->fetchAssoc();
    return $record;
  }
  public static function list_order($uid){
    $connection = Database::getConnection();
    $query = $connection->select('mtrc_order', 'mo');
    $query->fields('mo');
    if(isset($uid)){
      $query->condition('created_by', $uid);
    }
    $record = $query->execute()->fetchAll();
    return $record;
  }

  public static function update_order($order){
    $connection = Database::getConnection();
    $db_order_id = self::check_order_existed($order['id']);
    $order['updated_at'] = time();
    $order['updated_by'] = \Drupal::currentUser()->id();
    $client_owner = $order['owner'];
    unset($order['owner']);
    $client_insured = $order['insured'];
    unset($order['insured']);
    $client_payor = $order['payor'];
    unset($order['payor']);
    if($db_order_id){
      $connection->update('mtrc_order')
        ->fields($order)
        ->condition('id', $db_order_id)
        ->execute();
    }else{
      $order['created_at'] = time();
      $order['created_by'] = \Drupal::currentUser()->id();
      $db_order_id = $connection->insert('mtrc_order')
        ->fields($order)
        ->execute();
    }
    $client_owner['order_id'] = $db_order_id;
    $client_owner['client_type'] = 1;
    self::update_order_client($client_owner);
    $client_insured['order_id'] = $db_order_id;
    $client_insured['client_type'] = 2;
    self::update_order_client($client_insured);
    $client_payor['order_id'] = $db_order_id;
    $client_payor['client_type'] = 3;
    self::update_order_client($client_payor);
  }
  public static function check_order_client_existed($client){
    $connection = Database::getConnection();
    $query = $connection->select('mtrc_order_client', 'moc');
    $query->fields('moc');
    $query->condition('order_id', $client['order_id']);
    $query->condition('client_type', $client['client_type']);
    $record = $query->execute()->fetchAssoc();
    if (isset($record['id'])) {
      return $record['id'];
    }
    return false;
  }
  public static function update_order_client($client){
    $connection = Database::getConnection();
    $db_order_client_id = self::check_order_client_existed($client);
    if($db_order_client_id){
      $connection->update('mtrc_order_client')
        ->fields($client)
        ->condition('id', $db_order_client_id)
        ->execute();
    }else{
      $db_order_client_id = $connection->insert('mtrc_order_client')
        ->fields($client)
        ->execute();
    }
  }
  public static function order_format_json($order){
    $today = date("Y-m-d");
    $owner_age = date_diff(date_create($order['owner']['birthDate']), date_create($today))->y;
    $insured_age = date_diff(date_create($order['insured']['birthDate']), date_create($today))->y;
    $health_detail_question = false;
    if($order['health_details_q_1']=='Y'
      && $order['health_details_q_2']=='Y'
      && $order['health_details_q_3']=='Y'
      && $order['health_details_q_4']=='Y'
    ){
      $health_detail_question = true;
    }
    $results = array(
      'applicationDto'=>[
        'referenceNumber'=>sprintf('TM%06d',$order['id']),
        'aeonRefNumber'=>$order['aeonRefNumber'],
        'agentCode'=>substr($order['agentCode'], 0, 6),
        'accountHolder1'=>[
          'holderName'=>$order['cardHolderName'],
          'identityNumber'=>$order['cardholder_id_number'],
          'identityType'=>'',
        ],
        'accountNumber'=>$order['authorizationCode'],
        'agentName'=>$order['agentName'],
        'agentCode2'=>'',
        'annuityStartAge'=>'',
        'applicationSignDate'=>'',
        'autoPolicyDate'=>'',
        'billingType'=>$order['billingType'],
        'contribution'=>'',
        'currency'=>$order['currency'],
        'deathBenefitOption'=>'',
        'dividendOption'=>'',
        'ecopy'=>$order['ecopy']=='Y'?true:false,
        'effectiveDate'=>'',
        'extraContribution'=>'',
        'isOcrDataChanged'=>'',
        'isSignedByVos'=>'',
        'mailingAddressIndicator'=>'',
        'mgoDecision'=>'',
        'mgoOfferAcpt'=>'',
        'mgoStatus'=>'',
        'noSignatureIndicator'=>'',
        'nonForfeitureOption'=>'',
        'paidPolicy'=>'',
        'paidPolicyDuration'=>'',
        'parentPolicyNumber'=>'',
        'paymentMode'=>$order['paymentMode'],
        'pep'=>$order['pep']=='Y'?true:false,
        'policyEffectiveDate'=>'',
        'prePayment'=>'',
        'promoCode'=>'',
        'taxResidency1'=>array(
          'taxResidency'=>$order['owner']['taxResidency1'],
          'taxResidencyReason'=>'',
          'taxResidencyReasonCode'=>'',
          'taxResidencyTin'=>$order['owner']['taxResidencyTin1'],
        ),
        'taxResidency2'=>array(
          'taxResidency'=>$order['owner']['taxResidency2'],
          'taxResidencyReason'=>'',
          'taxResidencyReasonCode'=>'',
          'taxResidencyTin'=>$order['owner']['taxResidencyTin2'],
        ),
        'taxResidency3'=>array(
          'taxResidency'=>$order['owner']['taxResidency3'],
          'taxResidencyReason'=>'',
          'taxResidencyReasonCode'=>'',
          'taxResidencyTin'=>$order['owner']['taxResidencyTin3'],
        ),
        'totalLimit'=>'',
        'worldwideEmergencyAssistanceServices'=>'',
      ],
      'awdFileDto'=>[
        [
          'fileContent'=>'',
          'fileName'=>'',
        ],
      ],
      'beneficiaryDtos'=>[
        [
          'beneficiaryClass'=>1,
          'beneficiarySequence'=>1,
          'chineseName'=>$order['insured']['chineseName'],
          'customerType'=>'I',
          'givenName'=>$order['insured']['givenName'],
          'identityNumber'=>$order['insured']['identityNumber'],
          'relationship'=>$order['beneficiary_relationship'],
          'shared'=>100,
          'surname'=>$order['insured']['surname'],
        ],
      ],
      'benefitDtos'=>[
        [
          'annualDeductibleOption'=>'',
          'attachTo'=>'',
          'coverageClass'=>$order['plan_level'],
          'coverageCode'=>$order['plan_code'],
          'coverageNumber'=>1,
          'criticalIllnessCode1'=>'',
          'criticalIllnessCode1'=>'',
          'criticalIllnessCode1'=>'',
          'currency'=>$order['currency'],
          'customerSequence'=>1,
          'customerType'=>'I',
          'faceAmount'=>$order['face_amount'],
          'plannedPremium'=>0,
          'protectionFaceAmount'=>0,
          'savingFaceAmount'=>0,
          "plannedPremiumTM"=>$order['modal_premium_payment'],
		      "levyTM"=>$order['levy'],
          "productNameEn"=>$order['product_name_english'],
          "productNameTc"=>$order['product_name_chinese'],
          
        ],
      ],
      'customerDtos'=>[
        [
          'age'=>$owner_age,
          'birthDate'=>$order['owner']['birthDate'],
          'birthPlace'=>'',
          'chineseName'=>$order['owner']['chineseName'],
          'citizenship'=>$order['owner']['nationality'],
          'countryRegionCode'=>null,
          'customerSequence'=>1,
          'customerType'=>'O',
          'email'=>$order['owner']['email'],
          'employer'=>'',
          'gender'=>$order['owner']['gender'],
          'givenName'=>$order['owner']['givenName'],
          'identityNumber'=>$order['owner']['identityNumber'],
          'identityType'=>$order['owner']['identityType'],
          'isPermanentHkid'=>$order['owner']['isPermanentHkid']=='Y'?true:false,
          'isValidIdType'=>true,
          'issueCountry'=>$order['owner']['issueCountry'],
          'mailing'=>array(
            'address1'=>$order['owner']['mailing_address1'],
            'address2'=>$order['owner']['mailing_address2'],
            'address3'=>$order['owner']['mailing_address3'],
            'chineseAddress1'=>'',
            'chineseAddress2'=>'',
            'chineseAddress3'=>'',
            'chineseAddress4'=>'',
            'chineseAddressIndicator'=>'',
            'chineseCountry'=>'',
            'chinesePostalCode'=>'',
            'city'=>$order['owner']['mailing_city'],
            'country'=>$order['owner']['mailing_country'],
            'postalCode'=>'',
          ),
          'marital'=>$order['owner']['marital'],
          'mobileNumber'=>$order['owner']['mobile'],
          'mobileNumberCountryCode'=>'',
          'nationality'=>$order['owner']['nationality'],
          'occupation'=>'',
          'occupationCode'=>$order['owner']['occupationCode'],
          'relationship'=>$order['owner']['relationship'],
          'residence'=>array(
            'address1'=>$order['owner']['residence_address1'],
            'address2'=>$order['owner']['residence_address2'],
            'address3'=>$order['owner']['residence_address3'],
            'chineseAddress1'=>'',
            'chineseAddress2'=>'',
            'chineseAddress3'=>'',
            'chineseAddress4'=>'',
            'chineseAddressIndicator'=>'',
            'chineseCountry'=>'',
            'chinesePostalCode'=>'',
            'city'=>$order['owner']['residence_city'],
            'country'=>$order['owner']['residence_country'],
            'postalCode'=>'',
            'telephone'=>'',
            'telephoneCountryCode'=>'',
          ),
          'solicitation'=>$order['owner']['solicitation']=='Y'?true:false,
          'surname'=>$order['owner']['surname'],
          'uwClass'=>'',
          'workplace'=>array(
            'address1'=>'',
            'address2'=>'',
            'address3'=>'',
            'chineseAddress1'=>'',
            'chineseAddress2'=>'',
            'chineseAddress3'=>'',
            'chineseAddress4'=>'',
            'chineseAddressIndicator'=>'',
            'chineseCountry'=>'',
            'chinesePostalCode'=>'',
            'city'=>'',
            'country'=>'',
            'postalCode'=>'',
            'telephone'=>'',
            'telephoneCountryCode'=>'',
          ),
        ],
        [
          'age'=>$insured_age,
          'birthDate'=>$order['insured']['birthDate'],
          'birthPlace'=>'',
          'chineseName'=>$order['insured']['chineseName'],
          'citizenship'=>$order['insured']['nationality'],
          'countryRegionCode'=>null,
          'customerSequence'=>2,
          'customerType'=>'I',
          'email'=>$order['insured']['email'],
          'gender'=>$order['insured']['gender'],
          'givenName'=>$order['insured']['givenName'],
          'identityNumber'=>$order['insured']['identityNumber'],
          'identityType'=>$order['insured']['identityType'],
          'isPermanentHkid'=>$order['insured']['isPermanentHkid']=='Y'?true:false,
          'isValidIdType'=>true,
          'issueCountry'=>$order['insured']['issueCountry'],
          'mailing'=>array(
            'address1'=>$order['insured']['mailing_address1'],
            'address2'=>$order['insured']['mailing_address2'],
            'address3'=>$order['insured']['mailing_address3'],
            'city'=>$order['insured']['mailing_city'],
            'country'=>$order['insured']['mailing_country'],
            'postalCode'=>'',
            'telephone'=>'',
            'telephoneCountryCode'=>'',
          ),
          'marital'=>$order['insured']['marital'],
          'mobileNumber'=>$order['insured']['mobile'],
          'mobileNumberCountryCode'=>'',
          'nationality'=>$order['insured']['nationality'],
          'occupationCode'=>$order['insured']['occupationCode'],
          'relationship'=>$order['insured']['relationship'],
          'residence'=>array(
            'address1'=>$order['insured']['residence_address1'],
            'address2'=>$order['insured']['residence_address2'],
            'address3'=>$order['insured']['residence_address3'],
            'city'=>$order['insured']['residence_city'],
            'country'=>$order['insured']['residence_country'],
            'postalCode'=>'',
            'telephone'=>'',
            'telephoneCountryCode'=>'',
          ),
          'solicitation'=>$order['insured']['solicitation']=='Y'?true:false,
          'optOutReason'=>$order['insured']['opt_out_reason'],
          'surname'=>$order['insured']['surname'],
        ],
        
      ],
      'imageCaptureFileDto'=>[
        [
          'fileContent'=>'',
          'fileName'=>'',
        ],
      ],
      'paymentTransactionDto'=>array(
        'amount'=>$order['initial_premium'],
        'amountInPolicyCurrency'=>$order['currency'],
        'authorizationCode'=>$order['authorizationCode'],
        'bankName'=>'',
        'basicPlanCode'=>$order['plan_code'],
        'cardHolderName'=>$order['cardHolderName'],
        'cardType'=>'',
        'currency'=>$order['currency'],
        'insuredFirstName'=>$order['insured']['givenName'],
        'insuredLastName'=>$order['insured']['surname'],
        'ownerFirstName'=>$order['owner']['givenName'],
        'ownerLastName'=>$order['owner']['surname'],
        'paymentReceivedDate'=>'',
        'payorFirstName'=>$order['payor']['givenName'],
        'payorLastName'=>$order['payor']['surname'],
        'payorRole'=>'O',
        'policyCurrency'=>$order['currency'],
        'referenceNumber'=>sprintf('TM%06d',$order['id']),
        'transactionStatus'=>'',
        'transactionUpdatedDate'=>'',
      ),
      'policyNumber'=>'',
      'uploadId'=>'',
      'questionnaireDtos'=>[
        [
          'customerType'=>'I',
          'questionnaireNumber'=>13,
          'questionnaireSectionId'=>14,
          'questionnaireText'=>null,
          'questionnaireYesOrNo'=>$health_detail_question,
        ],
        [
          'customerType'=>'I',
          'questionnaireNumber'=>1,
          'questionnaireSectionId'=>11,
          'questionnaireText'=>$order['replacement_declaration']=='Y'?'E':'N',
          'questionnaireYesOrNo'=>null,
        ],
        [
          'customerType'=>'I',
          'questionnaireNumber'=>30,
          'questionnaireSectionId'=>11,
          'questionnaireText'=>null,
          'questionnaireYesOrNo'=>$order['fna']=='Y'?true:false,
        ],
        [
          'customerType'=>'I',
          'questionnaireNumber'=>2,
          'questionnaireSectionId'=>20,
          'questionnaireText'=>$order['insured']['monthly_income'],
          'questionnaireYesOrNo'=>null,
        ],
        [
          'customerType'=>'I',
          'questionnaireNumber'=>11,
          'questionnaireSectionId'=>14,
          'questionnaireText'=>null,
          'questionnaireYesOrNo'=>$order['insured']['smoker']=='Y'?true:false,
        ],
        [
          'customerType'=>'I',
          'questionnaireNumber'=>34,
          'questionnaireSectionId'=>11,
          'questionnaireText'=>$order['remarks'],
          'questionnaireYesOrNo'=>null,
        ],
      ],
    );
    return $results;
  }

}
