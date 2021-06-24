<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Customer extends MY_Controller {

	function __construct() {

		parent::__construct();
		$this->load->model('base_model');
		$this->load->helper(array('custom'));
		$this->load->library('email');
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

	

	function index($offset=0){

		

		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.css');

		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());

		$this->breadcrumb->add('Setup', base_url().'setup/index');

		$this->breadcrumb->add('Customer', base_url().'customer');

		$this->breadcrumb->add('List', base_url().'index');		

		$this->data['breadcrumb']=$this->breadcrumb->output();

		$this->data['main_title']='List Customer';

		

		$where = array('company_division'=>$this->data['division']);

		

		$this->load->library('pagination');

		$config['base_url'] = base_url().'/customer/index';

		$config['total_rows'] = $this->base_model->get_count('tbl_company_master',$where,'','');

		$config['per_page'] = $data['per_page'] = 50;

		$choice = $config['total_rows']/$config['per_page'];

		$config['num_links'] = 10;

		$config['use_page_numbers'] = TRUE;

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

		$data['result']=$this->base_model->get_paged_list('tbl_company_master','','','company_name asc',$config['per_page'],$offset);

		$data['search'] = '';

		if($this->input->get_post('search')){

			$config['suffix'] = '?'.http_build_query($_REQUEST, '', "&");

			$search = $this->base_model->search_handler($this->input->get_post('search', TRUE));

			$config['total_rows'] = $this->base_model->get_count('tbl_company_master','',array('company_name'),$search);

			$data['search']=$search;

			$this->db->select('*');

			$this->db->from('tbl_company_master');

			$this->db->where($where);

			$this->db->like('company_name',$search);

			$this->db->limit($config['per_page'],$offset);

			$data['result'] = $this->db->get()->result();

			

		}

		$this->pagination->initialize($config);

		$data['pagination'] = $this->pagination->create_links();

		$this->template->view('customer/list',$this->data,$data);

	}

	function view($id=""){

		

		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.css');

		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());

		$this->breadcrumb->add('Customer', base_url().'customer/index');

		$this->breadcrumb->add('List', base_url().'index');		

		$this->data['breadcrumb']=$this->breadcrumb->output();

		$this->data['main_title']='List Customer';

		

		$where = array('company_division'=>$this->data['division'],'company_code'=>$id);

		

		$this->db->select('company_code');

		$this->db->from('tbl_company_master');

		$this->db->where($where);

		$q = $this->db->get();

		$get_count = $q->num_rows();

		

		$this->load->library('pagination');

		$config['base_url'] = base_url().'/customer/index';

		$config['total_rows'] = $get_count;

		$config['per_page'] = 1;

		$data['per_page'] = 0;

		$choice = $config['total_rows']/$config['per_page'];

		$config['num_links'] = round($choice);

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

		$offset = 0;

	    

		$this->db->start_cache();

		$this->db->select('*');

		$this->db->from('tbl_company_master');

		$this->db->where($where);

		$this->db->limit($config['per_page'],$offset);

		$data['result'] =  $this->db->get()->result();

		$this->db->flush_cache();	

		$this->db->stop_cache();

		$data['pagination'] = $this->pagination->create_links();

		$data['search'] = '';

		$this->template->view('customer/list',$this->data,$data);

		//$this->output->enable_profiler(TRUE);

	}

	

	function mail_password($id=''){

		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());

		$this->breadcrumb->add('Setup', base_url().'setup/index');

		$this->breadcrumb->add('Customer', base_url().'customer');

		$this->breadcrumb->add('Mail Password', base_url().'index');		

		$this->data['breadcrumb']=$this->breadcrumb->output();

		$this->data['main_title']='Mail Password';

		

		$data['edit']=$this->base_model->edit('tbl_company_master',array('company_id'=>$id));

		

		$config_validation=array(

			array('field' => 'company_username','label' => 'Username','rules' => 'trim|required|xss_clean'),

			array('field' => 'company_email','label' => 'Email Id','rules' => 'trim|required|valid_email|xss_clean'));

		

		$this->form_validation->set_rules($config_validation);

		if ($this->form_validation->run() == FALSE) {

			

			$this->template->view('customer/mail_password',$this->data,$data);

			

		}else {

			$newpassword=create_password(12,true, true, false);

			$data = array(

				'company_password' =>$newpassword,

			);

			$this->base_model->update('tbl_company_master',$data,array('company_id'=>$id));

			$company=$this->base_model->edit('tbl_company_master',array('company_id'=>$id));

			

			$this->email->initialize(array(

				'protocol' => 'sendmail',

				'mailtype' => 'html',

				'crlf' => "\r\n",

				'newline' => "\r\n"

			));

			$data['key_logo']=$this->data['company-logo'];

			$data['key_username']=$company[0]->company_username;

			$data['key_password']=$newpassword;

			$data['key_email']=$company[0]->company_email;

			$this->email->from($this->data['noreply-email'], $this->data['company-name']);

			$this->email->to($company[0]->company_email);

			$message = $this->load->view('customer/mail_password_tpl',array('data'=>$data),TRUE);

			$this->email->subject('Customer - Resetted password');

			$this->email->message($message);

			$this->email->send();

			$this->session->set_flashdata('success', 'Password mailed successfully..!'); 

			redirect(base_url().'customer/mail-password/'.$id);

		}

	}

	

	function create(){

		$this->load->library('upload');
		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.min.css');
		$this->template->addCss(base_url().'assets/dist/css/mystyle.css');
		$this->template->addCss(base_url().'assets/dist/css/passwordValidation.css');
		$this->template->addJs(base_url().'assets/plugins/select2/select2.full.min.js');
		$this->template->addJs(base_url().'assets/dist/js/customer.js');
		//$this->template->addJs(base_url().'assets/dist/js/newValidation.js');
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Setup', base_url().'setup');
		$this->breadcrumb->add('Customer', base_url().'customer');
		$this->breadcrumb->add('Create', base_url().'create');		
		$this->data['breadcrumb']=$this->breadcrumb->output();   
		$this->data['main_title']='Customer Entry';

		$config_validation = array(                              

			array('field' => 'company_code','label' => 'company code','rules' => 'trim|required|xss_clean|callback_check_company_code'),

			array('field' => 'company_username','label' => 'Username','rules' => 'trim|required|xss_clean|callback_check_username'),

			array('field' => 'company_password','label' => 'Password','rules' => 'trim|required|xss_clean'),

			array('field' => 'confirm_password','label' => 'confirm Password','rules' => 'trim|required|xss_clean'),

			array('field' => 'company_name','label' => 'Company Name','rules' => 'trim|required|xss_clean'),

			array('field' => 'company_email','label' => 'Email','rules' => 'trim|required|xss_clean|valid_email'),

			array('field' => 'contact_person','label' => 'Contact Person','rules' => 'trim|required|xss_clean'),

			array('field' => 'company_address','label' => 'Address','rules' => 'trim|required|xss_clean'),

			array('field' => 'company_contact','label' => 'Contact number','rules' => 'trim|required|xss_clean'),

			array('field' => 'company_city','label' => 'City','rules' => 'trim|required|xss_clean'),

			array('field' => 'company_country','label' => 'Country','rules' => 'trim|required|xss_clean'),

			array('field' => 'company_status','label' => 'Status','rules' => 'trim|required|xss_clean'));

	$this->form_validation->set_rules($config_validation);

		

if ($this->form_validation->run() == FALSE) {

	$data['country_master']=$this->base_model->get('tbl_country');

	$country=$this->base_model->edit('tbl_country',array('country'=>$this->input->post('company_country')));

	$data['city_master']=$this->base_model->edit('tbl_city',array('country_code'=>$country[0]->iso));

	$data['location_master']='';

	$this->template->view('customer/form',$this->data,$data);

} else {



$data['country_master']=$this->base_model->get('tbl_country');

		                       

$data['countryid'] = $this->base_model->get_fields('tbl_country',array('iso'),array('country' => $this->input->post('company_country')));

		                                    

$data['city_master']=$this->base_model->get_fields('tbl_city',array('city_name'),array('country_code'=>$data['countryid'][0]->iso));

                 

$data['citycode'] = $this->base_model->get_fields('tbl_location',array('city_code'),array('city_code' => $this->input->post('company_city')));

		                  

$data['location_master']=$this->base_model->get_fields('tbl_location',array('location_name'),array('city_code'=>$data['citycode'][0]->city_code));	

		

//echo $this->input->post('company_password').'---'.$this->input->post('confirm_password'); exit;

	                      

if($this->input->post('company_password') != $this->input->post('confirm_password')){

	$this->session->set_flashdata('error', 'Password And Confirm Password Should Be Same!!!');

	$this->template->view('customer/form',$this->data,$data);

	return false;                       

}                                             

			 

			$data = array(

				'company_code' => $this->input->post('company_code'),
				'company_username' => $this->input->post('company_username'),
				'company_password' => md5($this->input->post('company_password')),
				'company_name' => $this->input->post('company_name'),
				'company_email' => $this->input->post('company_email'),
				'contact_person' => $this->input->post('contact_person'),
				'company_address' => $this->input->post('company_address'),
				'company_contact' => $this->input->post('company_contact'),
				'company_mobile' => $this->input->post('company_mobile'),
				'company_location' => $this->input->post('company_location'),
				'company_city' => $this->input->post('company_city'),
				'company_country' => $this->input->post('company_country'),
				'company_division' => 1,
				'send_daily_report' => $this->input->post('send_daily_report'),
				'send_sms_notification' => $this->input->post('send_sms_notification'),
				'trn_number' => $this->input->post('trn_number'),
				'tax_inclusive' => $this->input->post('tax_inclusive'),   
				'rto_charge' => $this->input->post('rto_charge'),      
				'cod_processing_fee' => $this->input->post('cod_processing_fee'),
				'bank_name' => $this->input->post('bank_name'),
				'bank_account_no' => $this->input->post('bank_account_no'),
				'remote_area_charge' => $this->input->post('remote_area_charge'),
				'max_credit_limit' => $this->input->post('max_credit_limit'),
				'domestic_rate_type'=> $this->input->post('domestic_rate_type'),
				'domestic_rate'=> $this->input->post('domestic_rate'),
				'domestic_baseweight'=> $this->input->post('domestic_baseweight'),
				'domestic_additional_rate'=> $this->input->post('domestic_additional_rate'),
				'remote_rate'=> $this->input->post('remote_rate'),
				'remote_base_weight'=> $this->input->post('remote_base_weight'),
				'remote_additional_rate'=> $this->input->post('remote_additional_rate'),
				'company_status' => $this->input->post('company_status'),
				'display_layout' => $this->input->post('display_layout'),
				'display_skin' => $this->input->post('display_skin'),
				'register_date' => date('Y-m-d'),
				'enable_awb' => $this->input->post('enable_awb'),
				'enable_customer_logo' => $this->input->post('enable_customer_logo'),
				'awb_prefix'	=> $this->input->post('awb_prefix'),
				'awb_starting_number'	=> $this->input->post('awb_starting_number'),
				'label_logo_size' 		=> $this->input->post('label_logo_size'),
				'awb_logo_awb_size' 	=> $this->input->post('awb_logo_awb_size'),
			);

			$this->base_model->add('tbl_company_master',$data);

			$lastid = $this->db->insert_id();

			if (isset($_FILES['company_logo']) && !empty($_FILES['company_logo']['name'])){
			   
				$config = array(
					'upload_path' 	=> $this->data['customer-upload-path'],
					'allowed_types' => "gif|jpg|png",
					'overwrite' 	=> FALSE,
					'max_size' 		=> "2048000", 
					'remove_spaces' => TRUE,

					'max_width'		=> "300",
					'max_height'	=> "200",
					'file_name' 	=> 'customer_logo_'.date('Ymdhis')
				);
				$this->upload->initialize($config);
				if($this->upload->do_upload('company_logo')){
					$image = $this->upload->data(); 
					$data = array(
						'company_logo' => $image['file_name']
					);
					$this->base_model->update('tbl_company_master',$data,array('company_id'=>$lastid));
				}
			}
			if (isset($_FILES['company_awb_logo']) && !empty($_FILES['company_awb_logo']['name'])){
			    	
				$config = array(
					'upload_path' 	=> $this->data['customer-upload-path'],
					'allowed_types' => "gif|jpg|png",
					'overwrite' 	=> FALSE,
					'max_size' 		=> "2048000", 
					'remove_spaces' => TRUE,
					'max_width'		=> "300",
					'max_height'	=> "200",
					'file_name' 	=> 'customer_awb_logo_'.date('Ymdhis')
				);
				$this->upload->initialize($config);
				if($this->upload->do_upload('company_awb_logo')){
					$image = $this->upload->data(); 
					$data = array(
						'company_awb_logo' => $image['file_name']
					);
					$this->base_model->update('tbl_company_master',$data,array('company_id'=>$lastid));
				}
			}

			$this->session->set_flashdata('success', 'Customer created sucessfully..!'); 
			$this->session->set_flashdata('upload_error', $this->upload->display_errors());
			$this->session->set_flashdata('upload_error_data','('.$config['max_width'].'X'.$config['max_height'].')'); 
			 
			redirect(base_url().'customer/create');

		}

	}

	

	public function edit($id=''){
		$this->load->library('upload');

		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.min.css');

		$this->template->addCss(base_url().'assets/plugins/iCheck/all.css');

		$this->template->addJs(base_url().'assets/plugins/select2/select2.full.min.js');

		$this->template->addJs(base_url().'assets/dist/js/customer.js');

		

		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());

		$this->breadcrumb->add('Setup', base_url().'setup/index');

		$this->breadcrumb->add('Customer', base_url().'customer');

		$this->breadcrumb->add('Edit', base_url().'edit');		

		$this->data['breadcrumb']=$this->breadcrumb->output();

		$this->data['main_title']='Customer Entry';

		

		$where = array('company_division_id'=>$this->data['division']);

		

		$config_validation=array(

			array('field' => 'company_code','label' => 'Comany Code','rules' => 'trim|required|xss_clean'),

			array('field' => 'company_username','label' => 'Username','rules' => 'trim|required|xss_clean|callback_check_username'),

			array('field' => 'company_name','label' => 'Company Name','rules' => 'trim|required|xss_clean'),

			array('field' => 'company_email','label' => 'Email','rules' => 'trim|required|xss_clean|valid_email'),

			array('field' => 'contact_person','label' => 'Contact Person','rules' => 'trim|required|xss_clean'),

			array('field' => 'company_address','label' => 'Address','rules' => 'trim|required|xss_clean'),

			array('field' => 'company_contact','label' => 'Contact number','rules' => 'trim|required|xss_clean'),

			array('field' => 'company_city','label' => 'City','rules' => 'trim|required|xss_clean'),

			array('field' => 'company_country','label' => 'Country','rules' => 'trim|required|xss_clean'),

			array('field' => 'company_status','label' => 'Status','rules' => 'trim|required|xss_clean'));

		

		$this->form_validation->set_rules($config_validation);

		if ($this->form_validation->run() == FALSE) {

			$data['edit']=$this->base_model->edit('tbl_company_master',array('company_id'=>$id));

			$data['location_master']=$this->base_model->get('tbl_location');

			$data['country_master']=$this->base_model->get('tbl_country');

			$company=$this->base_model->edit('tbl_company_master',array('company_id'=>$id));

			$country=$this->base_model->edit('tbl_country',array('country'=>$company[0]->company_country));

			$citys=$this->base_model->edit('tbl_city',array('city_name'=>$company[0]->company_city));

			$data['city_master']=$this->base_model->edit('tbl_city',array('country_code'=>$country[0]->iso));

			$data['location_master']=$this->base_model->edit('tbl_location',array('city_code'=>$citys[0]->city_code));

			$this->template->view('customer/form',$this->data,$data);

		} else {                              

			$data = array(                         

				'company_code' => $this->input->post('company_code'),

				'company_username' => $this->input->post('company_username'),

				'company_name' => $this->input->post('company_name'),

				'company_email' => $this->input->post('company_email'),

				'contact_person' => $this->input->post('contact_person'),

				'company_address' => $this->input->post('company_address'),

				'company_contact' => $this->input->post('company_contact'),  

				'company_mobile' => $this->input->post('company_mobile'),        

				'company_location' => $this->input->post('company_location'),

				'company_city' => $this->input->post('company_city'),

				'company_country' => $this->input->post('company_country'),

				'company_division' => 1,

				'send_daily_report' => $this->input->post('send_daily_report'),

				'send_sms_notification' => $this->input->post('send_sms_notification'),

				'trn_number' => $this->input->post('trn_number'),

				'tax_inclusive' => $this->input->post('tax_inclusive'),

				'rto_charge' => $this->input->post('rto_charge'),

				'cod_processing_fee' => $this->input->post('cod_processing_fee'),

				'bank_name' => $this->input->post('bank_name'),

				'bank_account_no' => $this->input->post('bank_account_no'),

				'remote_area_charge' => $this->input->post('remote_area_charge'),

				'max_credit_limit' => $this->input->post('max_credit_limit'),

				'domestic_rate_type'=> $this->input->post('domestic_rate_type'),

				'domestic_rate'=> $this->input->post('domestic_rate'),

				'domestic_baseweight'=> $this->input->post('domestic_baseweight'),

				'domestic_additional_rate'=> $this->input->post('domestic_additional_rate'),

				'remote_rate'=> $this->input->post('remote_rate'),

				'remote_base_weight'=> $this->input->post('remote_base_weight'),

				'remote_additional_rate'=> $this->input->post('remote_additional_rate'),

				'company_status' => $this->input->post('company_status'),

				'display_layout' => $this->input->post('display_layout'),

				'display_skin' => $this->input->post('display_skin'),
				'enable_awb' => $this->input->post('enable_awb'),
				'enable_customer_logo' => $this->input->post('enable_customer_logo'),
				'awb_prefix'	=> $this->input->post('awb_prefix'),
				'awb_starting_number'	=> $this->input->post('awb_starting_number'),
				'label_logo_size' 		=> $this->input->post('label_logo_size'),
				'awb_logo_awb_size' 	=> $this->input->post('awb_logo_awb_size'),
			);

			$this->base_model->update('tbl_company_master',$data,array('company_id'=>$id));

			if (isset($_FILES['company_logo']) && !empty($_FILES['company_logo']['name'])){
			   
				$config = array(
					'upload_path' 	=> $this->data['customer-upload-path'],
					'allowed_types' => "gif|jpg|png",
					'overwrite' 	=> TRUE,
					'max_size' 		=> "2048000", 
					'remove_spaces' => TRUE,
					'max_width'		=> "300",
					'max_height'	=> "200",
					'file_name' 	=> 'customer_logo_'.date('Ymdhis')
				);
				$this->upload->initialize($config);
				if($this->upload->do_upload('company_logo')){
					$image = $this->upload->data(); 
					$data = array(
						'company_logo' => $image['file_name']
					);
					$this->base_model->update('tbl_company_master',$data,array('company_id'=>$id));
				}
			}
			if (isset($_FILES['company_awb_logo']) && !empty($_FILES['company_awb_logo']['name'])){
			    	
				$config = array(
					'upload_path' 	=> $this->data['customer-upload-path'],
					'allowed_types' => "gif|jpg|png",
					'overwrite' 	=> FALSE,
					'max_size' 		=> "2048000", 
					'remove_spaces' => TRUE,
					'max_width'		=> "300",
					'max_height'	=> "200",
					'file_name' 	=> 'customer_awb_logo_'.date('Ymdhis')
				);
				$this->upload->initialize($config);
				if($this->upload->do_upload('company_awb_logo')){
					$image = $this->upload->data(); 
					$data = array(
						'company_awb_logo' => $image['file_name']
					);
					$this->base_model->update('tbl_company_master',$data,array('company_id'=>$id));
				}
			}
			$this->session->set_flashdata('success', 'Customer updated sucessfully..!');
			$this->session->set_flashdata('upload_error', $this->upload->display_errors());
			$this->session->set_flashdata('upload_error_data','('.$config['max_width'].'X'.$config['max_height'].')');
			redirect(base_url().'customer/edit/'.$id);

		}

	}

	

	public function delete($id=''){

		if(!empty($id)){

			$this->base_model->delete('tbl_company_master',array('company_id'=>$id));

			echo 'Success';

		}

		else

			echo 'Failure';

	}

	

	public function check_username($username){

		$this->db->select('*');

		$this->db->where('company_username',$username);

		$id= $this->uri->segment(3);

		if(!empty($id))

			$this->db->where('company_id !=',$this->uri->segment(3));

		$this->db->from('tbl_company_master');

		$q = $this->db->get();     

		                               

		if ($q->num_rows()>0){             

			$this->form_validation->set_message('check_username', 'The %s already exist');

			return FALSE;                        

		} else {

			return TRUE;

		}

	}

	

	public function check_company_code($company_code){

		$this->db->select('*');

		$this->db->where('company_code',$company_code);

		$id= $this->uri->segment(3);

		if(!empty($id))

			$this->db->where('company_id !=',$this->uri->segment(3));

		$this->db->from('tbl_company_master');

		$q = $this->db->get();

		

		if ($q->num_rows()>0){

			$this->form_validation->set_message('check_company_code', 'already exist');

			return FALSE;

		} else {

			return TRUE;

		}

	}

	

	

	public function change_password($id = '')

	{

		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());

		$this->breadcrumb->add('Setup', base_url().'setup/index');

		$this->breadcrumb->add('Customer', base_url().'customer');

		$this->breadcrumb->add('Change Password', base_url().'customer/change_password');		

		$this->data['breadcrumb']=$this->breadcrumb->output();

		$this->data['main_title']='Customer Password';

		

		//$this->template->addCss(base_url().'assets/dist/css/passwordValidation.css');

		//$this->template->addJs(base_url().'assets/dist/js/newValidation.js');

		

		$config_validation=array(

			array('field' => 'company_password','label' => 'Password','rules' => 'trim|required|xss_clean'),

			array('field' => 'confirm_password','label' => 'Confirm Password','rules' => 'trim|required|xss_clean'),);

		

		$this->form_validation->set_rules($config_validation);

		if ($this->form_validation->run() == FALSE) {

 		$this->template->view('customer/change_password',$this->data,$data);

              

		} else {

			

		if($this->input->post('company_password') !== $this->input->post('confirm_password')){

			$this->session->set_flashdata('error', 'New Password And Confirm Password Should Be Same!!!');                 

			$this->template->view('customer/change_password',$this->data,$data);

			return false;   

		}         

			

		$data = array('company_password' => md5($this->input->post('company_password')));

			

		$this->base_model->update('tbl_company_master',$data,array('company_id' => $id));

		$this->session->set_flashdata('success', 'Password Changed Successfully !!!'); 

		redirect(base_url().'customer/change_password');	

		//$this->output->enable_profiler(TRUE); 	

		}

	}

	

	public function get_city(){

		$data = array();

		$country=$this->base_model->edit('tbl_country',array('country'=>$this->input->post('country')));

		$this->db->where('country_code',$country[0]->iso); 

		$q = $this->db->get('tbl_city');

		if($q->num_rows() >0){

			foreach($q->result() as $row){

				$data[]=$row;

			}

			echo json_encode($data);

		}

	  //$this->output->enable_profiler(TRUE);

	}

		

	public function get_location(){

		$data = array();

		$citycode = $this->base_model->edit('tbl_city',array('city_name'=>$this->input->get_post('city')));

		$city=$this->base_model->edit('tbl_location',array('city_code'=>$citycode[0]->city_code));

		$this->db->where('city_code',$city[0]->city_code); 

		$q = $this->db->get('tbl_location');

		if($q->num_rows() >0){

			foreach($q->result() as $row){

				$data[]=$row;

			}

			echo json_encode($data);

		}

		//$this->output->enable_profiler(TRUE);

	}

}