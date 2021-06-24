<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Webapp_api extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Dubai');
        $this->_clear_cache();
        $this->load->library(array('smart_lib'));
    }

    public function index()
    {
        header('Content-Type: application/json');
        $response = array('success' => 0, 'message' => "Unauthorized Access!");
        $this->output->set_output(json_encode($response));
    }

    function _clear_cache()
    {
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->output->set_header('Cache-Control: post-check=0, pre-check=0', false);
        $this->output->set_header('Pragma: no-cache');
    }

    public function get_employee_info()
    {
        header('Content-Type: application/json');
        $content = trim(file_get_contents("php://input"));
        $decoded = json_decode($content, true);
        $data = array();

        try {
            $this->db->select('employee_code,employee_id,employee_name,outscan_number');
            $this->db->from('tbl_employee_master');
            $this->db->order_by('employee_id', 'asc');
            $query = $this->db->get();
            $get_count = $query->num_rows();
            $empList = $query->result_array();
            $this->db->select('*');
            $this->db->from('tbl_route_master');
            $query = $this->db->get();
            $routeList = $query->result_array();
            $this->db->select('*');
            $this->db->from('tbl_vehice_list');
            $query = $this->db->get();
            $vehicleList = $query->result_array();


            if ($get_count > 0) {
                $response = array('success' => 1, 'message' => "Success!", 'employee_list' => $empList, 'route_list' => $routeList, 'vehicle_list' => $vehicleList);
                $this->output->set_output(json_encode($response));
            } else {
                $response = array('success' => 0, 'message' => "No Records");
                $this->output->set_output(json_encode($response));
            }
        } catch (\Exception $e) {
            $response = array('success' => 0, 'message' => "Failed!", 'error' => 'No Data');
            $this->output->set_output(json_encode($response));
        }
    }
    public function get_emoloyee_sheet_no()
    {
        header('Content-Type: application/json');
        $content = trim(file_get_contents("php://input"));
        $decoded = json_decode($content, true);
        $data = array();

        try {
            $this->db->select('*');
            $this->db->from('tbl_employee_master');
            $this->db->where('employee_id', $decoded['filter_emp_id']);
            $query = $this->db->get();
            $get_count = $query->num_rows();
            $empList = $query->row_array();

            if ($get_count > 0) {
                $response = array('success' => 1, 'message' => "Success!", 'values' => $empList);
                $this->output->set_output(json_encode($response));
            } else {
                $response = array('success' => 0, 'message' => "No Records");
                $this->output->set_output(json_encode($response));
            }
        } catch (\Exception $e) {
            $response = array('success' => 0, 'message' => "Failed!", 'error' => 'No Data');
            $this->output->set_output(json_encode($response));
        }
    }
    public function get_booking_info()
    {
        header('Content-Type: application/json');
        $content = trim(file_get_contents("php://input"));
        $decoded = json_decode($content, true);
        $data = array();

        try {
            $this->db->select('*');
            $this->db->from('tbl_booking');
            $this->db->where('booking_number', $decoded['filter_booking_no']);
            $query = $this->db->get();
            $get_count = $query->num_rows();
            $bookingList = $query->row_array();

            if ($get_count > 0) {
                $response = array('success' => 1, 'message' => "Success!", 'values' => $bookingList);
                $this->output->set_output(json_encode($response));
            } else {
                $response = array('success' => 0, 'message' => "No Records");
                $this->output->set_output(json_encode($response));
            }
        } catch (\Exception $e) {
            $response = array('success' => 0, 'message' => "Failed!", 'error' => 'No Data');
            $this->output->set_output(json_encode($response));
        }
    }

    public function save_booking_info()
    {
        // var_dump('here'); exit();
        header('Content-Type: application/json');
        $content = trim(file_get_contents("php://input"));
        $filter = json_decode($content, true);

// var_dump($filter); exit();

        $data = array();

        try {

            $outScanMaster = array();
            $outScanMaster['employee_code'] = $filter['employee_code'];
            $outScanMaster['outscan_sheetno'] = $filter['sheet_no'] . "-" . $filter['outscan_no'];
            $outScanMaster['employee_name'] = $filter['emp_name'];
            $outScanMaster['outscan_date'] = $filter['filter_date'];
            $outScanMaster['user_name'] = "shajahan";
            $outScanMaster['user_id'] = 0;
            $outScanMaster['vehicle_no'] = $filter['vehicle_number'];
            $outScanMaster['route_name'] = $filter['route_name'];

            $outScanDetails = array();
            $mobailOutScanDetails = array();
            $bookingData = array();
            $shipData = array();
            $msgData = array();
            $scannedList = isset($filter['scanned_list']) ? $filter['scanned_list'] : array();
            $sheetno = $filter['sheet_no'] . "-" . $filter['outscan_no'];
            // var_dump($vard); exit();
            // var_dump($sheetno); exit;
            
           
            if (is_array($scannedList) && count($scannedList) > 0) {

                foreach ($scannedList as $item) {
                    if ($item['payment_mode'] == "COD" || $item['payment_mode'] == "Cash on Delivery") {
                        $totalAmount = ($item['courier_charge'] + $item['ncnd_amount']);
                    } else {
                        $totalAmount = $item['ncnd_amount'];
                    }
                    $msg = "Dear Customer, your shipment Ref No:" . $item['booking_number'] . " has been out for delivery, ";
                    $msg = $msg . "for tracking https://ekdelivers.freightworks.com/backend/awb/awb_status?awb=".$item['booking_number']." or further assistance call 600777";
                    $to_mobileno = ltrim($item['to_mobileno'], '+');
                    $to_mobileno = ltrim($to_mobileno, '971');
                    $to_mobileno = ltrim($to_mobileno, '0');
                    $to_mobileno = '971' . $to_mobileno;
                    $outScanDetails[] = array(
                        'outscan_sheetno' => $sheetno,
                        'booking_number' => $item['booking_number'],
                        'booking_date' => $item['booking_date'],
                        'booking_reference' => $item['booking_reference'],
                        'company_code' => $item['company_code'],
                        'from_company' => $item['from_company'],
                        'from_address' => $item['from_address'],
                        'from_location' => $item['from_location'],
                        'from_cperson' => $item['from_cperson'],
                        'from_contactno' => $item['from_contactno'],
                        'from_mobileno' => $item['from_mobileno'],
                        'to_company' => $item['to_company'],
                        'to_address' => $item['to_address'],
                        'to_location' => $item['to_location'],
                        'to_cperson' => $item['to_cperson'],
                        'to_contactno' => $item['to_contactno'],
                        'to_mobileno' => $item['to_mobileno'],
                        'payment_type' => $item['payment_type'],
                        'service_name' => $item['service_name'],
                        'weight' => $item['weight'],
                        'pieces' => $item['pieces'],
                        'item_description' => $item['item_description'],
                        'special_instruction' => $item['special_instruction'],
                        'cod_amount' => $item['ncnd_amount'],
                        'consignee_latitude' => $item['consignee_latitude'],
                        'consignee_longitude' => $item['consignee_longitude'],
                        'delivery_prefered_date' => $item['delivery_prefered_date'],
                        'delivery_prefered_time' => $item['delivery_prefered_time'],
                    );
                    
                    if($item['consignee_latitude'] >= 22 && $item['consignee_latitude'] <= 27 && $item['consignee_longitude'] >= 55 && $item['consignee_longitude'] <= 57){

                       $json = $this->smart_lib->getDistance(25.257128181867756,55.34054465429802,$item['consignee_latitude'],$item['consignee_longitude']);
                       
                        
                        $distance_data = json_decode($json);
                       
                       
                        if($distance_data->status=='OK'){
                            $distance = rtrim($distance_data->rows[0]->elements[0]->distance->text,'km');
                        }else
                            $distance ='0';

                    }
                    
                    else{
                        $distance = '0';
                    }
                    
                     

                    $mobailOutScanDetails[] = array(
                        'booking_date' => $item['booking_date'],
                        'referenceNo' => $item['booking_number'],
                        'customercode' => $item['company_code'],
                        'runsheetNo' => $filter['employee_location'],
                        'jobDate' => date('Y-m-d'),
                        'employeeCode' => $filter['employee_code'],
                        'consignee' => $item['to_company'],
                        'consigneeCPerson' => $item['to_cperson'],
                        'consigneeLocation' => $item['to_location'],
                        'consigneeContact' => $item['to_contactno'],
                        'consigneemobile' => $item['to_mobileno'],
                        'consigneeAddress' => $item['to_address'],
                        'consignor' => $item['from_company'],
                        'consignorCPerson' => $item['from_cperson'],
                        'consignorLocation' => $item['from_location'],
                        'consignorContact' => $item['from_contactno'],
                        'consignorMobile' => $item['from_mobileno'],
                        'consignorAddress' => $item['from_address'],
                        'paymentMode' => $item['payment_mode'],
                        'totalAmount' => $totalAmount,
                        'courierCost' => $item['courier_charge'],
                        'materialCost' => $item['ncnd_amount'],
                        'companyDivision' => "1",
                        'employee_name' => $filter['emp_name'],
                        'current_status' => "Out For Delivery",
                        'status_datettime' => date('Y-m-d h:i:s'),
                        'productname' => $item['service_name'],
                        'senderreferenceno' => $item['reference_number'],
                        'weight' => $item['weight'],
                        'pieces' => $item['pieces'],
                        'item_description' => $item['item_description'],
                        'special_instruction' => $item['special_instruction'],
                        'mobile_outscan' => "1",
                        'done_by' => "shajahan",
                        'prefered_time' => $item['delivery_prefered_time'],
                        'consignee_latitude' => $item['consignee_latitude'],
                        'consignee_longitude' => $item['consignee_longitude'],
                        'distance_kms'          => $distance,

                    );
                    

                    $bookingData[] = array(
                        'status_datetime'       => date('Y-m-d h:i:s'),
                        'current_status'        => "Out for Delivery",
                        'status_details'        => "Out for Delivery",
                        'outemployee_name'      => $filter['emp_name'],
                        'outscansheet_number'   => $filter['sheet_no'] . "-" . $filter['outscan_no'],
                        'outemployee_location'  => "Dubai",
                        'outscan_datetime'      => date('Y-m-d h:i:s'),
                        'outscan_status'        => 1,
                        'distance_kms'          => $distance,
                        "booking_number"        => $item['booking_number'],
                        
                    );
                    $shipData = array(
                        'booking_number' => $item['booking_number'],
                        'status_datetime' => date('Y-m-d h:i:s'),
                        'location' => "Dubai",
                        'courier_status' => 'Out for Delivery',
                        'status_details' => 'Shipment out for delivery',
                        'employee_name' => $filter['emp_name'],
                        'done_by' => "shajahan",
                    );
                    $suffix_1 = date('mY', strtotime($item['booking_date']));
                    $this->load->model('status_model');
                    $this->status_model->insert_status($suffix_1,$shipData);

                    if ($item['to_mobileno'] != '') {
                        $msgData[] = array(
                            'mobile_no' => $to_mobileno,
                            'sms_text' => $msg
                        );
                    }
                }

                if(!empty($msgData)){
                    $this->db->trans_begin();
                    $this->db->insert_batch('tbl_sms_send', $msgData);
                    $msgId = $this->db->insert_id();
                }

                // $this->db->insert_batch('tbl_ship_status', $shipData);
                $shipId = $this->db->insert_id();
                if ($shipId <= 0) {
                    $this->db->trans_complete();
                    $this->db->trans_rollback();
                    $response = array('success' => 0, 'message' => "ship status insertion failed..");
                    $this->output->set_output(json_encode($response));
                }
                $this->db->update_batch('tbl_booking', $bookingData, 'booking_number');
                $isLineUpdate = ($this->db->affected_rows() > 0) ? true : false;
                if ($isLineUpdate == FALSE) {
                    $this->db->trans_complete();
                    $this->db->trans_rollback();
                    $response = array('success' => 0, 'message' => "can not update Booking Data");
                    $this->output->set_output(json_encode($response));
                }
                $this->db->insert('tbl_outscan_master', $outScanMaster);
                $outscanId = $this->db->insert_id();
                if ($outscanId <= 0) {
                    $this->db->trans_complete();
                    $this->db->trans_rollback();
                    $response = array('success' => 0, 'message' => "Out scan master  insertion failed..");
                    $this->output->set_output(json_encode($response));
                }
                $this->db->insert_batch('tbl_outscan_details', $outScanDetails);
                $outscanDetailsId = $this->db->insert_id();
                if ($outscanDetailsId <= 0) {
                    $this->db->trans_complete();
                    $this->db->trans_rollback();
                    $response = array('success' => 0, 'message' => "Out scan Details  insertion failed..");
                    $this->output->set_output(json_encode($response));
                }
                $this->db->insert_batch('mobile_outscan', $mobailOutScanDetails);
                $mobailScanId = $this->db->insert_id();
                if ($mobailScanId <= 0) {
                    $this->db->trans_complete();
                    $this->db->trans_rollback();
                    $response = array('success' => 0, 'message' => "Mobile outscan  insertion failed.. ");
                    $this->output->set_output(json_encode($response));
                }
                $suffix = date('mY');
                $tbl_mobile_outscan = 'mobile_outscan_' . $suffix;
                if ($this->db->table_exists($tbl_mobile_outscan) == FALSE) {
                    $this->db->query('CREATE TABLE ' . $tbl_mobile_outscan . ' LIKE  clone_mobile_outscan');
                }
                $this->db->insert_batch($tbl_mobile_outscan, $mobailOutScanDetails);
                $mobailCloneScanId = $this->db->insert_id();
                if ($mobailCloneScanId <= 0) {
                    $this->db->trans_complete();
                    $this->db->trans_rollback();
                    $response = array('success' => 0, 'message' => "monthly mobile outscan  insertion failed..");
                    $this->output->set_output(json_encode($response));
                }
                if(floatval($filter['outscan_number']) == 1000){
                    $this->db->set('outscan_number', 1001, FALSE);
                }else{
                    $this->db->set('outscan_number', 'outscan_number+1', FALSE);
                    // echo $this->db->last_query();
                }
                // echo $this->db->last_query();
                $this->db->where('employee_code',$filter['employee_code']);
                $this->db->update('tbl_employee_master');
                $isUpdated = ($this->db->affected_rows() > 0) ? true : false;
                if($isUpdated == false){
                    $this->db->trans_complete();
                    $this->db->trans_rollback();
                    $response = array('success' => 0, 'message' => "can not update next outscan no..");
                    $this->output->set_output(json_encode($response));
                }

                $this->db->trans_complete();
                $this->db->trans_commit();

                $response = array('success' => 1, 'message' => "Success!");
                $this->output->set_output(json_encode($response));
            } else {
                $response = array('success' => 0, 'message' => "scanned list missing..");
                $this->output->set_output(json_encode($response));
            }
        } catch (\Exception $e) {
            $response = array('success' => 0, 'message' => "Failed!", 'error' => 'No Data');
            $this->output->set_output(json_encode($response));
        }
    }
}
