<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Employee extends MY_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->model('base_model');
		$this->load->library('upload');
		$this->_init();
	}
	
	private function _init(){      
		$this->template->addCss(base_url().'assets/bootstrap/css/bootstrap.min.css');
		$this->template->addCss('https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');
		$this->template->addCss('https://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css');
		$this->template->addCss(base_url().'assets/dist/css/AdminLTE.min.css');
		$this->template->addCss(base_url().'assets/dist/css/skins/_all-skins.min.css');
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

	function index($offset=0){
		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.css');
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Setup', base_url().'setup/index');
		$this->breadcrumb->add('Employee', base_url().'employee');
		$this->breadcrumb->add('List', base_url().'index');	
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='Employee Master';
		$this->load->library('pagination');
		$config['base_url'] = base_url().'/port/index';
		$config['total_rows'] = $this->base_model->count_all('tbl_employee_master');
		$config['per_page'] = $data['per_page'] = 50;
		$choice = $config['total_rows']/$config['per_page'];
		$config['num_links'] = 10;
		$config['use_page_numbers']  = TRUE;
		$config['full_tag_open'] = '<!--pagination--><ul class="pagination pagination-sm no-margin pull-right">';
		$config['full_tag_close'] = '</ul><!--pagination-->';
		$config['first_link'] = '&laquo; First';
		$config['first_tag_open'] = '<li class="prev page">';
		$config['first_tag_close'] = '</li>';
		$config['last_link'] = 'Last &raquo;';
		$config['last_tag_open'] = '<li class="next page">';
		$config['last_tag_close'] = '</li>';
		$config['next_link'] = 'Next &rarr;';
		$config['next_tag_open'] = '<li class="next page">';
		$config['next_tag_close'] = '</li>';
		$config['prev_link'] = '&larr; Previous';
		$config['prev_tag_open'] = '<li class="prev page">';
		$config['prev_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="active"><a href="">';
		$config['cur_tag_close'] = '</a></li>';
		$config['num_tag_open'] = '<li class="page">';
		$config['num_tag_close'] = '</li>';
		$offset = $offset == 0? 0: ($offset-1)*$config["per_page"];
		$data['result']=$this->base_model->get_paged_list('tbl_employee_master','','','employee_id asc',$config['per_page'],$offset);
		$data['search'] = '';
		if($this->input->get_post('search')){
			$config['suffix'] = '?'.http_build_query($_REQUEST, '', "&");
			$search = $this->base_model->search_handler($this->input->get_post('search', TRUE));
			$config['total_rows'] = $this->base_model->get_count('tbl_employee_master','',array('employee_name'),$search);
			$data['search']=$search;
			$data['result'] = $this->base_model->search('tbl_employee_master',array('employee_name'),$search,$config['per_page'],$offset);
		}
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$this->template->view('setup/employee/list',$this->data,$data);
	}

	function create(){
		$this->template->addCss(base_url().'assets/dist/css/jquery-ui-timepicker-addon.css'); 
		$this->template->addJs(base_url().'assets/dist/js/jquery-ui-timepicker-addon.js');
		$this->template->addJs(base_url().'assets/dist/js/date-time.js');
		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.css');
		$this->template->addJs(base_url().'assets/dist/js/report.js');
		$this->template->addCss('https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css');
		$this->template->addJs('https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js');
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Setup', base_url().'setup');
		$this->breadcrumb->add('Employee', base_url().'employee');
		$this->breadcrumb->add('Create', base_url().'create');			
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='Employee Entry';
		$data['country']=$this->base_model->get('tbl_country');
		$data['route']=$this->base_model->get('tbl_route_master');
		$data['department']=$this->base_model->get('tbl_department');
		$data['designation']=$this->base_model->get('tbl_designation');		
		$config_validation=array(
			array('field' => 'employee_code','label' => 'Employee Code','rules' => 'trim|required|xss_clean|callback_check_employee_code'),
			array('field' => 'employee_name','label' => 'Employee Name','rules' => 'trim|required|xss_clean'),
			array('field' => 'employee_contact','label' => 'Employee Contact','rules' => 'trim|required|xss_clean'),
			array('field' => 'employee_location','label' => 'Employee Location','rules' => 'trim|required|xss_clean'),
			array('field' => 'employee_country','label' => 'Employee Country','rules' => 'trim|required|xss_clean'),
			array('field' => 'employee_username','label' => 'Username','rules' => 'trim|required|xss_clean|callback_check_username'),
			array('field' => 'employee_password','label' => 'Password','rules' => 'trim|required|xss_clean'),
			array('field' => 'confirm_password','label' => 'confirm Password','rules' => 'trim|required|xss_clean'),
			array('field' => 'employee_route','label' => 'Employee Route','rules' => 'trim|required|xss_clean'),
			array('field' => 'employee_dob','label' => 'Date of Birth','rules' => 'trim|required|xss_clean'),
		    //array('field' => 'employee_marital_status ','label' => 'Marital Status','rules' => 'trim|required|xss_clean'),
			array('field' => 'employee_address','label' => 'Home Address','rules' => 'trim|required|xss_clean'),
			array('field' => 'employee_designation','label' => 'Employee Designation','rules' => 'trim|required|xss_clean'),
			array('field' => 'employee_department','label' => 'Employee Department','rules' => 'trim|required|xss_clean'),
			array('field' => 'employee_join_date','label' => 'Join Date','rules' => 'trim|required|xss_clean'),
			array('field' => 'work_start_time','label' => 'Work Start Time','rules' => 'trim|required|xss_clean'),
			array('field' => 'work_end_time','label' => 'Work End Time','rules' => 'trim|required|xss_clean'),
			array('field' => 'employee_allowance','label' => 'Allowances','rules' => 'trim|required|xss_clean'),
			array('field' => 'employee_other_allowance','label' => 'Other Allowances','rules' => 'trim|required|xss_clean'),
			array('field' => 'overtime_amount_hr','label' => 'Over Time Amount/hr','rules' => 'trim|required|xss_clean')
		);
        $this->form_validation->set_rules($config_validation);
		
		if ($this->form_validation->run() == FALSE) {
			$this->template->view('setup/employee/form',$this->data,$data);
		}else {
			
		if($this->input->post('employee_password') != $this->input->post('confirm_password')){
			$this->session->set_flashdata('error', 'Password And Confirm Password Should Be Same!!!');
			$this->template->view('setup/employee/form',$this->data,$data);
			return false;                       
		}

		if($this->input->post('employee_type'))
			$emp_type = $this->input->post('employee_type');
		else
			$emp_type = 0;
		if($this->input->post('clear_delivary'))
			$clear_delivary = $this->input->post('clear_delivary');
		
		else
			$clear_delivary = 0;

			$data = array(
				'employee_code' => $this->input->post('employee_code'),
				'employee_name' => $this->input->post('employee_name'),
				'employee_contact' => $this->input->post('employee_contact'),
				'employee_location' => $this->input->post('employee_location'),
				'employee_country' => $this->input->post('employee_country'),
				'employee_route' => $this->input->post('employee_route'),
				'employee_username' => $this->input->post('employee_username'),
				'employee_password' => $this->input->post('employee_password'),
				'employee_type'		=> $emp_type,
				'clear_pending_delivery' => $clear_delivary,
				'employee_dob'		=> $this->input->post('employee_dob'),
				'current_location_date' => date('Y-m-d'),
				'employee_marital_status' => $this->input->post('employee_marital_status'),
				'employee_address'=>$this->input->post('employee_address'),
				'employee_email'=>$this->input->post('employee_email'),
				'employee_designation'=>$this->input->post('employee_designation'),
				'employee_department'=>$this->input->post('employee_department'),
				'employee_join_date'=>date('Y-m-d',strtotime($this->input->post('employee_join_date'))),
				'work_start_time'=>date('H:i:s',strtotime($this->input->post('work_start_time'))),
				'work_end_time'=>date('H:i:s',strtotime($this->input->post('work_end_time'))),
				'employee_salary'=>$this->input->post('employee_salary'),
				'employee_allowance'=>$this->input->post('employee_allowance'),
				'employee_other_allowance'=>$this->input->post('employee_other_allowance'),
				'overtime_amount_hr'=>$this->input->post('overtime_amount_hr')
				
			);
			$this->base_model->add('tbl_employee_master',$data);
			$lastid = $this->db->insert_id(); 
			$this->load->library('upload');
			if (isset($_FILES['employee_photo']) && !empty($_FILES['employee_photo']['name'])){
			    	
				$config = array(
					'upload_path' 	=> $this->data['employee-upload-path'],
					'allowed_types' => "gif|jpg|png",
					'overwrite' 	=> FALSE,
					'max_size' 		=> "2048000", 
					'remove_spaces' => TRUE,
					'file_name' 	=> 'emp_'.date('Ymdhis')
				);
				$this->upload->initialize($config);
				if($this->upload->do_upload('employee_photo')){
					$image = $this->upload->data(); 

					$data = array(
						'employee_photo' => $image['file_name']
					);
					
					$this->base_model->update('tbl_employee_master',$data,array('employee_id'=>$lastid));
				}	
			}
			$this->session->set_flashdata('success', 'Employee inserted sucessfully..!'); 
			//redirect(base_url().'employee/create');
			redirect(base_url().'employee/edit/'.$lastid);
		}
	}
	function edit($id=""){
		$this->template->addCss(base_url().'assets/dist/css/jquery-ui-timepicker-addon.css'); 
		$this->template->addJs(base_url().'assets/dist/js/jquery-ui-timepicker-addon.js');
		$this->template->addJs(base_url().'assets/dist/js/date-time.js');
		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.css');
		$this->template->addJs(base_url().'assets/dist/js/report.js');
		$this->template->addCss('https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css');
		$this->template->addJs('https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js');
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Setup', base_url().'index');
		$this->breadcrumb->add('Employee', base_url().'employee');
		$this->breadcrumb->add('Edit', base_url().'index');		
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='Employee Entry';
		$data['country']=$this->base_model->get('tbl_country');
		$data['route']=$this->base_model->get('tbl_route_master');
		$data['department']=$this->base_model->get('tbl_department');
		$data['designation']=$this->base_model->get('tbl_designation');
		$config_validation=array(
			array('field' => 'employee_code','label' => 'Employee Code','rules' => 'trim|required|xss_clean'),
			array('field' => 'employee_name','label' => 'Employee Name','rules' => 'trim|required|xss_clean'),
			array('field' => 'employee_username','label' => 'Username','rules' => 'trim|required|xss_clean|callback_check_username'),
			array('field' => 'employee_contact','label' => 'Employee Contact','rules' => 'trim|required|xss_clean'),
			array('field' => 'employee_location','label' => 'Employee Location','rules' => 'trim|required|xss_clean'),
			array('field' => 'employee_country','label' => 'Employee Country','rules' => 'trim|required|xss_clean'),
			array('field' => 'employee_route','label' => 'Employee Route','rules' => 'trim|required|xss_clean'),
			array('field' => 'employee_dob','label' => 'Date of Birth','rules' => 'trim|required|xss_clean'),
			// array('field' => 'employee_marital_status ','label' => 'Marital Status','rules' => 'trim|required|xss_clean'),
			array('field' => 'employee_address','label' => 'Home Address','rules' => 'trim|required|xss_clean'),
			array('field' => 'employee_designation','label' => 'Employee Designation','rules' => 'trim|required|xss_clean'),
			array('field' => 'employee_department','label' => 'Employee Department','rules' => 'trim|required|xss_clean'),
			array('field' => 'employee_join_date','label' => 'Join Date','rules' => 'trim|required|xss_clean'),
			array('field' => 'work_start_time','label' => 'Work Start Time','rules' => 'trim|required|xss_clean'),
			array('field' => 'work_end_time','label' => 'Work End Time','rules' => 'trim|required|xss_clean'),
			array('field' => 'employee_allowance','label' => 'Allowances','rules' => 'trim|required|xss_clean'),
			array('field' => 'employee_other_allowance','label' => 'Other Allowances','rules' => 'trim|required|xss_clean'),
			array('field' => 'overtime_amount_hr','label' => 'Over Time Amount/hr','rules' => 'trim|required|xss_clean')
	
		);
        $this->form_validation->set_rules($config_validation);
		$data['country']=$this->base_model->get('tbl_country');

		$data['edit']=$this->base_model->edit('tbl_employee_master',array('employee_id'=>$id));
		$data['edit']['details']=$this->base_model->edit('tbl_employee_details',array('employee_code'=>$data['edit'][0]->employee_code));
		//var_dump($data['edit']); exit;
		if ($this->form_validation->run() == FALSE) {

			$this->template->view('setup/employee/form',$this->data,$data);
		}else {

			if($this->input->post('employee_type'))
				$emp_type = $this->input->post('employee_type');
			else
				$emp_type = 0;
			if($this->input->post('clear_delivary'))
				$clear_delivary = $this->input->post('clear_delivary');
			
			else
				$clear_delivary = 0;
		
			$data = '';
			$employee_join_date= $this->input->post('employee_join_date');

			$data = array(
				'employee_code' => $this->input->post('employee_code'),
				'employee_name' => $this->input->post('employee_name'),
				'employee_contact' => $this->input->post('employee_contact'),
				'employee_location' => $this->input->post('employee_location'),
				'employee_country' => $this->input->post('employee_country'),
				'employee_route' => $this->input->post('employee_route'),
				'employee_username' => $this->input->post('employee_username'),
				'employee_type'		=> $emp_type,
				'clear_pending_delivery' => $clear_delivary,
				'employee_dob'		=> $this->input->post('employee_dob'),
				'current_location_date' => date('Y-m-d'),
				'employee_marital_status' => $this->input->post('employee_marital_status'),
				'employee_address'=>$this->input->post('employee_address'),
				'employee_email'=>$this->input->post('employee_email'),
				'employee_designation'=>$this->input->post('employee_designation'),
				'employee_department'=>$this->input->post('employee_department'),
				'employee_join_date' => date('Y-m-d',strtotime($employee_join_date)),
				'work_start_time'=>date('H:i:s',strtotime($this->input->post('work_start_time'))),
				'work_end_time'=>date('H:i:s',strtotime($this->input->post('work_end_time'))),
				'employee_salary'=>$this->input->post('employee_salary'),
				'employee_allowance'=>$this->input->post('employee_allowance'),
				'employee_other_allowance'=>$this->input->post('employee_other_allowance'),
				'overtime_amount_hr'=>$this->input->post('overtime_amount_hr')
				
			);
			$this->base_model->update('tbl_employee_master',$data,array('employee_id'=>$id));
			$this->load->library('upload');
			if (isset($_FILES['employee_photo']) && !empty($_FILES['employee_photo']['name'])){
				$config = array(
					'upload_path' 	=> $this->data['employee-upload-path'],
					'allowed_types' => "gif|jpg|png",
					'overwrite' 	=> FALSE,
					'max_size' 		=> "2048000",
					'remove_spaces' => TRUE,
					'file_name' 	=> 'emp_'.date('Ymdhis')
				);

				$this->upload->initialize($config);
				if($this->upload->do_upload('employee_photo')){

					$image = $this->upload->data(); 

					if($edit['company'][0]->company_logo!=""){
						unlink($this->data['employee-upload-path'].$edit['employee_photo'][0]->company_logo);
					}
					$data = array(
						'employee_photo' => $image['file_name']
					);

					$this->base_model->update('tbl_employee_master',$data,array('employee_id'=>$id));
				}

			}
			
			$this->session->set_flashdata('success', 'Employee updated sucessfully..!'); 
			redirect(base_url().'employee/edit/'.$id);
		}
	}
	public function delete($id=''){
		if(!empty($id)){
			$this->base_model->delete('tbl_employee_master',array('employee_id'=>$id));
			echo 'Success';
		}
		else
			echo 'Failure';
	}
	
	public function check_username($username){
		$this->db->select('*');
		$this->db->where('employee_username',$username);
		$id= $this->uri->segment(3);
		if(!empty($id))
			$this->db->where('employee_id !=',$this->uri->segment(3));
		$this->db->from('tbl_employee_master');
		$q = $this->db->get();     
		                               
		if ($q->num_rows()>0){             
			$this->form_validation->set_message('check_username', 'The %s already exist');
			return FALSE;                        
		} else {
			return TRUE;
		}
	}
	
	public function check_employee_code($company_code){
		$this->db->select('*');
		$this->db->where('employee_code',$company_code);
		$id= $this->uri->segment(3);
		if(!empty($id))
			$this->db->where('employee_id !=',$this->uri->segment(3));
		$this->db->from('tbl_employee_master');
		$q = $this->db->get();
		
		if ($q->num_rows()>0){
			$this->form_validation->set_message('check_employee_code', 'already exist');
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
	public function change_password($id = '')
	{
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Setup', base_url().'setup/index');
		$this->breadcrumb->add('Employee', base_url().'employee');
		$this->breadcrumb->add('Change Password', base_url().'employee/change_password');		
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='Employee Password';
		
		$config_validation=array(
			array('field' => 'employee_password','label' => 'Password','rules' => 'trim|required|xss_clean'),
			array('field' => 'confirm_password','label' => 'Confirm Password','rules' => 'trim|required|xss_clean'),);
		
		$this->form_validation->set_rules($config_validation);
		if ($this->form_validation->run() == FALSE) {
 		$this->template->view('setup/employee/change_password',$this->data,$data);
              
		} else {
			
		if($this->input->post('employee_password') !== $this->input->post('confirm_password')){
			$this->session->set_flashdata('error', 'New Password And Confirm Password Should Be Same!!!');                 
			$this->template->view('setup/employee/change_password',$this->data,$data);
			return false;   
		}         
			
		$data = array('employee_password' => $this->input->post('employee_password'));
			
		$this->base_model->update('tbl_employee_master',$data,array('employee_id' => $id));
		$this->session->set_flashdata('success', 'Password Changed Successfully !!!'); 
		redirect(base_url().'employee/change_password');	
		//$this->output->enable_profiler(TRUE); 	
		}
	}

    public function update_employee_doc(){
		$employee = $this->base_model->get_fields('tbl_employee_master',array('employee_code'),array('employee_id' => $this->input->post('employee_id')));
		$data =array(
			'employee_code ' => $employee[0]->employee_code,
			'passport_number ' => $this->input->post('passport_number'),
			'passport_expire_date'=>date('Y-m-d',strtotime($this->input->post('passport_expire_date'))),
			'visa_number' => $this->input->post('visa_number'),
			'visa_expire_date'=>date('Y-m-d',strtotime($this->input->post('visa_expire_date'))),
			'health_card_number' => $this->input->post('health_card_number'),
			'health_card_expire_date'=>date('Y-m-d',strtotime($this->input->post('health_card_expire_date'))),
			'labour_card_number ' => $this->input->post('labour_card_number'),
			'labour_card_expire_date'=>date('Y-m-d',strtotime($this->input->post('labour_card_expire_date'))),
			'emirates_id' => $this->input->post('emirates_id'),
			'emirates_expire_date'=>date('Y-m-d',strtotime($this->input->post('emirates_expire_date'))),
			'bank_name' => $this->input->post('bank_name'),
			'bank_account_no' => $this->input->post('bank_account_no'),
			'bank_account_expire_date'=>date('Y-m-d',strtotime($this->input->post('bank_account_expire_date'))),
			'driving_licence_no' => $this->input->post('driving_licence_no'),
			'driving_licence_expire_date'=>date('Y-m-d',strtotime($this->input->post('driving_licence_expire_date')))
		);
		
		$this->db->select('employee_code');
		$this->db->from('tbl_employee_details')->where(array('employee_code'=>$employee[0]->employee_code));
		$q =  $this->db->get();
		if($q->num_rows()>0){	
			$this->base_model->update('tbl_employee_details',$data,array('employee_code'=>$employee[0]->employee_code));
			echo 'Updated';
		}else{
			$this->base_model->add('tbl_employee_details',$data);
			echo 'Inserted';
		}
		
		
		
		
		
		
	}
	
	/*  Check tbl_emplyee_details with employee_id if count >0 update else add * 
	    $this->base_model->update('tbl_employee_master',$employee[0]->employee_code,array('employee_id'=>$id));
	if(isset($data['edit'][0]->employee_id){
		$this->base_model->update('tbl_employee_details',array('employee_code'=>$data['edit'][0]->employee_code));
		$lastid = $this->db->insert_id(); 
	    echo 'Inserted';             */
	//$data['edit'][0]->employee_code=$this->base_model->edit('tbl_employee_details',array('employee_id'=>$id));
	//$this->base_model->add('tbl_employee_details',$data);
	//$lastid = $this->db->insert_id(); 
	//echo 'Inserted';
	/*if(isset($data['edit'][0]->employee_id){
		$this->base_model->update('tbl_employee_details',array('employee_code'=>$data['edit'][0]->employee_code));
	}*/	
	//isset($data['edit'][0]->employee_id) ? $data['edit'][0]->employee_id : set_value('employee_id');dffrrfr
public function do_upload(){
	$config['upload_path']="./assets/images";
	$config['allowed_types']='gif|jpg|png';
	$config['encrypt_name'] = TRUE;
	 
	$this->load->library('upload',$config);
	if($this->upload->do_upload("file")){
		$data = array(
			'passport_front_image' => $this->upload->data()
		);

		$result= $this->base_model->add('tbl_employee_details',$data);
		echo json_decode($result);
	}
}


}
?>