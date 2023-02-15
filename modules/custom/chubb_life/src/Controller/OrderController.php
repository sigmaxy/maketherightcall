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
  public static function calculate_age($birthday){
    $today = date("Y-m-d");
    $date_diff = date_diff(date_create($birthday), date_create($today));
    $yyyyDiff = $date_diff->y;
    $mmDiff = $date_diff->m;
    $ddDiff = $date_diff->d;
    if ($ddDiff < 0) {
      $ddDiff = $ddDiff + 30;
      $mmDiff = $mmDiff - 1;
    }
    if ($mmDiff < 0) {
      $mmDiff = $mmDiff + 12;
      $yyyyDiff = $yyyyDiff - 1;
    }
    if ($mmDiff > 6 || ($mmDiff === 6 && $ddDiff > 0)) {
      $yyyyDiff += 1;
    }
    if ($yyyyDiff < 0) {
      $yyyyDiff = 0;
    }
    return $yyyyDiff;
  }	
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
    return $db_order_id;
  }
  public static function update_order_json_generated($order_id){
    $connection = Database::getConnection();
    $order['json_generated'] = time();
    $connection->update('mtrc_order')
        ->fields($order)
        ->condition('id', $order_id)
        ->execute();
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
    $owner_age = self::calculate_age($order['owner']['birthDate']);
    $insured_age = self::calculate_age($order['insured']['birthDate']);
    $payor_age = self::calculate_age($order['payor']['birthDate']);
    $health_detail_question = false;
    // echo $order['dda_setup'].date('/m/Y', strtotime('+2 months'));exit;
    // $effectiveDate = $order['dda_setup'].date('/m/Y', strtotime('+2 months'));
    if($order['health_details_q_1']=='Y'
      && $order['health_details_q_2']=='Y'
      && $order['health_details_q_3']=='Y'
      && $order['health_details_q_4']=='Y'
    ){
      $health_detail_question = true;
    }
    if (mb_substr($order['plan_code'], 0, 3)=='PAB') {
      $coverageClass = null;
    }else{
      $coverageClass = $order['plan_level'];
    }
    if ($order['plan_code']=='MCE') {
      $q1 = $order['health_details_q_1']=='Y'?true:false;
      $q2 = $order['health_details_q_2']=='Y'?true:false;
      $q3 = $order['health_details_q_3']=='Y'?true:false;
      $q4 = $order['health_details_q_4']=='Y'?true:false;
      $q5 = $order['health_details_q_5']=='Y'?true:false;
    }else{
      $q1 = null;
      $q2 = null;
      $q3 = null;
      $q4 = null;
      $q5 = null;
    }
    if($order['currency'] == 'USD'){
      $transaction = $order['initial_premium']*7.8;
    }else{
      $transaction = $order['initial_premium'];
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
        'applicationSignDate'=>date('d/m/Y', time()),
        'autoPolicyDate'=>'',
        'billingType'=>$order['billingType'],
        'contribution'=>'',
        'currency'=>$order['currency'],
        'deathBenefitOption'=>'',
        'dividendOption'=>'',
        'ecopy'=>$order['ecopy']=='Y'?true:false,
        'epolicyIndicator'=>$order['epolicy']=='Y'?true:false,
        'effectiveDate'=>$order['dda_setup'].date('/m/Y', strtotime('+2 months')),
        'extraContribution'=>'',
        'isOcrDataChanged'=>'',
        'isSignedByVos'=>'',
        'mailingAddressIndicator'=>'R',
        'mgoDecision'=>'',
        'mgoOfferAcpt'=>'',
        'mgoStatus'=>'',
        'noSignatureIndicator'=>'',
        'nonForfeitureOption'=>'',
        'paidPolicy'=>'',
        'paidPolicyDuration'=>'',
        'parentPolicyNumber'=>'',
        'paymentMode'=>$order['paymentMode'],
        'pep'=>false,
        'policyEffectiveDate'=>'',
        'prePayment'=>'',
        'promoCode'=>$order['promotion_code'],
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
          'coverageClass'=>$coverageClass,
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
          'age'=>$insured_age,
          'birthDate'=>$order['insured']['birthDate'],
          'birthPlace'=>$order['insured']['birthPlace'],
          'chineseName'=>$order['insured']['chineseName'],
          'citizenship'=>'HK',
          'countryRegionCode'=>null,
          'customerSequence'=>1,
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
            'address1'=>$record['insured']['mailing_same_as_residence']=='N'?$order['insured']['mailing_address1']:'',
            'address2'=>$record['insured']['mailing_same_as_residence']=='N'?$order['insured']['mailing_address2']:'',
            'address3'=>$record['insured']['mailing_same_as_residence']=='N'?$order['insured']['mailing_address3']:'',
            'city'=>$record['insured']['mailing_same_as_residence']=='N'?$order['insured']['mailing_city']:'',
            'country'=>$record['insured']['mailing_same_as_residence']=='N'?$order['insured']['mailing_country']:'',
            'postalCode'=>'',
            'telephone'=>'',
            'telephoneCountryCode'=>'',
          ),
          'marital'=>$order['insured']['marital'],
          'mobileNumber'=>$order['insured']['mobile'],
          'mobileNumberCountryCode'=>$order['insured']['mobile']?'852':'',
          'nationality'=>$order['insured']['nationality'],
          'occupationCode'=>$order['insured']['occupationCode'],
          'relationship'=>$order['owner']['relationship']=='INS'?$order['owner']['relationship']:'',
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
          'solicitation'=>false,
          'optOutReason'=>'',
          'surname'=>$order['insured']['surname'],
        ],
        [
          'age'=>$owner_age,
          'birthDate'=>$order['owner']['birthDate'],
          'birthPlace'=>$order['owner']['birthPlace'],
          'chineseName'=>$order['owner']['chineseName'],
          'citizenship'=>'HK',
          'countryRegionCode'=>null,
          'customerSequence'=>2,
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
            'address1'=>$record['owner']['mailing_same_as_residence']=='N'?$order['owner']['mailing_address1']:'',
            'address2'=>$record['owner']['mailing_same_as_residence']=='N'?$order['owner']['mailing_address2']:'',
            'address3'=>$record['owner']['mailing_same_as_residence']=='N'?$order['owner']['mailing_address3']:'',
            'chineseAddress1'=>'',
            'chineseAddress2'=>'',
            'chineseAddress3'=>'',
            'chineseAddress4'=>'',
            'chineseAddressIndicator'=>'',
            'chineseCountry'=>'',
            'chinesePostalCode'=>'',
            'city'=>$record['owner']['mailing_same_as_residence']=='N'?$order['owner']['mailing_city']:'',
            'country'=>$record['owner']['mailing_same_as_residence']=='N'?$order['owner']['mailing_country']:'',
            'postalCode'=>'',
          ),
          'marital'=>$order['owner']['marital'],
          'mobileNumber'=>$order['owner']['mobile'],
          'mobileNumberCountryCode'=>$order['owner']['mobile']?'852':'',
          'nationality'=>$order['owner']['nationality'],
          'occupation'=>'',
          'occupationCode'=>$order['owner']['occupationCode'],
          'relationship'=>$order['owner']['relationship']!='INS'?$order['owner']['relationship']:'',
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
          'age'=>$payor_age,
          'birthDate'=>$order['payor']['birthDate'],
          'birthPlace'=>'',
          'chineseName'=>'',
          'citizenship'=>'',
          'countryRegionCode'=>null,
          'customerSequence'=>3,
          'customerType'=>'P1',
          'email'=>'',
          'gender'=>$order['payor']['gender'],
          'givenName'=>$order['payor']['givenName'],
          'identityNumber'=>$order['payor']['identityNumber'],
          'identityType'=>$order['payor']['identityType'],
          'isPermanentHkid'=>true,
          'isValidIdType'=>true,
          'issueCountry'=>'',
          'mailing'=>array(
            'address1'=>'',
            'address2'=>'',
            'address3'=>'',
            'city'=>'',
            'country'=>'',
            'postalCode'=>'',
            'telephone'=>'',
            'telephoneCountryCode'=>'',
          ),
          'marital'=>'',
          'mobileNumber'=>'',
          'mobileNumberCountryCode'=>'',
          'nationality'=>'',
          'occupationCode'=>'',
          'relationship'=>'',
          'residence'=>array(
            'address1'=>'',
            'address2'=>'',
            'address3'=>'',
            'city'=>'',
            'country'=>'',
            'postalCode'=>'',
            'telephone'=>'',
            'telephoneCountryCode'=>'',
          ),
          'solicitation'=>'',
          'optOutReason'=>'',
          'surname'=>$order['payor']['surname'],
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
        'amountInPolicyCurrency'=>$order['initial_premium'],
        'authorizationCode'=>$order['authorizationCode'],
        'bankName'=>'',
        'basicPlanCode'=>$order['plan_code'],
        'cardHolderName'=>$order['cardHolderName'],
        'cardType'=>$order['cardType'],
        'currency'=>$order['currency'],
        'insuredFirstName'=>$order['insured']['givenName'],
        'insuredLastName'=>$order['insured']['surname'],
        'ownerFirstName'=>$order['owner']['givenName'],
        'ownerLastName'=>$order['owner']['surname'],
        'paymentReceivedDate'=>$order['paymentReceivedDate'],
        'payorFirstName'=>$order['payor']['givenName'],
        'payorLastName'=>$order['payor']['surname'],
        'payorRole'=>'O',
        'policyCurrency'=>$order['currency'],
        'referenceNumber'=>sprintf('TM%06d',$order['id']),
        'transactionCurrency'=>'HKD',
        'transaction'=>$transaction,
        'transactionStatus'=>'',
        'transactionUpdatedDate'=>$order['transactionUpdatedDate'],
      ),
      'policyNumber'=>'',
      'uploadId'=>'',
      'questionnaireDtos'=>[
        // [
        //   'customerType'=>'I',
        //   'questionnaireNumber'=>13,
        //   'questionnaireSectionId'=>14,
        //   'questionnaireText'=>null,
        //   'questionnaireYesOrNo'=>$health_detail_question,
        // ],
        [
          'customerType'=>'I',
          'questionnaireNumber'=>30,
          'questionnaireSectionId'=>14,
          'questionnaireText'=>null,
          'questionnaireYesOrNo'=>$q1,
        ],
        [
          'customerType'=>'I',
          'questionnaireNumber'=>31,
          'questionnaireSectionId'=>14,
          'questionnaireText'=>null,
          'questionnaireYesOrNo'=>$q2,
        ],
        [
          'customerType'=>'I',
          'questionnaireNumber'=>32,
          'questionnaireSectionId'=>14,
          'questionnaireText'=>null,
          'questionnaireYesOrNo'=>$q3,
        ],
        [
          'customerType'=>'I',
          'questionnaireNumber'=>33,
          'questionnaireSectionId'=>14,
          'questionnaireText'=>null,
          'questionnaireYesOrNo'=>$q4,
        ],
        [
          'customerType'=>'I',
          'questionnaireNumber'=>34,
          'questionnaireSectionId'=>14,
          'questionnaireText'=>null,
          'questionnaireYesOrNo'=>$q5,
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
