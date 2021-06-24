<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

class Apiservice extends REST_Controller {

    const requestValueIndex = 'inputs';
    
    const requestModeIndex = 'modecode';
    const requestModeJson = 100;
    const requestModeString = 200;
    const requestModeBase64 = 300;
    const requestModeEncription = 400;

    public function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
    }

    protected function encodeResponse($val = null) {
        if ($val != null && is_array($val)) {

            $val['CRP'] = "0";
        }
        return $val;
    }

    protected function decodeRequest($req = null) {
        $res = array();
        if (isset($req) && $req != null && is_array($req) && isset($req[Apiservice::requestValueIndex])) {
            $res[AppConst::errorindex_code] = AppConst::errorcode_success;
            $res[AppConst::errorindex_message] = AppConst::errormessage_success;

            $crp = isset($req[Apiservice::requestModeIndex]) ? intval($req[Apiservice::requestModeIndex]) : 0;
            if ($crp == Apiservice::requestModeJson) {

                $res['json'] = $req[Apiservice::requestValueIndex];
                
                if (!$this->isValidInput($res['json'])) {
                    $res[AppConst::errorindex_code] = 1000;
                    $res[AppConst::errorindex_message] = "Valid data not found in request: InJS:1000";
                    unset($res['json']);
                }
            } else if ($crp == Apiservice::requestModeString) {
                $jsonStr = $req[Apiservice::requestModeIndex];
                $res['json'] = json_decode($jsonStr, TRUE);

                if (!$this->isValidInput($res['json'])) {
                    $res[AppConst::errorindex_code] = 1001;
                    $res[AppConst::errorindex_message] = "Valid data not found in request: InSTR:1001";
                    unset($res['json']);
                }
            } else if ($crp == Apiservice::requestModeBase64) {

                $jsonStr = base64_decode($req[Apiservice::requestValueIndex]);
                $res['json'] = json_decode($jsonStr, TRUE);
                if (!$this->isValidInput($res['json'])) {
                    $res[AppConst::errorindex_code] = 1002;
                    $res[AppConst::errorindex_message] = "Valid data not found in request: InB6:1002";
                    unset($res['json']);
                }
            } else if ($crp == Apiservice::requestModeEncription) {

                $jsonStr = base64_decode($req[Apiservice::requestValueIndex]);
                $res['json'] = json_decode($jsonStr, TRUE);
                if (!$this->isValidInput($res['json'])) {
                    $res[AppConst::errorindex_code] = 1003;
                    $res[AppConst::errorindex_message] = "Valid data not found in request: InENX:1003";
                    unset($res['json']);
                }
            } else {
                $res = array();
                $res[AppConst::errorindex_code] = 1004;
                $res[AppConst::errorindex_message] = "Invalid request : UNKCD:1004";
            }
        } else {
            $res[AppConst::errorindex_code] = 999;
            $res[AppConst::errorindex_message] = "Invalid request : IFOUT:999";
        }
        return $res;
    }

    private function isValidInput($reqInputs) {
        if (isset($reqInputs) && $reqInputs != null && $reqInputs != NULL && is_array($reqInputs)) {
            return true;
        } else {
            return false;
        }
    }

}
