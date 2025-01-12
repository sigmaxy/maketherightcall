<?php

namespace Drupal\chubb_life\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\chubb_life\Controller\CallController;
use Drupal\chubb_life\Controller\CustomerController;
use Drupal\chubb_life\Controller\AttributeController;
use Drupal\chubb_life\Controller\OrderController;


/**
 * Class ProductController.
 */
class ReportController extends ControllerBase {

  /**
   * Qwe.
   *
   * @return string
   *   Return Hello string.
   */
    public static function prepare_call_report_data(){
        $call_status_opt = AttributeController::get_call_status_options();
        $call_list = CallController::list_call();
        foreach ($call_list as $each_call) {
            $import_customer = CustomerController::get_import_customer_by_id($each_call->import_customer_id);
            $agent = \Drupal\user\Entity\User::load($each_call->assignee_id);
            $each_data = [
                date('Y', $each_call->updated_at),//colum A year
                ceil(date('m', $each_call->updated_at)/3),//colum B Quarter
                date('m', $each_call->updated_at),//colum C Month
                date('d', $each_call->updated_at),//colum D Date
                date('Y-m-d', $each_call->updated_at),//colum E call Date
                '',//colum F call hour
                $each_call->count,//colum G Previous call
                '=COUNTIFS(AK:AK,AK2,E:E,"=" &E2)',//colum H same day call
                '',//colum I reachable
                '',//colum J Presentable
                '',//colum K List Batch
                'TM6',//colum L Segment
                $agent->field_agentcode->value,//colum M Agent ID
                $agent->field_agentname->value,//colum N Agent Name
                $each_call->id,//colum O Call ID
                '',//colum P Start Time
                '',//colum Q DNIS
                '',//colum R Service Type
                '',//colum S Campaign
                '',//colum T CLI
                $call_status_opt[$each_call->status],//colum U Status
                '',//colum V Talk Time
                $each_call->assignee_id,//colum W Agent ID
                $agent->field_agentname->value,//colum X Agent Name 
                '',//colum Y Hang Up
                '',//colum Z Failover Dest
                '',//colum AA Conid
                '',//colum AB LUCS Chanel ID
                '',//colum AC LUCS Call ID
                '',//colum AD PABX Domain
                '',//colum AE PABX CallId
                '',//colum AF CSA UUI
                '',//colum AG Ccxml Flow
                '',//colum AH Vxml Flow
                '',//colum AI COS SDP
                '',//colum AJ Call List ID
                $import_customer['cust_ref'],//colum AK Customer ID
                '',//colum AL Call Result
                '',//colum AM Caller Num 
                $import_customer['tel_mbl'],//colum AN Called Num 
                '',//colum AO DNIS Perfix
                '',//colum AP Call Access Time
                '',//colum AQ Call Answer Time
                '',//colum AR Call Finish Time
                '',//colum AS Hangup Time
                '',//colum AT Call Hold Time
                '',//colum AU Call Ring Time
                '',//colum AV Call Agent Ext
                '',//colum AW Full Dup Rec File
                '',//colum AX File State
                '',//colum AY Exit Point
                '',//colum AZ Transfer Num
                '',//colum BA Transfer Result
                '',//colum BB Release Code
                '',//colum BC Release Desc
                '',//colum BD Created By
                '',//colum BE Created Time
                '',//colum BF Label
                $each_call->remark,//colum BG Remark


            ];
            $excel['data'][] = $each_data;
        }
        $excel['header'] = ['Year','Quarter','Month','Date','Call Date','Call Hour','Previous Calls','Same day Call','Reachable','Presentable','List Batch','Segment','Agent ID','Agent Name','Call ID','Start Time','DNIS','Service Type','Campaign','CLI','Status','Talk Time','Agent ID','Agent Name','Hang Up','Failover Dest','Conid','LUCS Chanel ID','LUCS Call ID','PABX Domain','PABX CallId','CSA UUI','Ccxml Flow','Vxml Flow','COS SDP','Call List ID','Customer ID','Call Result','Caller Num','Called Num','DNIS Perfix','Call Access Time','Call Answer Time','Call Finish Time','Hangup Time','Call Hold Time','Call Ring Time','Call Agent Ext','Full Dup Rec File','File State','Exit Point','Transfer Num','Transfer Result','Release Code','Release Desc','Created By','Created Time','Label','Remark'];
        $phpexcel = \Drupal::service('phpexcel');
        $report_file_name = 'Call_Report_'.date('Ymd').'.xlsx';
        $report_file_prefix = 'public://temp/'.date('Ymdhis').'/';
        \Drupal::service('file_system')->prepareDirectory($report_file_prefix, \Drupal\Core\File\FileSystemInterface::CREATE_DIRECTORY);
        $excel['path'] = $report_file_prefix.$report_file_name;
        $phpexcel->export($excel['header'], $excel['data'], $excel['path']);
        return $excel;
    }
    public static function prepare_sales_report_data(){
        $roles = \Drupal::currentUser()->getRoles();
        $uid = \Drupal::currentUser()->id();
        $order_status = AttributeController::get_order_status_options();
        $conditions = [];
        // if(in_array('administrator', $roles)){
        //     $conditions['uid'] = null;
        //     $conditions['teams'] = null;
        // }else if(in_array('manager', $roles)) {
        //     $conditions['uid'] = null;
        //     $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id()); // pass your uid
        //     $teams = [];
        //     foreach ($user->get('field_team')->getValue() as $key => $value) {
        //         $teams[] = $value['value'];
        //     }
        //     $conditions['teams'] = $teams;
        // }else{
        //     $conditions['uid'] = $uid;
        // }
        $order_list = OrderController::list_order($conditions);
        foreach ($order_list as $each_order) {
            $record= OrderController::get_order_by_id($each_order->id);
            $payment_mode_opt = AttributeController::get_payment_mode_options();
            if($each_order->paymentMode=='12'){
                
                $column_r = number_format((float)$each_order->initial_premium, 2, '.', '');
            }else if($each_order->paymentMode=='01'){
                $column_r = number_format((float)$each_order->initial_premium*6, 2, '.', '');
            }else{
                $column_r = '';
            }
            if($column_r!=''){
                $column_s = number_format((float)$column_r/7.8, 2, '.', '');
            }else{
                $column_s = '';
            }
            $each_data = [
                date('Y', $each_order->created_at),//colum A year
                ceil(date('m', $each_order->created_at)/3),//colum B Quarter
                date('m', $each_order->created_at),//colum C Month
                date('d', $each_order->created_at),//colum D Date
                '',//colum E list batch
                'TM',//colum F Segment
                $each_order->agentCode,//colum G Agent ID
                $each_order->agentName,//colum H Agent Name
                $each_order->aeonRefNumber,//colum I Cust_REF
                date('Y-m-d', $each_order->created_at),//colum J Order Date
                date('H:i:s', $each_order->created_at),//colum K Order Time
                '',//colum L Lead Batch #
                $each_order->plan_code,//colum M Product
                $each_order->plan_level,//colum N Plan
                $record['owner']['surname'].' '.$record['owner']['givenName'],//colum O Insured
                $each_order->beneficiary_relationship,//colum P Insured Relationship
                $each_order->currency,//colum Q HKD
                $column_r,//colum R APE (HKD)
                $column_s,//colum S APE (USD)
                $payment_mode_opt[$each_order->paymentMode],//colum T Payment Mode
                $each_order->initial_premium,//colum U Initial Premium (HKD)
                number_format((float)$each_order->initial_premium/7.8, 2, '.', ''),//colum V Initial Premium (USD)
                $each_order->cardType,//colum W Card Type
                '',//colum X Release Status
                '',//colum Y Release Date
                '',//colum Z Released by
                '',//colum AA Policy No
                sprintf('TM%06d',$each_order->id),//colum AB Reference No
                $each_order->remarks,//colum AC Remarks
            ];
            $excel['data'][] = $each_data;
        }
        $excel['header'] = ['Year','Quarter','Month','Date','List Batch','Segment','Agent ID','Agent Name','Cust_REF','Order Date','Order Time','Lead Batch #','Product','Plan','Insured','Insured Relationship','HKD','APE (HKD)','APE (USD)','Payment Mode','Initial Premium (HKD)','Initial Premium (USD)','Card Type','Release Status','Release Date','Released by','Policy No','Reference No','Remarks'];
        $phpexcel = \Drupal::service('phpexcel');
        $report_file_name = 'Sales_Report_'.date('Ymd').'.xlsx';
        $report_file_prefix = 'public://temp/'.date('Ymdhis').'/';
        \Drupal::service('file_system')->prepareDirectory($report_file_prefix, \Drupal\Core\File\FileSystemInterface::CREATE_DIRECTORY);
        $excel['path'] = $report_file_prefix.$report_file_name;
        $phpexcel->export($excel['header'], $excel['data'], $excel['path']);
        return $excel;
    }

