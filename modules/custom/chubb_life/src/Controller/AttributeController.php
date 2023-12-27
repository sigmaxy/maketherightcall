<?php

namespace Drupal\chubb_life\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Ilex\Validation\HkidValidation\Helper;

/**
 * Class AttributeController.
 */
class AttributeController extends ControllerBase {

  /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   */
  public static function get_reject_reason_options() {
    $results = array(
      '0' => '--Select--',
      '1' => 'Premium too high (expensive)',
      '2' => 'Protection period too long / short',
      '3' => 'Coverage not enough',
      '4' => 'Well covered',
      '5' => 'Coverage not interest',
      '6' => 'No need',
      '7' => 'Leaving HK',
      '8' => 'Unsatisfied with Partners',
      '9' => 'Unsatisfied with Chubb',
      '10' => 'Others',
    );
    return $results;
  }
  public static function get_record_per_page_options() {
    $results = array();
    $results[10] = 10;
    $results[25] = 25;
    $results[50] = 50;
    $results[100] = 100;
    return $results;
  }
  public static function get_order_status_options() {
    $results = array();
    $results[1] = t('Pending');
    $results[2] = t('Void');
    $results[3] = t('Success');
    $results[4] = t('Cancel');
    // $results[4] = t('Json Generated');
    return $results;
  }
  public static function get_call_status_options() {
    $results = array();
    $results[1] = t('Pending');
    $results[2] = t('Consider');
    $results[3] = t('DNQ');
    $results[4] = t('Reject');
    $results[5] = t('RTT');
    $results[6] = t('Busy');
    $results[7] = t('No Answer');
    $results[8] = t('Invalid Number');
    $results[9] = t('Success');
    $results[10] = t('Opt Out');
    return $results;
  }
  public static function get_id_type_options() {
    $results = array(
      'A' => 'Birth Certificate',
      'B' => 'Business Registration',
      'C' => 'Certificate of Incorporation',
      'H' => 'HKSAR Re-entry Permits',
      'I' => 'ID Card',
      'P' => 'Passport',
      'X' => 'Others',
      'S' => 'Single Entry Permit',
      'R' => 'PRC ID CARD',
    );
    return $results;
  }
  public static function get_country_list() {
    $connection = Database::getConnection();
    $query = $connection->select('mtrc_attribute_country', 'mac');
    $query->fields('mac');
    $record = $query->execute()->fetchAll();
    return $record;
  }
  public static function get_country_options() {
    $record = self::get_country_list();
    $results = array();
    foreach ($record as $data) {
      $results[$data->code] = $data->en.'/'.$data->hk.'/'.$data->cn;
    }
    return $results;
  }
  public static function get_relation_list() {
    $connection = Database::getConnection();
    $query = $connection->select('mtrc_attribute_relation', 'mar');
    $query->fields('mar');
    $record = $query->execute()->fetchAll();
    return $record;
  }
  public static function get_relation_options() {
    $record = self::get_relation_list();
    $results = array();
    foreach ($record as $data) {
      $results[$data->code] = $data->en.'/'.$data->hk.'/'.$data->cn;
    }
    return $results;
  }
  public static function get_marital_status_options() {
    $results = array();
    $results['S'] = t('Single');
    $results['M'] = t('Married');
    $results['W'] = t('Widowed');
    $results['D'] = t('Divorced');
    $results['C'] = t('Company');
    $results['U'] = t('Unknown');
    $results['P'] = t('Separated');
    return $results;
  }
  public static function get_occupations_group_options() {
    $connection = Database::getConnection();
    $query = $connection->select('mtrc_attribute_industry', 'mai');
    $query->fields('mai');
    $record = $query->execute()->fetchAll();
    foreach ($record as $each_industry) {
      $query_occ = $connection->select('mtrc_attribute_occupation', 'mao');
      $query_occ->fields('mao');
      $query_occ->condition('industry_id', $each_industry->id);
      $record_occ = $query_occ->execute()->fetchAll();
      $sub_results = array();
      foreach ($record_occ as $each_occupation) {
        $sub_results[$each_occupation->code] = $each_occupation->hk;
      }
      $results[$each_industry->hk] = $sub_results;
      # code...
    }
    return $results;
  }
  public static function get_yn_options() {
    $results = array();
    $results['Y'] = t('Yes');
    $results['N'] = t('No');
    return $results;
  }
  public static function get_gender_options() {
    $results = array();
    $results['M'] = t('Male');
    $results['F'] = t('Female');
    return $results;
  }
  public static function get_monthly_income_options() {
    $results = array();
    $results['10000'] = t('Less than HK$10,000');
    $results['14999.5'] = t('HK$10,000 - HK$19,999');
    $results['34999.5'] = t('HK$20,000 - HK$49,999');
    $results['75000'] = t('HK$50,000 - HK$100,000');
    $results['100000'] = t('Over HK$100,000');
    return $results;
  }
  public static function get_solicitation_options() {
    $results = array(
      'N' => 'No',
      'Y' => 'Yes',
    );
    return $results;
  }
  public static function get_opt_out_reason_options() {
    $results = array(
      'DND' => 'Do Not Disturb',
      'OFTA' => 'Report to OFTA',
      'COPS' => 'Report to Police',
      'Ptners' => 'Complained to Partners',
      'Media' => 'Complained to Media',
      'Online' => 'Post on Social media',
      'CHUBBTM' => 'CHUBB TM approaching',
    );
    return $results;
  }
  public static function get_currency_options() {
    $results = array();
    $results['HKD'] = t('HKD');
    $results['USD'] = t('USD');
    // $results['CNY'] = t('CNY');
    return $results;
  }
  public static function get_payment_mode_options() {
    $results = array(
      '12' => 'Annual',
      '01' => 'Monthly',
      // '3' => 'Quarterly',
      // '6' => 'Semi-Annual',
    );
    return $results;
  }
  public static function get_bill_type_options() {
    $results = array(
      'CCDDA' => 'Credit Card',
      'CUP' => 'CUP',
      'Autopay' => 'Autopay',
    );
    return $results;
  }
  public static function get_card_type_options() {
    $results = array(
      'FPS' => 'FPS',
      'VISA' => 'VISA',
      'MASTERCARD' => 'MASTERCARD',
      'CUP' => 'CUP',
    );
    return $results;
  }
  public static function get_dda_setup_options() {
    $results = array(
      '03' => '3rd',
      '18' => '18th',
    );
    return $results;
  }
  public static function get_beneficiary_relationship_options() {
    $results = array(
      'EST' => 'Estate',
    );
    return $results;
  }
  public static function get_face_amount_options() {
    $results = [
      'RHC5' =>[
        '1' => [
          'HKD'=>400,
          'USD'=>50,
        ],
        '2' => [
          'HKD'=>800,
          'USD'=>100,
        ],
        '3' => [
          'HKD'=>1200,
          'USD'=>150,
        ],
        '4' => [
          'HKD'=>1600,
          'USD'=>200,
        ],
      ],
      'RHC10' =>[
        '1' => [
          'HKD'=>400,
          'USD'=>50,
        ],
        '2' => [
          'HKD'=>800,
          'USD'=>100,
        ],
        '3' => [
          'HKD'=>1200,
          'USD'=>150,
        ],
        '4' => [
          'HKD'=>1600,
          'USD'=>200,
        ],
      ],
      'PAB10' =>[
        'A' => [
          'HKD'=>500000,
          'USD'=>64100,
        ],
        'B' => [
          'HKD'=>1000000,
          'USD'=>128200,
        ],
        'C' => [
          'HKD'=>1500000,
          'USD'=>192300,
        ],
      ],
      'PAB20' =>[
        'A' => [
          'HKD'=>500000,
          'USD'=>64100,
        ],
        'B' => [
          'HKD'=>1000000,
          'USD'=>128200,
        ],
        'C' => [
          'HKD'=>1500000,
          'USD'=>192300,
        ],
      ],
      'MCE' =>[
        '1' => [
          'HKD'=>1500000,
        ],
        '2' => [
          'HKD'=>1000000,
        ],
        '3' => [
          'HKD'=>500000,
        ],
        '4' => [
          'HKD'=>1600,
        ],
      ],
      'RST08' =>[
        '1' => [
          'HKD'=>3000,
        ],
        '2' => [
          'HKD'=>6000,
        ],
        '3' => [
          'HKD'=>'',
        ],
        '4' => [
          'HKD'=>10000,
        ],
      ],
      'RST10' =>[
        '1' => [
          'HKD'=>3000,
        ],
        '2' => [
          'HKD'=>6000,
        ],
        '3' => [
          'HKD'=>'',
        ],
        '4' => [
          'HKD'=>10000,
        ],
      ],
      'DG08H' =>[
        '1' => [
          'HKD'=>1500000,
        ],
        '2' => [
          'HKD'=>2000000,
        ],
        '3' => [
          'HKD'=>2500000,
        ],
        '4' => [
          'HKD'=>3000000,
        ],
      ],
      'DG08U' =>[
        '1' => [
          'USD'=>187500,
        ],
        '2' => [
          'USD'=>250000,
        ],
        '3' => [
          'USD'=>312500,
        ],
        '4' => [
          'USD'=>375000,
        ],
      ],
      'DG10H' =>[
        '1' => [
          'HKD'=>1500000,
        ],
        '2' => [
          'HKD'=>2000000,
        ],
        '3' => [
          'HKD'=>2500000,
        ],
        '4' => [
          'HKD'=>3000000,
        ],
      ],
      'DG10U' =>[
        '1' => [
          'USD'=>187500,
        ],
        '2' => [
          'USD'=>250000,
        ],
        '3' => [
          'USD'=>312500,
        ],
        '4' => [
          'USD'=>375000,
        ],
      ],
    ];
    return $results;
  }
  public static function get_promotion_code_arr() {
    $results = [
      'CC17' => [
        'plan'=>['RHC5','RHC10'],
        'discount'=>0.15
      ],
      'CC87' => [
        'plan'=>['RHC5','RHC10'],
        'discount'=>0.15
      ],
      'CD01' => [
        'plan'=>['RHC5','RHC10'],
        'discount'=>0
      ],
      'CD30' => [
        'plan'=>['RST08','RST10'],
        'discount'=>0.25
      ],
    ];
    return $results;
  }
  public static function check_hkid($hkid){
    // add () at last digit
    $formate_hkid = substr_replace($hkid,'('.substr($hkid, -1).')',-1);
    $a = Helper::checkByString($formate_hkid);
    return $a->isValid();
  }

}
