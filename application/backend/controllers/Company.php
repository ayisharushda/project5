<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Company extends MY_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->model('base_model');
		$this->load->helper(array('custom'));
		$this->load->library('email');
		$this->load->library('pagination');
		$this->_init(); }  
	                                               
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
	
	private function pagination_config($params){
		$config['base_url'] 		= $params['base_url'];
		$config['total_rows'] 		= $params['get_count'];
		$config['per_page'] 		= $params['per_page'];
		$config['num_links'] 		= 10;             
		$config['use_page_numbers'] = TRUE;   
		$config['full_tag_open'] 	= '<!--pagination--><ul class="pagination pagination-sm no-margin pull-right">';
		$config['full_tag_close'] 	= '</ul><!--pagination-->';
		$config['first_link'] 		= '&laquo; First';
		$config['first_tag_open'] 	= '<li class="prev page">';
		$config['first_tag_close'] 	= '</li>';  
		$config['last_link'] 		= 'Last &raquo;'; 
		$config['last_tag_open'] 	= '<li class="next page">';
		$config['last_tag_close'] 	= '</li>';   
		$config['next_link'] 		= 'Next &rarr;';  
		$config['next_tag_open'] 	= '<li class="next page">';
		$config['next_tag_close'] 	= '</li>';
		$config['prev_link'] 		= '&larr; Previous';
		$config['prev_tag_open'] 	= '<li class="prev page">';
		$config['prev_tag_close'] 	= '</li>';
		$config['cur_tag_open'] 	= '<li class="active"><a href="">';
		$config['cur_tag_close'] 	= '</a></li>';
		$config['num_tag_open'] 	= '<li class="page">';
		$config['num_tag_close'] 	= '</li>';
		$config['suffix']			=  '?'.urldecode(http_build_query($_REQUEST,'', "&"));
		$config['reuse_query_string'] = TRUE;
		return $config;
	}
	
	function index($offset=0){
		
		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.css');
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Setup', base_url().'setup/index');
		$this->breadcrumb->add('Company', base_url().'company');
		$this->breadcrumb->add('List', base_url().'index');		
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='List Company';
		
		$per_page = $data['per_page'] =100;
		$offset = $offset == 0? 0: ($offset-1)*$per_page;
		
		$this->db->start_cache();
			$this->db->select('*');
			$this->db->from('tbl_company');
			$this->db->where(array('company_country_code'=>$this->data['country_code']));
		$this->db->stop_cache();
			$get_count = $this->db->get()->num_rows();
			$this->db->limit($per_page,$offset);
			$q =  $this->db->get();
			$data['result']=  $q->result();
		$this->db->flush_cache();	
		
		$params=array(
			'base_url' 	=> base_url().'company/index',
			'get_count' => $get_count,
			'per_page' 	=> $per_page,
			'suffix'	=> '?'.urldecode(http_build_query($_REQUEST,'', "&"))
		);
		$config = $this->pagination_config($params);
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		
	
		$this->template->view('company/list',$this->data,$data);
	}
	
	function create(){
		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.min.css');
		$this->template->addCss(base_url().'assets/dist/css/passwordValidation.css');
		$this->template->addJs(base_url().'assets/plugins/select2/select2.full.min.js');
		$this->template->addJs(base_url().'assets/dist/js/customer.js');
		
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Setup', base_url().'setup/index');
		$this->breadcrumb->add('Company', base_url().'company');
		$this->breadcrumb->add('Create', base_url().'create');		
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='Configure Company Details';
		
                          
		$config_validation = array(                              
			array('field' => 'company_name','label' => 'Company Name','rules' => 'trim|required|xss_clean'),
			array('field' => 'company_address','label' => 'Address','rules' => 'trim|required|xss_clean'),
			array('field' => 'company_phone','label' => 'Contact number','rules' => 'trim|required|xss_clean'),
			array('field' => 'company_mobile','label' => 'Contact number','rules' => 'trim|xss_clean'),
			array('field' => 'company_email','label' => 'Email','rules' => 'trim|required|xss_clean|valid_email'),
			array('field' => 'company_country','label' => 'Country','rules' => 'trim|required|xss_clean'),
			array('field' => 'company_city','label' => 'City','rules' => 'trim|required|xss_clean'),
			array('field' => 'company_location','label' => 'City','rules' => 'trim|xss_clean'),
			array('field' => 'company_currency','label' => 'Currency','rules' => 'trim|required|xss_clean'),
			array('field' => 'company_complaint_email','label' => 'Complaint Email','rules' => 'trim|xss_clean'),
			array('field' => 'company_booking_email','label' => 'Booking Email','rules' => 'trim|xss_clean'),
		);
		$this->form_validation->set_rules($config_validation);
		
		if ($this->form_validation->run() == FALSE) {
			$data['edit']=$this->base_model->edit('tbl_company',array('company_id'=>$id));
			$data['currency_master']=$this->base_model->get('tbl_currencies');
			$data['city_master']=$this->base_model->get_fields('tbl_city',array('city_name'),array('country_code'=>$data['edit'][0]->company_country_code));
			$this->template->view('company/form',$this->data,$data);
		}else{
			$country_code=$this->base_model->get_fields('tbl_country',array('iso'),array('country'=>$this->input->post('company_country')));
			$data = array(                         
				'company_name' 			=> $this->input->post('company_name'),
				'company_email' 		=> $this->input->post('company_email'),
				'company_address' 		=> $this->input->post('company_address'),
				'company_phone' 		=> $this->input->post('company_phone'),  
				'company_mobile' 		=> $this->input->post('company_mobile'),    
				'company_fax' 			=> $this->input->post('company_fax'),   
				'company_location' 		=> $this->input->post('company_location'),
				'company_city' 			=> $this->input->post('company_city'),
				'company_country' 		=> $this->input->post('company_country'),
				'company_country_code' 	=> $country_code[0]->iso,
				'company_website' 		=> $this->input->post('company_website'),
				'company_currency' 		=> $this->input->post('company_currency'),
				'company_tax_number' 	=> $this->input->post('company_tax_number'),
				'company_panel_title' 	=> $this->input->post('company_panel_title'),
				'company_pickup_prefix' => $this->input->post('company_pickup_prefix'),
				'company_awb_prefix' 	=> $this->input->post('company_awb_prefix'),
				'company_complaint_email'=> $this->input->post('company_complaint_email'),
				'company_booking_email' => $this->input->post('company_booking_email'),
				'company_beneficiary_name' => $this->input->post('company_beneficiary_name'),
				'company_bank_name' => $this->input->post('company_bank_name'),
				'company_account_no' => $this->input->post('company_account_no'),
				'company_iban' => $this->input->post('company_iban'),
				'company_swift_code' => $this->input->post('company_swift_code'),
			);
			$this->base_model->add('tbl_company',$data);
			$this->session->set_flashdata('success', 'Company added sucessfully..!'); 
			redirect(base_url().'company/create');
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
		$this->breadcrumb->add('Company', base_url().'company');
		$this->breadcrumb->add('Edit', base_url().'edit');		
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='Configure Company Details';
		
		$data['country_master']=$this->base_model->get('tbl_country');
		
		
		$config_validation=array(
			array('field' => 'company_name','label' => 'Company Name','rules' => 'trim|required|xss_clean'),
			array('field' => 'company_address','label' => 'Address','rules' => 'trim|required|xss_clean'),
			array('field' => 'company_phone','label' => 'Contact number','rules' => 'trim|required|xss_clean'),
			array('field' => 'company_mobile','label' => 'Contact number','rules' => 'trim|xss_clean'),
			array('field' => 'company_email','label' => 'Email','rules' => 'trim|required|xss_clean|valid_email'),
			array('field' => 'company_country','label' => 'Country','rules' => 'trim|required|xss_clean'),
			array('field' => 'company_city','label' => 'City','rules' => 'trim|required|xss_clean'),
			array('field' => 'company_location','label' => 'City','rules' => 'trim|xss_clean'),
			array('field' => 'company_currency','label' => 'Currency','rules' => 'trim|required|xss_clean'),
			array('field' => 'company_complaint_email','label' => 'Complaint Email','rules' => 'trim|xss_clean'),
			array('field' => 'company_booking_email','label' => 'Booking Email','rules' => 'trim|xss_clean'),
			
			
		);
		$this->form_validation->set_rules($config_validation);
		if ($this->form_validation->run() == FALSE) {
			$data['edit']=$this->base_model->edit('tbl_company',array('company_id'=>$id));
			
			$data['currency_master']=$this->base_model->get('tbl_currencies');
			
			$data['city_master']=$this->base_model->get_fields('tbl_city',array('city_name'),array('country_code'=>$data['edit'][0]->company_country_code));
			$this->template->view('company/form',$this->data,$data);
		}else{
			$country_code=$this->base_model->get_fields('tbl_country',array('iso'),array('country'=>$this->input->post('company_country')));
			$data = array(                         
				'company_name' 			=> $this->input->post('company_name'),
				'company_email' 		=> $this->input->post('company_email'),
				'company_address' 		=> $this->input->post('company_address'),
				'company_phone' 		=> $this->input->post('company_phone'),  
				'company_mobile' 		=> $this->input->post('company_mobile'),    
				'company_fax' 			=> $this->input->post('company_fax'),   
				'company_location' 		=> $this->input->post('company_location'),
				'company_city' 			=> $this->input->post('company_city'),
				'company_country' 		=> $this->input->post('company_country'),
				'company_country_code' 	=> $country_code[0]->iso,
				'company_website' 		=> $this->input->post('company_website'),
				'company_currency' 		=> $this->input->post('company_currency'),
				'company_tax_number' 	=> $this->input->post('company_tax_number'),
				'company_panel_title' 	=> $this->input->post('company_panel_title'),
				'company_pickup_prefix' => $this->input->post('company_pickup_prefix'),
				'company_awb_prefix' 	=> $this->input->post('company_awb_prefix'),
				'company_noreply_email' => $this->input->post('company_noreply_email'),
				'company_complaint_email'=> $this->input->post('company_complaint_email'),
				'company_booking_email' => $this->input->post('company_booking_email'),
				'awb_logo_awb_size' 	=> $this->input->post('awb_logo_awb_size'),
				'label_logo_size' 	=> $this->input->post('label_logo_size'),
				'company_beneficiary_name' => $this->input->post('company_beneficiary_name'),
				'company_bank_name' => $this->input->post('company_bank_name'),
				'company_account_no' => $this->input->post('company_account_no'),
				'company_iban' => $this->input->post('company_iban'),
				'company_swift_code' => $this->input->post('company_swift_code'),
				
			);
			$this->base_model->update('tbl_company',$data,array('company_id'=>$id));
			$lastid = $id;
			$edit['company']=$this->base_model->edit('tbl_company',array('company_id'=>$lastid));
		
			if (isset($_FILES['company_logo']) && !empty($_FILES['company_logo']['name'])){
			    	
				$config = array(
					'upload_path' 	=> $this->data['company-logo-upload-path'],
					'allowed_types' => "gif|jpg|png",
					'overwrite' 	=> FALSE,
					'max_size' 		=> "2048000", 
					'remove_spaces' => TRUE,
					'file_name' 	=> 'logo_'.date('Y-m-d-h-i-s')
				);
				$this->upload->initialize($config);
				if($this->upload->do_upload('company_logo')){
					$image = $this->upload->data(); 
					if($edit['company'][0]->company_logo!=""){
						unlink($this->data['company-logo-upload-path'].$edit['company'][0]->company_logo);
					}
					$data = array(
						'company_logo' => $image['file_name']
					);
					$this->base_model->update('tbl_company',$data,array('company_id'=>$lastid));
				}
			}
			
			if (isset($_FILES['company_awb_logo']) && !empty($_FILES['company_awb_logo']['name'])){
			   
				$config = array(
					'upload_path' 	=> $this->data['company-logo-upload-path'],
					'allowed_types' => "gif|jpg|png",
					'overwrite' 	=> FALSE,
					'max_size' 		=> "2048000", 
					'remove_spaces' => TRUE,
					'file_name' 	=> 'awb_logo_'.date('Y-m-d-h-i-s')
				);
				$this->upload->initialize($config);
				if($this->upload->do_upload('company_awb_logo')){
					$image = $this->upload->data(); 
					if( $edit['company'][0]->company_logo!=""){
						unlink($this->data['company-logo-upload-path'].$edit['company'][0]->company_awb_logo);
					}
					$data = array(
						'company_awb_logo' => $image['file_name']
					);
					$this->base_model->update('tbl_company',$data,array('company_id'=>$lastid));
				}
			}
			
			
			$this->output->enable_profiler(TRUE);
			
			
			$this->session->set_flashdata('success', 'Company updated sucessfully..!'); 
			redirect(base_url().'company/edit/'.$id);
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