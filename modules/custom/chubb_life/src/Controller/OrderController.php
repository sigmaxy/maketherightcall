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
    return $record;
  }
  public static function list_order(){
    $connection = Database::getConnection();
    $query = $connection->select('mtrc_order', 'mo');
    $query->fields('mo');
    $record = $query->execute()->fetchAll();
    return $record;
  }

  public static function update_order($order){
    $connection = Database::getConnection();
    $db_record = self::check_order_existed($order['id']);
    $order['updated_at'] = time();
    $order['updated_by'] = \Drupal::currentUser()->id();
    if($db_record){
      $connection->update('mtrc_order')
        ->fields($order)
        ->condition('id', $db_record)
        ->execute();
    }else{
      $call_insert_id = $connection->insert('mtrc_order')
        ->fields($order)
        ->execute();
    }
  }
  public static function order_format_json($order){
    $results = array(
      'applicationDto'=>array(
        'referenceNumber'=>$order['referenceNumber'],
        'aeonRefNumber'=>$order['aeonRefNumber'],
        'accountHolder1'=>array(
          'holderName'=>'',
          'identityNumber'=>'',
          'identityType'=>'',
        ),
        'accountNumber'=>'',
        'agentCode'=>$order['agents_code'],
        'billingType'=>$order['billing_type'],
        'currency'=>$order['currency'],
        'ecopy'=>$order['ecopy'],
        'mailingAddressIndicator'=>'',
        'paymentMode'=>$order['paymentMode'],
        'pep'=>$order['pep'],
        'promoCode'=>'',
        'taxResidency1'=>array(
          'taxResidency'=>$order['taxResidency'],
          'taxResidencyTin'=>$order['taxResidencyTin'],
        ),
      ),
      'beneficiaryDtos'=>array(
        'beneficiaryClass'=>'',
        'beneficiarySequence'=>'',
        'chineseName'=>$order['customer_insured_chineseName'],
        'customerType'=>'',
        'givenName'=>$order['customer_insured_givenName'],
        'identityNumber'=>$order['customer_insured_identityNumber'],
        'relationship'=>$order['beneficiary_relationship'],
        'shared'=>'',
        'surname'=>$order['customer_insured_surname'],
      ),
      'benefitDtos'=>array(
        'attachTo'=>'',
        'coverageClass'=>'',
        'coverageCode'=>'',
        'coverageNumber'=>'',
        'currency'=>$order['currency'],
        'customerSequence'=>'',
        'customerType'=>'',
        'faceAmount'=>$order['face_amount'],
        'plannedPremium'=>$order['plan_code'],
        'protectionFaceAmount'=>'',
        'savingFaceAmount'=>'',
      ),
      'customerDtos'=>array(
        'age'=>'',
        'birthDate'=>$order['birthDate'],
        'birthPlace'=>'',
        'chineseName'=>$order['chineseName'],
        'citizenship'=>$order['nationality'],
        'countryRegionCode'=>$order['issueCountry'],
        'customerSequence'=>'',
        'customerType'=>'',
        'email'=>$order['email'],
        'gender'=>$order['gender'],
        'givenName'=>$order['givenName'],
        'identityNumber'=>$order['referenceNumber'],
        'identityType'=>$order['referenceNumber'],
        'isPermanentHkid'=>$order['isPermanentHkid'],
        'isValidIdType'=>'',
        'issueCountry'=>$order['issueCountry'],
        'mailing'=>array(
          'address1'=>$order['mailing_address1'],
          'address2'=>$order['mailing_address2'],
          'address3'=>$order['mailing_address3'],
          'city'=>$order['mailing_city'],
          'country'=>$order['mailing_country'],
          'postalCode'=>'',
          'telephone'=>'',
          'telephoneCountryCode'=>'',
        ),
        'marital'=>$order['marital_status'],
        'mobileNumber'=>$order['mobile'],
        'mobileNumberCountryCode'=>'',
        'nationality'=>$order['nationality'],
        'occupationCode'=>$order['referenceNumber'],
        'relationship'=>$order['relationship'],
        'residence'=>array(
          'address1'=>$order['residence_address1'],
          'address2'=>$order['residence_address2'],
          'address3'=>$order['residence_address3'],
          'city'=>$order['residence_city'],
          'country'=>$order['residence_country'],
          'postalCode'=>'',
          'telephone'=>'',
          'telephoneCountryCode'=>'',
        ),
        'solicitation'=>$order['referenceNumber'],
        'optOutReason'=>$order['referenceNumber'],
        'surname'=>$order['surname'],
      ),
      'paymentTransactionDto'=>array(
        'amount'=>'',
        'amountInPolicyCurrency'=>'',
        'authorizationCode'=>'',
        'bankName'=>'',
        'basicPlanCode'=>'',
        'cardHolderName'=>'',
        'cardType'=>'',
        'currency'=>$order['currency'],
        'insuredFirstName'=>$order['customer_insured_givenName'],
        'insuredLastName'=>$order['customer_insured_surname'],
        'ownerFirstName'=>$order['givenName'],
        'ownerLastName'=>$order['surname'],
        'paymentReceivedDate'=>'',
        'payorFirstName'=>$order['customer_payor_givenName'],
        'payorLastName'=>$order['customer_payor_surname'],
        'payorRole'=>'',
        'policyCurrency'=>'',
        'referenceNumber'=>'',
        'transactionStatus'=>'',
        'transactionUpdatedDate'=>'',
      ),
      'questionnaireDtos'=>array(
        'customerType'=>'',
        'questionnaireNumber'=>'',
        'questionnaireSectionId'=>'',
        'questionnaireText'=>'',
        'questionnaireYesOrNo'=>'',
      ),
    );
    return $results;
  }

}