    public static function prepare_call_lead_status_report_data(){
        $import_customer_list = CustomerController::list_import_customer();
        foreach ($import_customer_list as $each_customer) {
            $agent_created = \Drupal\user\Entity\User::load($each_customer->created_by);
            $agent_updated = \Drupal\user\Entity\User::load($each_customer->updated_by);
            $each_data = [
                $each_customer->id,//colum A ID
                $each_customer->cust_ref,//colum B cust_ref
                $each_customer->team,//colum C Team
                $each_customer->name,//colum D name
                $each_customer->gender,//colum E gender
                $each_customer->tel_mbl,//colum F tel_mbl
                $each_customer->tel_hom,//colum G tel_hom
                $each_customer->hkid,//colum H hkid
                $each_customer->acc_no,//colum I acc_no
                $each_customer->card_brand,//colum J card_brand
                $each_customer->card_type,//colum K card_type
                $each_customer->dob,//colum L dob
                $each_customer->married_status,//colum M married_status
                $each_customer->address,//colum N address
                $each_customer->living_person,//colum O living_person
                $each_customer->email,//colum P email
                $each_customer->member_since,//colum Q member_since
                $each_customer->occupation,//colum R occupation
                $each_customer->position,//colum S position
                $each_customer->fid,//colum T fid
                date('Y-m-d', $each_customer->created_at),//colum U created_at
                $agent_created->field_agentname->value,//colum V created_by
                date('Y-m-d', $each_customer->updated_at),//colum W updated_at
                $agent_updated->field_agentname->value,//colum X updated_by
                'TM6',//colum Y Segment
            ];
            $excel['data'][] = $each_data;
        }
        $excel['header'] = ['id','cust_ref','Team','name','gender','tel_mbl','tel_hom','hkid','acc_no','card_brand','card_type','dob','married_status','address','living_person','email','member_since','occupation','position','fid','created_at','created_by','updated_at','updated_by','Segment'];
        $phpexcel = \Drupal::service('phpexcel');
        $report_file_name = 'Call_Lead_Status_Report_'.date('Ymd').'.xlsx';
        $report_file_prefix = 'public://temp/'.date('Ymdhis').'/';
        \Drupal::service('file_system')->prepareDirectory($report_file_prefix, \Drupal\Core\File\FileSystemInterface::CREATE_DIRECTORY);
        $excel['path'] = $report_file_prefix.$report_file_name;
        $phpexcel->export($excel['header'], $excel['data'], $excel['path']);
        return $excel;
    }
}
