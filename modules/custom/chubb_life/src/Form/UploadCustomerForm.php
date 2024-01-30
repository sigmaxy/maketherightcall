<?php

namespace Drupal\chubb_life\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Drupal\Core\Link;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use Drupal\Core\Database\Database;
use Drupal\chubb_life\Controller\AttributeController;
use Drupal\chubb_life\Controller\CustomerController;
use Spatie\Async\Pool;
use Drupal\Core\Site\Settings;
/**
 * Class UploadCustomerForm.
 */
class UploadCustomerForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'upload_customer_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id()); // pass your uid
    $teams = [];
    foreach ($user->get('field_team')->getValue() as $key => $value) {
      $teams[$value['value']] = $value['value'];
    }
    $validators = array(
      'file_validate_extensions' => array('csv xlsx'),
    );
    $form['customer_file'] = array(
      '#type' => 'managed_file',
      '#title' => t('Purchase Order Excel File'),
      '#size' => 20,
      '#description' => t('XLSX CSV format only'),
      '#upload_validators' => $validators,
      '#upload_location' => 'public://temp/',
      '#weight' => '1',
    );
    $url = \Drupal::service('file_url_generator')->generate('public://mtrc/sample_customer.xlsx'); 
    $url = file_create_url('public://mtrc/sample_customer.xlsx'); 
    // print_r($url);exit;
    $form['team'] = [
      '#title' => $this->t('Team'),
      '#type' => 'select',
      '#options' => $teams,
      '#empty_option' => '--Select--',
      // '#default_value' => isset($conditions['assignee_id'])?$conditions['assignee_id']:'',
      '#attributes' => [
        'class' => ['noselect2'],
      ],
      '#required'=> true,
      '#weight' => '2',
    ];
    $form['sample'] = [
      '#type' => 'link',
      '#title' => $this->t('customer sample xlsx'),
      "#weight" => 1,
      '#url' => \Drupal::service('file_url_generator')->generate('public://mtrc/sample_customer.xlsx'),
      '#attributes' => ['target' => '_blank'],
      '#prefix' => '<div class="form_item_maxwidth">', '#suffix' => '</div>',
      '#weight' => '3',
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Import'),
      '#button_type' => 'primary',
      '#weight' => '4',
    );
    
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('customer_file') == NULL) {
      $form_state->setErrorByName('customer_file', $this->t('Fail to upload the file'));
    }
  }
  public function process_customer_data($formatesheetData,$fid,$customer){
    $pool = Pool::create();
    foreach ($formatesheetData as $each_data) {
      $pool->add(function () use($each_data,$fid){
        $customer = $each_data;
        $customer['fid'] = $fid;
        $customer['member_since'] = $each_data['member since'];
        unset($customer['member since']);
        CustomerController::update_import_customer($customer);
        CustomerController::update_import_customer_happy_client($customer);
      })->then(function ($output) {
      })->catch(function (Throwable $exception) {
      });
      $pool->wait();
    }
  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $team = $form_state->getValue('team');
    $fid = $form_state->getValue('customer_file')[0];
    $file = File::load($fid);
    $file_uri = \Drupal::service('file_system')->realpath($file->getFileUri());
    // print_r($file_uri);exit;
    $inputFileType = IOFactory::identify($file_uri);
    $reader = IOFactory::createReader($inputFileType);
    $reader->setReadDataOnly(TRUE);
    $spreadsheet = $reader->load($file_uri);
    $sheetData = $spreadsheet->getSheet(0)->toArray();
    $formatesheetData = array();
    $header = array();
    $header_row_index = 0;
    foreach ($sheetData as $key => $row_data) {
      if($key == $header_row_index){
        $header = array_filter($row_data);
      }else if($key > $header_row_index && !empty($row_data[0])){
        foreach ($row_data as $column_index => $column_data) {
          if(!empty($column_data))
          $formatesheetData[$key][strtolower($header[$column_index])] = $column_data;
        }
      }
    }
    foreach ($formatesheetData as $po_number => $each_data) {
      $customer = $each_data;
      $customer['fid'] = $fid;
      $customer['team'] = $team;
      $customer['member_since'] = $each_data['member since'];
      unset($customer['member since']);
      CustomerController::update_import_customer($customer);
      if(\Drupal\Core\Site\Settings::get('happy_client')){
        CustomerController::update_import_customer_happy_client($customer);
      }
    }
    // self::process_customer_data($formatesheetData,$fid,$customer);
    \Drupal::messenger()->addMessage(t('File has been uploaded.'));
  }

}