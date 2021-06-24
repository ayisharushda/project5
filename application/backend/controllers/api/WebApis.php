<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once('ApiService.php');

class WebApis extends ApiService {

    public function __construct() {
        parent::__construct();
        $this->load->helper(array('url', 'apputil', "appdate"));
        $this->load->library(array('AppConst', 'Tbl'));
        $this->load->model('AdminModel');
    }

    public function get_emoloyee_details_post() {

        $req = json_decode(file_get_contents('php://input'), TRUE);
    
        if ($req != '') {

            $res = $this->AdminModel->getEmployeeDetails($req);
        } else {

            $res = array();
            $res[AppConst::errorindex_code] = 20;
            $res[AppConst::errorindex_message] = 'invalid request';
        }

        $this->set_response($res, REST_Controller::HTTP_OK);
    }
    public function get_emoloyee_sheet_no_post() {

        $req = json_decode(file_get_contents('php://input'), TRUE);
    
        if ($req != '') {

            $res = $this->AdminModel->getEmployeeInfo($req);
        } else {

            $res = array();
            $res[AppConst::errorindex_code] = 20;
            $res[AppConst::errorindex_message] = 'invalid request';
        }

        $this->set_response($res, REST_Controller::HTTP_OK);
    }
    public function get_booking_info_post() {

        $req = json_decode(file_get_contents('php://input'), TRUE);
 
        if ($req != '') {

            $res = $this->AdminModel->getBookingInfo($req);
        } else {

            $res = array();
            $res[AppConst::errorindex_code] = 20;
            $res[AppConst::errorindex_message] = 'invalid request';
        }

        $this->set_response($res, REST_Controller::HTTP_OK);
    }
    
    public function save_booking_info_post() {

        $req = json_decode(file_get_contents('php://input'), TRUE);
        // print_r($req);
        // die();
        if ($req != '') {
            $res = $this->AdminModel->saveBookingInfo($req);
        } else {

            $res = array();
            $res[AppConst::errorindex_code] = 20;
            $res[AppConst::errorindex_message] = 'invalid request';
        }

        $this->set_response($res, REST_Controller::HTTP_OK);
    }
    public function get_outscan_details_post() {

        $req = json_decode(file_get_contents('php://input'), TRUE);
        // print_r($req);
        // die();
        if ($req != '') {
            $res = $this->AdminModel->getOutScanInfo($req);
        } else {

            $res = array();
            $res[AppConst::errorindex_code] = 20;
            $res[AppConst::errorindex_message] = 'invalid request';
        }

        $this->set_response($res, REST_Controller::HTTP_OK);
    }
    
    public function get_emoloyee_data_post() {

        $req = json_decode(file_get_contents('php://input'), TRUE);
        // print_r($req);
        // die();
        if ($req != '') {
            $res = $this->AdminModel->getEmployeeData($req);
        } else {

            $res = array();
            $res[AppConst::errorindex_code] = 20;
            $res[AppConst::errorindex_message] = 'invalid request';
        }

        $this->set_response($res, REST_Controller::HTTP_OK);
    }
    

}
