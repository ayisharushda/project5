<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Attendance extends MY_Controller {
    function __construct(){
        parent::__construct();
		$this->load->model('Base_model');
		$this->load->helper(array('custom'));
		$this->load->library('email');
		$this->load->library('pagination');
		$this->_init();
    
    }

    private function _init(){
		$this->template->addCss(base_url().'assets/bootstrap/css/bootstrap.min.css');
		$this->template->addCss('https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');  
		$this->template->addCss('https://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css');
		$this->template->addCss(base_url().'assets/plugins/select2/select2.min.css');
		$this->template->addCss(base_url().'assets/dist/css/AdminLTE.min.css');
		$this->template->addCss(base_url().'assets/dist/css/skins/_all-skins.min.css');
		$this->template->addCss(base_url().'assets/dist/css/passwordValidation.css');
		$this->template->addJs(base_url().'assets/plugins/jQuery/jQuery-2.1.3.min.js');
		$this->template->addJs(base_url().'assets/plugins/jQueryUI/jquery-ui-1.10.3.min.js');
		$this->template->addJs(base_url().'assets/bootstrap/js/bootstrap.min.js');
		$this->template->addJs(base_url().'assets/dist/js/jquery.min.js');
		$this->template->addJs(base_url().'assets/dist/js/jquery-ui.min.js');
		$this->template->addJs(base_url().'assets/plugins/slimScroll/jquery.slimscroll.min.js');
		$this->template->addJs(base_url().'assets/plugins/fastclick/fastclick.min.js');
		$this->template->addJs(base_url().'assets/plugins/input-mask/jquery.inputmask.js');
		$this->template->addJs(base_url().'assets/plugins/input-mask/jquery.inputmask.date.extensions.js');
		$this->template->addJs(base_url().'assets/plugins/input-mask/jquery.inputmask.extensions.js');
		$this->template->addJs(base_url().'assets/dist/js/site.js');
		$this->template->addJs(base_url().'assets/dist/js/app.min.js');
	}

	public function index(){
 
        $this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.css');
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Setup', base_url().'setup/index');
		$this->breadcrumb->add('Company', base_url().'company');
		$this->breadcrumb->add('List', base_url().'index');		
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='Attendance';
    	//$this->template->view('attendance/form',$this->data,$data);
	}


    public function attendence_form(){
	$this->template->addCss(base_url().'assets/dist/css/jquery-ui-timepicker-addon.css'); 
	$this->template->addJs(base_url().'assets/dist/js/jquery-ui-timepicker-addon.js');
	$this->template->addJs(base_url().'assets/dist/js/date-time.js');
	$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.css');
	$this->template->addJs(base_url().'assets/dist/js/report.js');
	$this->template->addCss('https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css');
	$this->template->addJs('https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js');
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Setup', base_url().'setup/index');
		$this->breadcrumb->add('Company', base_url().'company');
		$this->breadcrumb->add('List', base_url().'index');		
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='Attendance';
		$data['employee_name']=$this->base_model->get('tbl_employee_master');
		$config_validation = array(
			array('field'=>'employee_name','label'=>'Employee Name*','rules'=>'trim|required'),
			array('field'=>'check_in_time','label'=>'Punch Time*','rules'=>'trim|required'),
			array('field'=>'check_out_time','label'=>'Punch Time*','rules'=>'trim|required')
			
		);
		$this->form_validation->set_rules($config_validation);
        if($this->form_validation->run()==FALSE){
            $this->template->view('attendance/form',$this->data,$data);
        }
    	else{
			$data = array(
				'employee_name' => $this->input->post('employee_name'),
                'check_in_time' => $this->input->post('check_in_time'),
				'check_out_time' => $this->input->post('check_out_time')
            );
			$this->Base_model->add('tbl_attendance_form',$data);
			$this->session->set_flashdata('success','Data inserted successfully');
			redirect(base_url().'Attendance/attendence_form');
		}
	}
	//date('Y-m-d',strtotime($this->input->post('punch_date'))).' '.date('H:i:s',strtotime($this->input->post('punch_date')))

    public function monthly_attendance(){
        
        $this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.css');
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Setup', base_url().'setup/index');
		$this->breadcrumb->add('Company', base_url().'company');
		$this->breadcrumb->add('List', base_url().'index');		
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='Attendance';
    	$this->template->view('attendance/form1',$this->data,$data);

    }
    public function missing_attendance(){
        
        $this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.css');
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Setup', base_url().'setup/index');
		$this->breadcrumb->add('Company', base_url().'company');
		$this->breadcrumb->add('List', base_url().'index');		
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='Attendance';
    	$this->template->view('attendance/form2',$this->data,$data);

    }
    public function attendance_log(){
        
        $this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.css');
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Setup', base_url().'setup/index');
		$this->breadcrumb->add('Company', base_url().'company');
		$this->breadcrumb->add('List', base_url().'index');		
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='Attendance log';
    	$this->template->view('attendance/form3',$this->data,$data);

    }

    
}
?>
	
	