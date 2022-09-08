<?php

namespace Drupal\chubb_life\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
  public static function get_call_status_options() {
    $results = array();
    $results[1] = t('Pending');
    $results[2] = t('Consider');
    $results[3] = t('DNQ');
    $results[4] = t('Reject');
    $results[5] = t('RTT');
    return $results;
  }
  public static function get_id_type_options() {
    $results = array(
      'I' => 'HKID',
      'B' => 'Birth Certificate',
      'P' => 'Passport',
      'O' => 'Other ID card',
      'C' => 'PRC Residen Identity Card',
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
    $results[1] = t('Single');
    $results[2] = t('Married');
    $results[3] = t('Divorced');
    $results[4] = t('Widowed');
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
    $results['10,000'] = t('Less than HK$10,000');
    $results['14,999.5'] = t('HK$10,000 - HK$19,999');
    $results['34,999.5'] = t('HK$20,000 - HK$49,999');
    $results['75,000'] = t('HK$50,000 - HK$100,000');
    $results['100,000'] = t('Over HK$100,000');
    return $results;
  }
  public static function get_solicitation_options() {
    $results = array();
    $results['N'] = t('Opt Out');
    $results['Y'] = t('Not Opt Out');
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
    $results['CNY'] = t('CNY');
    return $results;
  }
  public static function get_payment_mode_options() {
    $results = array(
      '12' => 'Annual',
      '1' => 'Monthly',
      '3' => 'Quarterly',
      '6' => 'Semi-Annual',
    );
    return $results;
  }
  public static function get_bill_type_options() {
    $results = array(
      'Credit Card' => 'Credit Card',
      'CUP' => 'CUP',
    );
    return $results;
  }
  public static function get_dda_setup_options() {
    $results = array(
      '3' => '3rd',
      '18' => '18th',
    );
    return $results;
  }


}
