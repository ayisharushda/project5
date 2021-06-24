<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
                    
class Booking extends MY_Controller {
                              
	function __construct(){
		parent::__construct();
		$this->load->model('base_model');
		$this->_init();
		$this->load->library(array('smart_lib'));     
	}                            
	
	private function _init(){               
		$this->template->addCss(base_url().'assets/bootstrap/css/bootstrap.min.css');
		$this->template->addCss('https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');
		$this->template->addCss('https://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css');
		$this->template->addCss(base_url().'assets/dist/css/AdminLTE.min.css');
		$this->template->addCss(base_url().'assets/dist/css/skins/_all-skins.min.css');
		$this->template->addCss(base_url().'assets/dist/css/box.css');
		$this->template->addCss(base_url().'assets/dist/css/mystyle.css');
		$this->template->addJs(base_url().'assets/dist/js/jquery.min.js');
		$this->template->addJs(base_url().'assets/dist/js/jquery-ui.min.js');
		$this->template->addJs(base_url().'assets/bootstrap/js/bootstrap.min.js');
		$this->template->addJs(base_url().'assets/plugins/slimScroll/jquery.slimscroll.min.js');
		$this->template->addJs(base_url().'assets/plugins/fastclick/fastclick.min.js');
		$this->template->addJs(base_url().'assets/plugins/input-mask/jquery.inputmask.js');
		$this->template->addJs(base_url().'assets/plugins/input-mask/jquery.inputmask.date.extensions.js');
		$this->template->addJs(base_url().'assets/plugins/input-mask/jquery.inputmask.extensions.js');
		$this->template->addJs(base_url().'assets/dist/js/site.js');
		$this->template->addJs(base_url().'assets/dist/js/app.min.js');
		$this->template->addJs(base_url().'assets/dist/js/jquery.calculation.js');
		$this->template->addJs(base_url().'assets/dist/js/box.js');
	}
	
	function index($offset=0){

		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.css');
		$this->template->addJs(base_url().'assets/plugins/iCheck/icheck.min.js');
		$this->template->addJs(base_url().'assets/dist/js/autotab.js');
		//$this->template->addJs(base_url().'assets/dist/js/booking.min.js');
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Booking', base_url().'booking/index');
		$this->breadcrumb->add('List', base_url().'index');
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='List Consigment';
		$this->db->select('*');
		$this->db->from('tbl_status_master');
		$this->db->order_by('status_type', ASC);
		$data['status']=$this->db->get()->result();
		$this->db->select('*');
		$this->db->from('tbl_company_master');
		$data['company']=$this->db->get()->result();

		$where =array('company_division'=>$this->data['division']);
		$this->db->select('booking_id');
		$this->db->from('tbl_booking');
		$this->db->where($where);
		$q = $this->db->get();
		$get_count = $q->num_rows();

		$this->load->library('pagination');
		$config['base_url'] = base_url().'/booking/index';
		$config['total_rows'] = $get_count;
		$config['per_page'] = $data['per_page'] = 100;
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

		$fields = array('booking_id','booking_date','booking_number','batch_number','pickup_date','booking_reference','to_company','to_country','company_code','to_company','current_status','package_type','to_location','status_datetime','shipment_hold','reference_number','consignee_latitude','consignee_longitude','delivered_latitude','delivered_longitude');

		$this->db->start_cache();
		$this->db->select($fields);
		$this->db->from('tbl_booking');
		$this->db->where($where);
		
		$this->db->order_by("booking_id", "desc");
		$this->db->limit($config['per_page'],$offset);
		$data['result'] =  $this->db->get()->result();
		$this->db->flush_cache();
		$this->db->stop_cache();


		if($this->input->get('from_date') || $this->input->get('to_date') || $this->input->get('q') || $this->input->get('current_status') || $this->input->get('company_code')){

			$config['suffix'] = '?'.urldecode(http_build_query($_GET, '', "&"));
			$offset = 0;

			$this->db->select($fields);
			$this->db->from('tbl_booking');
			$this->db->where($where);
			if($this->input->get('from_date'))
				$this->db->where('DATE(booking_date) >=',date('Y-m-d',strtotime($this->input->get('from_date'))));
			if($this->input->get('to_date'))
				$this->db->where('DATE(booking_date) <=', date('Y-m-d',strtotime($this->input->get('to_date'))));
			if($this->input->get('current_status')){
				$this->db->where('current_status',$this->input->get('current_status'));
			}
			if($this->input->get('company_code')){
				$this->db->where('company_code',$this->input->get('company_code'));
			}
			if($this->input->get('q')){
				$this->db->group_start();
				$this->db->like('booking_number',$this->input->get('q'),'after');
				$this->db->or_like('booking_reference',$this->input->get('q'),'after');
				$this->db->or_like('reference_number',$this->input->get('q'),'after');
				$this->db->or_like('batch_number',$this->input->get('q'),'after');
				$this->db->group_end();
			}
			$count= count($this->db->get()->result());
			/*if($this->input->get('current_status')){
				$status_condition='';
				$status = $this->input->get('current_status');
				$get_status=$this->base_model->edit('tbl_status_master',array('status_group'=>$status,'status_type_status'=>1));
				foreach($get_status as $row){
					$status_condition[]=$row->status_type;
				}
			}*/

			$this->db->select($fields);
			$this->db->from('tbl_booking');
			$this->db->where($where);
			if($this->input->get('from_date'))
				$this->db->where('DATE(booking_date) >=',date('Y-m-d',strtotime($this->input->get('from_date'))));
			if($this->input->get('to_date'))
				$this->db->where('DATE(booking_date) <=', date('Y-m-d',strtotime($this->input->get('to_date'))));
			if($this->input->get('current_status')){
				$this->db->where('current_status',$this->input->get('current_status'));
			}
			if($this->input->get('company_code')){
				$this->db->where('company_code',$this->input->get('company_code'));
			}
			if($this->input->get('q')){
				$this->db->group_start();
				$this->db->like('booking_number',$this->input->get('q'),'after');
				$this->db->or_like('booking_reference',$this->input->get('q'),'after');
				$this->db->or_like('reference_number',$this->input->get('q'),'after');
				$this->db->or_like('batch_number',$this->input->get('q'),'after');
				$this->db->group_end();
			}
			$this->db->limit($config['per_page'],$offset);
			$data['result'] = $this->db->get()->result();
			$config['total_rows']=$count;
			$data['edit']=array(
				'from_date'=>$this->input->get('from_date'),
				'to_date'=>$this->input->get('to_date'),
				'q'=>$this->input->get('q'),
				'current_status'=>$this->input->get('current_status'),
				'company_code'=>$this->input->get('company_code'),
			);

			$this->pagination->initialize($config);
			$data['pagination'] = $this->pagination->create_links();
			$this->template->view('booking/list',$this->data,$data);

		} else {
			$this->pagination->initialize($config);
			$data['pagination'] = $this->pagination->create_links();
			$this->template->view('booking/list',$this->data,$data);
		}
		//$this->output->enable_profiler(TRUE);
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
		$config['reuse_query_string'] = TRUE;
		return $config;
	}

	function consignment_searching($offset=0){

		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.css');
		// $this->template->addJs(base_url().'assets/plugins/iCheck/icheck.min.js');
		// $this->template->addJs(base_url().'assets/dist/js/autotab.js');
		//$this->template->addJs(base_url().'assets/dist/js/booking.min.js');
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Booking', base_url().'booking/consignment_searching');
		$this->breadcrumb->add('List', base_url().'consignment_searching');
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='Consigment Searching';
		$this->db->select('*');
		$this->db->from('tbl_status_master');
		$data['status']=$this->db->get()->result();
		$this->db->select('*');
		$this->db->from('tbl_company_master');
		$data['company']=$this->db->get()->result();
		$data['status']=$this->base_model->get('tbl_status_master');

		$where =array('company_division'=>$this->data['division']);

		$this->load->library('pagination');

		$per_page = $data['per_page'] =100;

		$offset = $offset == 0? 0: ($offset-1)*$per_page;

		$fields = array('booking_id','booking_date','booking_number','batch_number','pickup_date','booking_reference','to_company','to_country','company_code','to_mobileno','to_contactno','to_cperson','current_status','package_type','to_city','reference_number','consignee_latitude','consignee_longitude','distance_kms','weight','pieces','manifest_number','to_address','to_city','to_location','shipment_hold','outemployee_name');
		$date = date('Y-m-d');

		$this->db->start_cache();

		$this->db->select($fields);

		$this->db->from('tbl_booking');

			if($this->input->get('from_date'))
				$this->db->where('DATE(booking_date) >=',date('Y-m-d',strtotime($this->input->get('from_date'))));
			else
				$this->db->where('DATE(booking_date) >=',date('Y-m-d',strtotime($date.' -1 day')));
			if($this->input->get('to_date'))
				$this->db->where('DATE(booking_date) <=', date('Y-m-d',strtotime($this->input->get('to_date'))));
			else
				$this->db->where('DATE(booking_date) <=',date('Y-m-d'));

			if($this->input->get('manifest_number')){
				$this->db->group_start();
				$this->db->like('booking_number',$this->input->get('manifest_number'),'after');
				$this->db->or_like('manifest_number',$this->input->get('manifest_number'),'after');
				$this->db->group_end();
			}

			if($this->input->get_post('hold_value')){

				$hold_value =  $this->input->get_post('hold_value');

				if($hold_value==1)

					$this->db->where('shipment_hold', 1);
				if($hold_value==2)
					$this->db->where('shipment_hold', 0);
			}

			if($this->input->get_post('current_status'))

				$this->db->where('current_status',$this->input->get_post('current_status'));
			

			if($this->input->get('mobile_num')){
				$this->db->group_start();
				$this->db->like('to_contactno',$this->input->get('mobile_num'),'after');
				$this->db->or_like('to_mobileno',$this->input->get('mobile_num'),'after');
				$this->db->or_like('to_cperson',$this->input->get('mobile_num'),'after');
				$this->db->group_end();
			}

			if($this->input->get('deli_lat_long')){
				$deli_lat_long =  $this->input->get('deli_lat_long');
			
				if($deli_lat_long==1){

					$this->db->group_start();
						$this->db->where('consignee_latitude >=',22,'after');
						$this->db->where('consignee_latitude <=',27,'after');
						$this->db->where('consignee_longitude >=',55,'after');
						$this->db->where('consignee_longitude <=',57,'after');
					$this->db->group_end();

				}
				if($deli_lat_long==2){
					$this->db->group_start();
						$this->db->where('consignee_latitude <=',22,'after');
						$this->db->or_where('consignee_latitude >=',27,'after');
						$this->db->or_where('consignee_longitude <=',55,'after');
						$this->db->or_where('consignee_longitude >=',57,'after');
						$this->db->or_where('consignee_latitude =',NULL,'after');
						$this->db->or_where('consignee_latitude =','','after');
						$this->db->or_where('consignee_longitude =',NULL,'after');
						$this->db->or_where('consignee_longitude =','','after');
					$this->db->group_end();

				}
			}
			
		
		$this->db->stop_cache();

		$get_count = $this->db->get()->num_rows();

		$this->db->order_by("booking_date", "desc");

		$this->db->limit($per_page,$offset);

		$q =  $this->db->get();

		$data['result']=  $q->result();

		$this->db->flush_cache();

		$params=array(

			'base_url' 	=> base_url().'booking/consignment_searching',

			'get_count' => $get_count,

			'per_page' 	=> $per_page,

			'suffix'	=> '?'.urldecode(http_build_query($_REQUEST,'', "&"))

		);
		$config = $this->pagination_config($params);	

		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();

		$data['edit']=array(
			'from_date'=>$this->input->get('from_date'),
			'to_date'=>$this->input->get('to_date'),
			'mobile_num'=>$this->input->get('mobile_num'),
			'manifest_number'=>$this->input->get('manifest_number'),
			'hold_value'=>$hold_value,
			'deli_lat_long' =>$deli_lat_long,
			'current_status'=>$this->input->get('current_status'),
		);
		$this->template->view('booking/consign_list',$this->data,$data);
		// $this->output->enable_profiler(TRUE);

	}
    
	function create(){
			$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.min.css');
			$this->template->addCss(base_url().'assets/dist/css/booking.css');
			$this->template->addJs(base_url().'assets/dist/js/autotab.js');
			$this->template->addJs(base_url().'assets/dist/js/booking.min.js');
			$this->template->addJs(base_url().'assets/dist/js/inscan.js');
			$this->template->addJs(base_url().'assets/dist/js/booking-select.js');
			$this->template->addhJs(base_url().'assets/dist/js/editable-main.js');
			$this->template->addhJs(base_url().'assets/dist/js/jquery-editable-select.js');
			$this->template->addCss(base_url().'assets/dist/css/jquery-editable-select.min.css');
			$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
			$this->breadcrumb->add('Booking', base_url().'booking/index');
			$this->breadcrumb->add('Create', base_url().'booking/create');
			$this->data['breadcrumb']=$this->breadcrumb->output();
			$this->data['main_title']='Consignment Entry';

			$config_validation = array(
				array('field' => 'service_type','label' => 'Service Type','rules' => 'trim|required|xss_clean'),
				array('field' => 'booking_number','label' => 'Booking Number','rules' => 'trim|required|xss_clean'),
				array('field' => 'booking_date','label' => 'Booking date','rules' => 'trim|required|xss_clean'),
				array('field' => 'pickup_time','label' => 'Pickup Time','rules' => 'trim|required|xss_clean'),
				array('field' => 'from_company','label' => 'From Company','rules' => 'trim|required|xss_clean'),
				array('field' => 'from_address','label' => 'From Address','rules' => 'trim|required|xss_clean'),
				array('field' => 'from_location','label' => 'From Location','rules' => 'trim|required|xss_clean'),
				array('field' => 'from_country','label' => 'From Country','rules' => 'trim|required|xss_clean'),
				array('field' => 'from_cperson','label' => 'From Cperson','rules' => 'trim|required|xss_clean'),
				array('field' => 'from_contactno','label' => 'From Contactno','rules' => 'trim|required|xss_clean'),
				array('field' => 'to_company','label' => 'To Company','rules' => 'trim|required|xss_clean'),
				array('field' => 'to_address','label' => 'To Address','rules' => 'trim|required|xss_clean'),
				array('field' => 'to_location','label' => 'To Location','rules' => 'trim|required|xss_clean'),
				array('field' => 'to_country','label' => 'To Country','rules' => 'trim|required|xss_clean'),
				array('field' => 'to_cperson','label' => 'To Contact Person','rules' => 'trim|required|xss_clean'),        
				array('field' => 'to_contactno','label' => 'To Contactno','rules' => 'trim|required|xss_clean'),
				array('field' => 'collected_by','label' => 'Collected By','rules' => 'trim|required|xss_clean'),
				array('field' => 'pieces','label' => 'Pieces','rules' => 'trim|required|xss_clean'),
			);
		
			$currency 		= $this->data['base_company'][0]->company_currency;
			$from_country 	= $this->data['base_company'][0]->company_country;
			$to_country 	= $this->data['base_company'][0]->company_country;
		
				                    
			$this->form_validation->set_rules($config_validation);  
		                                                        
			$data['package'] = $this->base_model->get('tbl_package_type');
			$data['currency'] = $this->base_model->edit('tbl_currencies',array('currency_status'=>'1')); 
		    $data['status'] = $this->base_model->get('tbl_status_master');
		    
		    $data['edit'][0]->currency_code = $currency;
		

		    $data['employee_master'] = $this->base_model->get_fields('tbl_employee_master',array('employee_code','employee_name'),array('employee_status'=>1));
		                                                                   
			$compstatus = array('company_status' => '1');                    
			$data['companymain']= $this->base_model->edit('tbl_company_master', $compstatus);
                                                                             
			$get_bnumber=$this->base_model->edit('tbl_settings',array('settings_label'=>'awb-end'));
			$awb_number=$this->data['awb-prefix'].($get_bnumber[0]->settings_value+1);

			$data['country'] = $this->base_model->get('tbl_country');

			$customer_country = $this->base_model->edit('tbl_country',array('country'=>$data['companymain'][0]->company_country));

			$data['tocity'] = array();
		
			if ($this->form_validation->run() == FALSE) {
            $data['edit'][0]->booking_number = $awb_number;
            $country = $this->base_model->edit('tbl_country',array('country'=>$this->input->post('to_country')));

			$data['tocity'] = $this->base_model->edit('tbl_city',array('country_code'=>$country[0]->iso));

			$country = $this->base_model->edit('tbl_country',array('country'=>$this->input->post('from_country')));

			$data['fromcity'] = $this->base_model->edit('tbl_city',array('country_code'=>$country[0]->iso));
			
			$this->template->view('booking/form',$this->data,$data);
			} else {
                
				if($this->input->post('book_type') == 1) {
					$get_bnumber = $this->base_model->edit('tbl_settings',array('settings_label'=>'awb-end'));
					$awb_number = $this->data['awb-prefix'].($get_bnumber[0]->settings_value+1);
				} else {
					$awb_number = $this->input->post('booking_number');
				}
				                   
				if($this->input->post('service_type') == 1)
					$service_type='CN';                      
				else                                                                       
					$service_type='ME'; 
				
				$package_type=$this->base_model->edit('tbl_package_type',array('package_type'=>$this->input->post('package_type'),'package_status'=>1));
				
				$ncnd = $this->input->post('ncnd_amount');      
				if(empty($ncnd) || $ncnd==0){                       
					$return_service_ncnd=0;                                    
					$ncnd_amount=0;                                          
				} else {                                  
					$return_service_ncnd=1;                     
					$ncnd_amount=$this->input->post('ncnd_amount');
				}
				
				$code = $this->input->post('currency_code');
				if(empty($code)){                        
					$currency_code=$this->data['currency_code'];              
				}else{
					$currency_code=$this->input->post('currency_code');
				}         
				                       
				/*$h=$this->input->post('height');
				$w=$this->input->post('width');
				$l=$this->input->post('length');   
				if(empty($h)){$h=0;} if(empty($w)){$w=0;} if(empty($l)){$l=0;}*/

				$boxheight = explode(',', $this->input->post('boxheight'));
				$boxwidth = explode(',', $this->input->post('boxwidth'));
				$boxlength = explode(',', $this->input->post('boxlength'));
				$boxvweight = explode(',', $this->input->post('boxvweight'));
				
				if($this->input->post('paymode') == '3') {
					$account_type = $this->input->post('account_type');
				} else {
					$account_type = null;
				}

				$booking_date = str_replace('/', '-', $this->input->post('booking_date'));
				$suffix = date('mY', strtotime($booking_date));
				// var_dump($suffix); exit;

				for($i=0; $i<$this->input->post('pieces'); $i++) {
					$no = $i+1;
					$dimention[] = $no . ' - ( H - '.$boxheight[$i].' / W - '.$boxwidth[$i].' / L - '.$boxlength[$i].' ) ';
				$data = array(
					'box_number' => $no,
					'box_height' => $boxheight[$i],
					'box_width' => $boxwidth[$i],
					'box_length' => $boxlength[$i],
					'box_vol_weight' => $boxvweight[$i],
					'booking_number' => $awb_number);
					$this->base_model->add('tbl_box',$data);
				}
                $dimention = implode(", ",$dimention); 

				$data = array(
					'booking_number' 		=> $awb_number,
					'book_type' 			=> $this->input->post('book_type'),
					'company_code' 			=> $this->input->post('compnycode'),
					'batch_number' 			=> $this->input->post('batch_number'),
				    'booking_reference' 	=> $this->input->post('reference_number'),
					'booking_date' 			=>date('Y-m-d',strtotime($booking_date)).' '.date('H:i:s',strtotime($this->input->post('pickup_time'))),
					'pickup_date' 			=>date('Y-m-d',strtotime($booking_date)),
					'pickup_time' 			=> $this->input->post('pickup_time'),
					'from_company' 			=> $this->input->post('from_company'),
					'from_address' 			=> $this->input->post('from_address'),
					'from_location' 		=> $this->input->post('from_location'),
					'from_city' 			=> $this->input->post('from_city'),
					'from_country' 			=> $this->input->post('from_country'),
					'from_cperson' 			=> $this->input->post('from_cperson'),
					'from_contactno' 		=> $this->input->post('from_contactno'),
					'to_company_code' 		=> $this->input->post('compnyrecivercode'),
					'to_company' 			=> $this->input->post('to_company'),
					'to_address' 			=> $this->input->post('to_address'),
					'to_location' 			=> $this->input->post('to_location'),
					'to_city' 				=> $this->input->post('to_city'),
					'to_country' 			=> $this->input->post('to_country'),
					'to_cperson' 			=> $this->input->post('to_cperson'),
					'to_contactno' 			=> $this->input->post('to_contactno'),
					'other_company' 		=> $this->input->post('from_company'),
					'other_address' 		=> $this->input->post('from_address'),
					'other_location' 		=> $this->input->post('from_location'),
					'other_country' 		=> $this->input->post('from_country'),
					'other_cperson' 		=> $this->input->post('from_cperson'),
					'other_contactno' 		=> $this->input->post('from_contactno'),
					'payment_type' 			=> 'Account(A/c)',
					'package_type' 			=> $package_type[0]->package_type,
				    'service_type' 			=> $service_type,
					'weight' 				=> $this->input->post('weight'),
					'pieces' 				=> $this->input->post('pieces'),
					'volume_weight' 		=> $this->input->post('volume_weight'),
				    'chargable_weight' 		=> $this->input->post('chargable_weight'),
					/*'height' => $h,
					'width' => $w,
					'length' => $l,*/
					'item_description' => $this->input->post('item_description'),
					'special_instruction' 	=> $this->input->post('special_instruction'),
					'return_service_ncnd' 	=> $return_service_ncnd,
					'currency_code' 		=> $currency_code,
					'courier_charge' 		=> $this->input->post('courier_charge'),
					'ncnd_amount' 			=> $ncnd_amount,
					'reference_number' 		=> $this->input->post('reference_number'),
				    'company_division' 		=> $this->data['division'],
					'current_status' 		=> 'Submitted',
					'payment_mode' 			=> $this->input->post('paymode'),
				    'account_type' 			=> $account_type,
                    'box_dimentions' 		=> $dimention,
                    'customs_declared_currency_code' => $this->input->post('customs_declared_currency_code'),
                    'customs_declared_value' => $this->input->post('customs_declared_value'),
                    'collected_by' 			=> $this->input->post('collected_by'),
                    'from_mobileno' 		=> $this->input->post('from_mobileno'),
                    'from_zipcode' 			=> $this->input->post('from_zipcode'),
                    'to_mobileno' 			=> $this->input->post('to_mobileno'),
                    'to_zipcode' 			=> $this->input->post('to_zipcode'));
				$this->base_model->add('tbl_booking',$data);
				$lastid = $this->db->insert_id();     
				$data='';                        
                                         
				$data = array('settings_value' => $get_bnumber[0]->settings_value+1);
				$this->base_model->update('tbl_settings',$data,array('settings_label'=>'awb-end'));          
				$data = '';
				$data = array(
					  'booking_number' =>  $awb_number,
					  'status_datetime' =>date('Y-m-d',strtotime($booking_date)).'  '.date('H:i:s',strtotime($this->input->post('pickup_time'))),
					  'location' => 'Customer Location',
					  'courier_status' => 'Submitted',  
					  'status_details' =>'Shipment at Collection Point'
					  );
				$this->load->model('status_model');
				$this->status_model->insert_status($suffix,$data);
					  
				// $this->base_model->add('tbl_ship_status',$data);
				$data='';
				$consignee = $this->base_model->edit('tbl_consignee_master',array('consignee_name'=>$this->input->post('to_company')));

				if(count($consignee)==0){
					$data = array(
						'consignor_account' => $this->input->post('editable-select'),
						'consignor' => $this->input->post('to_company'),
						'consignee_name' => $this->input->post('to_company'),
						'consignee_contactperson'=>$this->input->post('to_cperson'),
						'consignee_address1'=>$this->input->post('to_address'),
						'consignee_address2'=>'',
						'consignee_location'=>$this->input->post('to_location'),
						'consignee_country'=>$this->input->post('to_country'),
						'consignee_zipcode'=>'',
						'consignee_telephone'=>$this->input->post('to_contactno'),
						'consignee_fax'=>'',
						'consignee_mobile'=>$this->input->post('to_mobileno'));
					$this->base_model->add('tbl_consignee_master',$data);
					$data='';
				}else{
					$data = array(
					    'consignor_account' => $this->input->post('editable-select'),
						'consignor' => $this->input->post('to_company'),
						'consignee_name' => $this->input->post('to_company'),
						'consignee_contactperson'=>$this->input->post('to_cperson'),
						'consignee_address1'=>$this->input->post('to_address'),
						'consignee_address2'=>'',
						'consignee_location'=>$this->input->post('to_location'),
						'consignee_country'=>$this->input->post('to_country'),
						'consignee_zipcode'=>'',
						'consignee_telephone'=>$this->input->post('to_contactno'),
						'consignee_fax'=>'',
						'consignee_mobile'=>$this->input->post('to_mobileno'));
					
					$this->base_model->update('tbl_consignee_master',$data,array('consignee_id'=>$consignee[0]->consignee_id));
					$data='';
				}

				$checked = $this->input->post('custom_status_enable');

				if($checked == 1){

					$additional_booking_status = array(

						'status_datetime' => date('Y-m-d H:i:s'),

						'current_status'  =>  $this->input->post('custom_status'),  

						'status_details'  => $this->input->post('custom_status')

					);

					$this->base_model->update('tbl_booking',$additional_booking_status,array('booking_number'=>$awb_number));

					$data = array(

						  'booking_number' =>  $awb_number,

						  'status_datetime' => date('Y-m-d H:i:s'),

						  'location' 		=> 'Office',

						  'courier_status' 	=> $this->input->post('custom_status'), 

						  'status_details' 	=> $this->input->post('custom_status')

					);

					$this->base_model->add('tbl_ship_status',$data);

				}

				$this->session->set_flashdata('success', 'Consigment added sucessfully ...!!!');
				redirect(base_url().'booking/edit/'.$lastid);
			}
			//$this->output->enable_profiler(TRUE);
		}
	
	
public function edit($id=''){

	$data['edit']=$this->base_model->edit('tbl_booking',array('booking_id'=>$id));

	$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.min.css');
	$this->template->addCss(base_url().'assets/dist/css/booking.css');
	$this->template->addJs(base_url().'assets/dist/js/autotab.js');
	$this->template->addJs(base_url().'assets/dist/js/booking.min.js');
	$this->template->addJs(base_url().'assets/dist/js/inscan.js');
	$this->template->addJs(base_url().'assets/dist/js/booking-select.js');
	$this->template->addhJs(base_url().'assets/dist/js/editable-main.js');
	$this->template->addhJs(base_url().'assets/dist/js/jquery-editable-select.js');
	$this->template->addCss(base_url().'assets/dist/css/jquery-editable-select.min.css');

		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Booking', base_url().'index');
		$this->breadcrumb->add('Create', base_url().'create');
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='Consignment Entry';

		$data['country'] = $this->base_model->get('tbl_country');
		$compstatus = array('company_status' => '1');                    
		$data['companymain']= $this->base_model->edit('tbl_company_master', $compstatus);
		$customer_country = $this->base_model->edit('tbl_country',array('country'=>$data['companymain'][0]->company_country));

		$data['fromcity'] = $this->base_model->edit('tbl_city',array('country_code'=>$customer_country[0]->iso));

		$data['tocity'] = array();

	   $data['employee_master'] = $this->base_model->get_fields('tbl_employee_master',array('employee_code','employee_name'),array('employee_status'=>1));
	
                $config_validation=array(
				array('field' => 'service_type','label' => 'Service Type','rules' => 'trim|required|xss_clean'),
				array('field' => 'booking_number','label' => 'Booking Number','rules' => 'trim|required|xss_clean'),
				array('field' => 'booking_date','label' => 'Booking date','rules' => 'trim|required|xss_clean'),
				array('field' => 'pickup_time','label' => 'Pickup Time','rules' => 'trim|required|xss_clean'),
				array('field' => 'from_company','label' => 'From Company','rules' => 'trim|required|xss_clean'),
				array('field' => 'from_address','label' => 'From Address','rules' => 'trim|required|xss_clean'),
				array('field' => 'from_location','label' => 'From Location','rules' => 'trim|required|xss_clean'),
				array('field' => 'from_country','label' => 'From Country','rules' => 'trim|required|xss_clean'),
				array('field' => 'from_cperson','label' => 'From Cperson','rules' => 'trim|required|xss_clean'),
				array('field' => 'from_contactno','label' => 'From Contactno','rules' => 'trim|required|xss_clean'),
				array('field' => 'to_company','label' => 'To Company','rules' => 'trim|required|xss_clean'),
				array('field' => 'to_address','label' => 'To Address','rules' => 'trim|required|xss_clean'),
				array('field' => 'to_location','label' => 'To Location','rules' => 'trim|required|xss_clean'),
				array('field' => 'to_country','label' => 'To Country','rules' => 'trim|required|xss_clean'),
				array('field' => 'to_cperson','label' => 'To Contact Person','rules' => 'trim|required|xss_clean'),
				array('field' => 'to_contactno','label' => 'To Contactno','rules' => 'trim|required|xss_clean'),
				array('field' => 'package_type','label' => 'Package Type','rules' => 'trim|required|xss_clean'),
				array('field' => 'courier_charge','label' => 'Courier Charge','rules' => 'trim|required|xss_clean'),array('field' => 'collected_by','label' => 'Collected By','rules' => 'trim|required|xss_clean'),
				array('field' => 'pieces','label' => 'Pieces','rules' => 'trim|required|xss_clean'),
			);
                                         
		$this->form_validation->set_rules($config_validation);
	                            
		$compstatus = array('company_status' => '1');
        $data['companymain']= $this->base_model->edit('tbl_company_master', $compstatus);
	               
		if ($this->form_validation->run() == FALSE) {

			$this->db->select("GROUP_CONCAT(box_number ORDER BY box_number ASC SEPARATOR ', ') as nobox, GROUP_CONCAT(box_height ORDER BY box_number ASC SEPARATOR ', ') as box_height, GROUP_CONCAT(box_width ORDER BY box_number ASC SEPARATOR ', ') as box_width, GROUP_CONCAT(box_length ORDER BY box_number ASC SEPARATOR ', ') as box_length,
			GROUP_CONCAT(box_vol_weight ORDER BY box_number ASC SEPARATOR ', ') as box_vol_weight");
			$this->db->from('tbl_box');	          
			$this->db->where('booking_number',$data['edit'][0]->booking_number);
			$q= $this->db->get();
			$data['boxresults'] = $q->result();

			$nobox = explode(', ', $data['boxresults'][0]->nobox);
			foreach ($nobox as $value) {
				$noboxedit .=  $value ;
				$noboxedit .= ", ";
			}   
			$remove[]='"';	
			$noboxedit =str_replace($remove, "", $noboxedit );
			$data['noboxedit']=rtrim($noboxedit,", ");
			
			if($data['edit'][0]->service_type == 'ME') {
				$data['edit'][0]->service_type = 2;
			} else {
				$data['edit'][0]->service_type = 1;
			}
			
			$data['domestic_type'] = $this->base_model->get_fields('tbl_company_master',array('domestic_rate_type'),array('company_code'=>$data['edit'][0]->company_code));
			
			$data['package']=$this->base_model->get('tbl_package_type');
			$data['currency'] = $this->base_model->edit('tbl_currencies',array('currency_status'=>'1'));

			$country = $this->base_model->edit('tbl_country',array('country'=>$this->input->post('to_country')));
			$data['tocity'] = $this->base_model->edit('tbl_city',array('country_code'=>$customer_country[0]->iso));

			$this->template->view('booking/form',$this->data,$data);
		} else {

		$boxheight = explode(',', $this->input->post('boxheight'));
		$boxwidth = explode(',', $this->input->post('boxwidth'));
		$boxlength = explode(',', $this->input->post('boxlength'));
		$boxvweight = explode(',', $this->input->post('boxvweight'));
		
		if($this->input->post('service_type') == 1)
			$service_type='CN';                      
		else                                                                       
			$service_type='ME'; 
			
		$package_type=$this->base_model->edit('tbl_package_type',array('package_type'=>$this->input->post('package_type'),'package_status'=>1));

		$ncnd = $this->input->post('ncnd_amount');      
		if(empty($ncnd) || $ncnd==0){                       
			$return_service_ncnd=0;                                    
			$ncnd_amount=0;                                          
		} else {                                  
			$return_service_ncnd=1;                     
			$ncnd_amount=$this->input->post('ncnd_amount');
		}
			
		$code = $this->input->post('currency_code');
		if(empty($code)){
			$currency_code=$this->data['currency_code'];
		} else {          
			$currency_code=$this->input->post('currency_code'); }
			                    
		if($this->input->post('paymode') == '3') {
			$account_type = $this->input->post('account_type');
		} else {
			$account_type = null;
		}
			$booking_number = $data['edit'][0]->booking_number;
			$this->base_model->delete('tbl_box',array('booking_number'=>$booking_number));

            $dimention = array();
			for($i=0; $i<$this->input->post('pieces'); $i++) {
					$no = $i+1;
					$dimention[] = $no . ' - ( H - '.$boxheight[$i].' / W - '.$boxwidth[$i].' / L - '.$boxlength[$i].' ) ';
			$data = array(
				'box_number' => $no,
				'box_height' => $boxheight[$i],
				'box_width' => $boxwidth[$i],
				'box_length' => $boxlength[$i],
				'box_vol_weight' => $boxvweight[$i],
				'booking_number' => $booking_number);
				$this->base_model->add('tbl_box',$data);
			}
            $dimention = implode(", ",$dimention);

            $data = '';
			$booking_date = str_replace('/', '-', $this->input->post('booking_date'));
			$data = array(
					'company_code' => $this->input->post('compnycode'),
					'batch_number' => $this->input->post('batch_number'),
				    'booking_reference' => $this->input->post('reference_number'),
					'booking_date' => date('Y-m-d',strtotime($booking_date)).' '.date('H:i:s',strtotime($this->input->post('pickup_time'))),
					'pickup_date' => date('Y-m-d',strtotime($booking_date)),
					'pickup_time' => $this->input->post('pickup_time'),
					'from_company' => $this->input->post('from_company'),
					'from_address' => $this->input->post('from_address'),
					'from_location' => $this->input->post('from_location'),
					'from_city' => $this->input->post('from_city'),
					'from_country' => $this->input->post('from_country'),
					'from_cperson' => $this->input->post('from_cperson'),
					'from_contactno' => $this->input->post('from_contactno'),
				    'to_company_code' => $this->input->post('compnyrecivercode'),
					'to_company' => $this->input->post('to_company'),
					'to_address' => $this->input->post('to_address'),
					'to_location' => $this->input->post('to_location'),
					'to_city' 			=> $this->input->post('to_city'),
					'to_country' => $this->input->post('to_country'),
					'to_cperson' => $this->input->post('to_cperson'),
					'to_contactno' => $this->input->post('to_contactno'),
					'other_company' => $this->input->post('from_company'),
					'other_address' => $this->input->post('from_address'),
					'other_location' => $this->input->post('from_location'),
					'other_country' => $this->input->post('from_country'),
					'other_cperson' => $this->input->post('from_cperson'),
					'other_contactno' => $this->input->post('from_contactno'),
					'payment_type' => 'Account(A/c)',
					'package_type' => $package_type[0]->package_type,
				    'service_type' => $service_type,
					'weight' => $this->input->post('weight'),
					'pieces' => $this->input->post('pieces'),
					'volume_weight' => $this->input->post('volume_weight'),
				    'chargable_weight' => $this->input->post('chargable_weight'),
					'item_description' => $this->input->post('item_description'),
					'special_instruction' => $this->input->post('special_instruction'),
					'return_service_ncnd' => $return_service_ncnd,
					'currency_code' => $currency_code,
					'courier_charge' => $this->input->post('courier_charge'),
					'ncnd_amount' => $ncnd_amount,
					'reference_number' => $this->input->post('reference_number'),
				    'company_division' => $this->data['division'],
					'payment_mode' => $this->input->post('paymode'),
			        'account_type' => $account_type,
                    'box_dimentions' => $dimention,
					'customs_declared_currency_code' => $this->input->post('customs_declared_currency_code'),
					'customs_declared_value' => $this->input->post('customs_declared_value'),
					'collected_by' => $this->input->post('collected_by'),
					'from_mobileno' => $this->input->post('from_mobileno'),
                    'from_zipcode' => $this->input->post('from_zipcode'),
                    'to_mobileno' => $this->input->post('to_mobileno'),
                    'to_zipcode' => $this->input->post('to_zipcode')
                );
			
			$this->base_model->update('tbl_booking',$data,array('booking_id'=>$id));


			$checked = $this->input->post('custom_status_enable');

			
			

				if($checked == 1){

					$booking = $this->base_model->get_fields('tbl_booking',array('booking_number'),array('booking_id'=>$id));
					
					$additional_booking_status = array(

						'status_datetime' => date('Y-m-d H:i:s'),

						'current_status'  => 'Inscan',  

						'status_details'  => $this->input->post('custom_status')

					);

					$this->base_model->update('tbl_booking',$additional_booking_status,array('booking_number'=>$booking[0]->booking_number));

					$additional_status = array(

						  'booking_number' 	=> $booking[0]->booking_number,

						  'status_datetime' => date('Y-m-d H:i:s'),

						  'location' 		=> 'Office',

						  'courier_status' 	=> 'Inscan',  

						  'status_details' 	=> $this->input->post('custom_status')

					);



					$this->base_model->add('tbl_ship_status',$additional_status);

				}
			$this->session->set_flashdata('success', 'Consigment updated sucessfully..!');
			redirect(base_url().'booking/edit/'.$id);
			//$this->output->enable_profiler(TRUE);
		}
	}
	
	public function delete($id=''){
		if(!empty($id)){
			$booking_number=$this->base_model->edit('tbl_booking',array('booking_id'=>$id));
			$this->base_model->delete('tbl_ship_status',array('booking_number'=>$booking_number[0]->booking_number));
			$this->base_model->delete('tbl_booking',array('booking_id'=>$id));
			echo 'Success';
		}
		else
			echo 'Failure';
	}

	public function awb($id=""){
		$this->load->library('pdf');
	
		$data=$this->base_model->edit('tbl_booking',array('booking_id'=>$id));
		if(!empty($data)){
			$pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
			// set document information
			$pdf->SetCreator(PDF_CREATOR);
			//$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
			$pdf->SetAuthor('TSS Smart Systems LLC');
			$pdf->SetTitle('AirwayBill - '.$data[0]->booking_number);
			$pdf->SetSubject('AirwayBill');
			$pdf->SetKeywords($data[0]->booking_number.', PDF, AWB Bill');
			$pdf->SetTopMargin(5);
			$pdf->SetLeftMargin(5);
			$pdf->SetRightMargin(6);
			$pdf->SetAutoPageBreak(TRUE, 0);
			$pdf->SetFont('Helvetica', '', 9);
			$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');
			$pdf->AddPage('P', 'A4');
			$service_type=$this->base_model->edit('tbl_package_type',array('service_type'=>$data[0]->service_type));

			$logo = '<img src="'.$this->data['awb-logo'].'" width="'.$this->data['awb_logo_awb_size'].'"/>';
			
			$style =  array(
				'position'=>'R', 
				'border'=>0, 
				'padding'=>1, 
				'fgcolor'=>array(0,0,0), 
				'bgcolor'=>array(255,255,255), 
				'text'=>true, 
				'font'=>'helvetica', 
				'fontsize'=>12, 
				'stretchtext'=>4
			);
			$params = $pdf->serializeTCPDFtagParameters(array($data[0]->booking_number, 'C128', '', '', 63, 23, 0.5,$style, 'N'));
			$barcode = '<tcpdf method="write1DBarcode" params="'.$params.'"/>';
			$from_address=trim(strtoupper($data[0]->from_company));
			if(!empty($data[0]->from_cperson)){$from_address.=' / '.trim($data[0]->from_cperson).'<br/>';}
			$from_address.= substr(trim(preg_replace('/\s\s+/', ' ', $data[0]->from_address)), 0, 60).'<br/>';
			$from_address.= trim($data[0]->from_location);
			$from_address.= ','.trim($data[0]->from_country);

			$to_address=trim(strtoupper(str_replace('"', "", $data[0]->to_company)));
			if(!empty($data[0]->to_cperson)){$to_address.=' / '.trim($data[0]->to_cperson).'<br/>';}
			$to_address.= substr(trim(preg_replace('/\s\s+/', ' ', $data[0]->to_address)), 0, 60).'<br/>';
			$to_address.= trim($data[0]->to_location);
			$to_address.= ','.trim($data[0]->to_country);
			
			$return_service="";
			if(!empty($data[0]->currency_code)) { $currency_code = $data[0]->currency_code;} else{ $currency_code = $this->data['currency_code'];}

			if(!empty($data[0]->ncnd_amount)) { $return_service	 = '<br/>'.$currency_code.' '.number_format($data[0]->ncnd_amount,2,'.','');}
			

			$pdf_data=array(
				'logo' 				=> $logo,
				'address' 			=> $this->data['awb-address'],
				'barcode'			=> $barcode,
				'origin'			=> $data[0]->from_location,
				'destination'		=> $data[0]->to_location,
				'account_number'	=> $data[0]->company_code,
				'reference_number'	=> $data[0]->reference_number,
				'awb_number'		=> $data[0]->booking_number,
				'booking_date'		=> "Booking Date :".date('d/m/Y',strtotime($data[0]->booking_date)),
				'from_address'		=> $from_address,
				'to_address'		=> $to_address,
				'from_contact'		=> $data[0]->from_contactno,
				'from_mobile'		=> $data[0]->from_mobileno,
				'to_contact'		=> $data[0]->to_contactno,
				'to_mobile'			=> $data[0]->to_mobileno,
				'service_type'		=> $data[0]->service_type,
				'shipper_agreement'	=> '',
				'service'			=> $data[0]->package_type,
				'pieces'			=> $data[0]->pieces,
				'weight'			=> $data[0]->weight,
				'volume_weight'		=> $data[0]->volume_weight,
				'width'				=> $data[0]->width,
				'height'			=> $data[0]->height,
				'length'			=> $data[0]->length,
				'item_description'	=> substr(trim(preg_replace('/\s\s+/', ' ', $data[0]->item_description)), 0, 100),
				'special_instruction'=> substr(trim(preg_replace('/\s\s+/', ' ', $data[0]->special_instruction)), 0, 100),
				'return_service'	=> $return_service,
				'payment_type'		=> $data[0]->payment_type,
				'proof_of_delivery'	=> "Consignment Received in Good Condition\n\nName & Signature&nbsp;&nbsp;&nbsp;&nbsp;Date & Time",
				'pod_image_signature'=>''
			);
			
			$tbl=$this->load->view('booking/awb_bill',array('data'=>$pdf_data),true);
			$pdf->writeHTML($tbl, true, 0, true, 0);
			$pdf->writeHTML('<p align="center">-------------------------------------------------------------------------------------------------------</p><br/>', true, 0, true, 0);
			$pdf->writeHTML($tbl, true, 0, true, 0);
			$pdf->lastPage();
			$pdf->Output($data[0]->booking_number.'.pdf', 'I');
		}
	}

    function checkcompany() {
    	$companycodes = array('company_code' => $this->input->post('id'));
    	if($companydata=$this->base_model->edit('tbl_company_master', $companycodes)){
    		echo $companydata[0]->company_name."|||".$companydata[0]->company_address."|||".$companydata[0]->company_city."|||".$companydata[0]->company_country."|||".$companydata[0]->contact_person."|||".$companydata[0]->company_contact."|||".$companydata[0]->domestic_rate_type."|||".$companydata[0]->company_location;
    	}  else  { 
         	echo 1;
        }
    }

    function checkcompanyreciver(){
        $companycodes = array('company_code' => $this->input->post('id'));
    	if($companydata=$this->base_model->edit('tbl_company_master', $companycodes)){
    		echo $companydata[0]->company_name."|||".$companydata[0]->company_address."|||".$companydata[0]->company_city."|||".$companydata[0]->company_country."|||".$companydata[0]->contact_person."|||".$companydata[0]->company_contact."|||".$companydata[0]->company_location;
    	}  else  {
         	echo 1;
        } 
    }
        
		function get_package(){
			$data=array();
			$get = $this->input->post('get');
			$product = $this->input->post('product');
			if($get==1)      
				$this->db->where('service_type','CN');
			else    
				$this->db->where('service_type <>','CN');
			$this->db->where('package_status','1');
			$q = $this->db->get('tbl_package_type');
			if($q->num_rows() >0){
				foreach($q->result() as $row){
					$data[]=$row;
				}
			}
			foreach($data as $row){
				if($row->package_type == $product)
					$optionSelected = 'selected="selected"';
				else
					$optionSelected = '';
				$items[] =  array('optionValue' =>$row->package_type,'optionDisplay' =>$row->package_type, 'optionSelected' =>$optionSelected);
			}
			echo json_encode($items);
			//$this->output->enable_profiler(TRUE);
		}
	     
	function get_courier_charge(){
		
		$package_code = $this->base_model->get_fields('tbl_package_type',array('package_code'),array('package_type'=>$this->input->post('package_type')));
		
		$this->db->select('price_id,amount,additional_weight,additional_rate,weight_from,weight_to');
		$this->db->from('tbl_price');             
		$this->db->where('company_code',$this->input->post('compnycode'));
		$this->db->where('from_city',strtolower($this->input->post('from_location')));
		$this->db->where('to_city',strtolower($this->input->post('to_location')));
		$this->db->where('service_type',$package_code[0]->package_code);
		$data['price'] = $this->db->get()->result();
		                                 
		if($this->input->post('weight') >= $data['price'][0]->weight_from &&  $this->input->post('weight') <= $data['price'][0]->weight_to) {
		$courier_charge =  $data['price'][0]->amount; 
		}
		else if($this->input->post('weight') > $data['price'][0]->weight_to) {
		$weight = ceil($this->input->post('weight')) - $data['price'][0]->weight_to;
		$weight = $weight * $data['price'][0]->additional_rate;
		$courier_charge = $weight + $data['price'][0]->amount;
		} else {    
		$courier_charge = 0;
		}              
     echo $courier_charge;
	}
	
	function get_from_location(){

		$iso = $this->base_model->get_fields('tbl_country',array('iso'),array('country'=>$this->input->post('f_country')));

		$this->db->where('country_code', $iso[0]->iso);
		$this->db->limit($this->input->post('limit'), 0);
		$q = $this->db->get('tbl_city');
		if($q->num_rows() >0){
			foreach($q->result() as $row){
				$data[]=$row;
			}
		}
		foreach($data as $row){
			$items[] =  array('label' =>trim($row->city_name),'category' =>'Location');
		}
		echo json_encode($items);
	}
	
	function get_courier_charge_by_company(){
		$row = $this->base_model->get_fields('tbl_company_master',array('domestic_rate','domestic_baseweight','domestic_additional_rate'),array('company_code'=>$this->input->post('compnycode')));
		          
		if($this->input->post('weight') <= $row[0]->domestic_baseweight) {
			echo $row[0]->domestic_rate;
		} else {
			$net_weight = $this->input->post('weight') - $row[0]->domestic_baseweight;
			$net_weight = $net_weight * $row[0]->domestic_additional_rate;
			$net_weight = $net_weight + $row[0]->domestic_rate;
			echo $net_weight; 
		}
	}
	
	function awbedit(){
		
		$awb = $this->base_model->get_fields('tbl_booking',array('booking_id'),array('booking_number'=>$this->input->post('awb')));
		
		if(empty($awb[0]->booking_id)) {
			
			$this->session->set_flashdata('error', 'Booking Number Not Exist..!!!');
			$this->template->view('booking/list',$this->data,$data);
		} else {
			
			redirect(base_url().'booking/edit/'.$awb[0]->booking_id);
			
		}
		
	}

	function hold_status(){

		$awbNumber = explode(',', $this->input->post('booking_number'));

		if($this->input->post('holdValue') == 1){
			$hold_value = 'Hold';
		}
		else{
			$hold_value = 'Unhold';
		}


		foreach($awbNumber as $awb){
			if(!empty($awb)){
				
				$awb_data = $this->base_model->get_fields('tbl_booking',array('booking_number'),array('booking_id'=>$awb));

				$data[] = array(
					'booking_id'	  => $awb,
					'shipment_hold'   => $this->input->post('holdValue'),
					'current_status'  => $this->input->post('current_status'),
					'status_details'  => $this->input->post('remarks'),
					'status_datetime' => date('Y-m-d H:i:s'),
				);
				
				$status = array(
					'booking_number'	=>	$awb_data[0]->booking_number,
					'status_datetime' 	=> 	date('Y-m-d H:i:s'),
					'location' 			=> 	'Despatch Hub',
					'courier_status' 	=> 	$hold_value,
					'status_details' 	=> 	$this->input->post('remarks'),
				);

				$this->db->insert('tbl_ship_status', $status);
			}
			else{
				$this->session->set_flashdata('danger', 'No AWB Number Selected.');
				redirect(base_url().'booking/index');
			}
		}
		if(!empty($data)){
			$this->db->update_batch('tbl_booking', $data,'booking_id');
		}
		

		$this->session->set_flashdata('success', 'Hold Status Updated Sucessfully..!');
		redirect(base_url().'booking');
	}

	function list_print(){

		$this->load->library('pdf');
		$fields = array('booking_id','booking_date','booking_number','to_address','to_company','to_city','to_location','to_mobileno','to_contactno','to_cperson','weight','pieces','company_code','current_status','package_type','reference_number','ncnd_amount','manifest_number');

		if($this->input->get('from_date') || $this->input->get('to_date') || $this->input->get('q') || $this->input->get('shipment_hold') || $this->input->get('manifest_number') || $this->input->get('company_code')){

			$config['suffix'] = '?'.urldecode(http_build_query($_GET, '', "&"));
			$offset = 0;

			$this->db->select($fields);
			$this->db->from('tbl_booking');

			if($this->input->get('from_date'))
			$this->db->where('DATE(booking_date) >=',date('Y-m-d',strtotime($this->input->get('from_date'))));
			if($this->input->get('to_date'))
				$this->db->where('DATE(booking_date) <=', date('Y-m-d',strtotime($this->input->get('to_date'))));
			if($this->input->get('manifest_number')){
				$this->db->like('manifest_number',$this->input->get('manifest_number'));
			}
			if($this->input->get('shipment_hold')){

				$this->db->where('shipment_hold',$this->input->get('shipment_hold'));
			}
			if($this->input->get('q')){
				$this->db->group_start();
				$this->db->like('to_contactno',$this->input->get('q'),'after');
				$this->db->or_like('to_cperson',$this->input->get('q'),'after');
				$this->db->group_end();
			}
			$this->db->limit($config['per_page'],$offset);
			$data = $this->db->get()->result();

		}
		else{

			$this->db->select($fields);
			$this->db->from('tbl_booking');
			$config['suffix'] = '?'.urldecode(http_build_query($_GET, '', "&"));
			$offset = 0;
			$this->db->limit($config['per_page'],$offset);
			$data = $this->db->get()->result();
		}
		
		if(!empty($data)){

			$pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
			$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('TSS Smart Systems LLC');
			$pdf->SetTitle('Booking Consigment List');
			$pdf->SetSubject('Booking Consigment List ');
			$pdf->SetKeywords('CA, PDF, Booking Consigment List');
			$pdf->SetTopMargin(5);
			$pdf->SetLeftMargin(5);
			$pdf->SetRightMargin(6);
			$pdf->SetAutoPageBreak(TRUE, 0);
			$pdf->SetFont('Helvetica', '', 9);
			$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');
			$pdf->AddPage('L', 'A4');

			$tbl=$this->load->view('booking/print_list',array('result'=>$data),true);

			$pdf->writeHTML($tbl, true, 0, true, 0);
			$pdf->lastPage();
			$pdf->Output('Booking_Consigment.pdf', 'I');
		}
	}

	function sent_sms(){

		$awbNumber = explode(',', $this->input->post('booking_number'));
		if($this->input->post('remarks'))
			$remarks = $this->input->post('remarks');
		else
			$remarks = 'Package with customer service';

		foreach($awbNumber as $awb){

			$awb_data = $this->base_model->get_fields('tbl_booking',array('booking_number','to_contactno', 'to_city'),array('booking_id'=>$awb));

			$data_submitted[]= array(
					'booking_number'	 =>$awb_data[0]->booking_number,
					'current_status' =>$this->input->post('current_status'),
				);

			$status = array(
				'booking_number'	=>	$awb_data[0]->booking_number,
				'status_datetime' 	=> 	date('Y-m-d H:i:s'),
				'courier_status' 	=> 	$this->input->post('current_status'),
				'status_details' 	=> 	$remarks.', sms sent to consignee',
				'location'			=>	$awb_data[0]->to_city,
			);

			$this->db->insert('tbl_ship_status', $status);

			if ($awb_data[0]->to_contactno!=''){

				$msg="Dear Customer, Your Shipment Booking No:".$awb_data[0]->booking_number." is on hold, due to ".$this->input->post('remarks');

				$to_mobileno = ltrim($awb_data[0]->to_contactno,'+');
				$to_mobileno = ltrim($awb_data[0]->to_contactno,'971');
				$to_mobileno = ltrim($awb_data[0]->to_contactno,'0');
				$toMobileno ='971'.$to_mobileno;
				// $this->Sms_code_send($to_mobileno, $msg); 
			}

			$sms_data[] = array(
				'mobile_no'	=>$toMobileno,
				'sms_text'	=>$msg);

		}

		if(!empty($data_submitted)){
			$this->db->update_batch('tbl_booking', $data_submitted,'booking_number');
		}

		if(!empty($sms_data)){
			$this->db->insert_batch('tbl_sms_send',$sms_data);
		}
		redirect(base_url().'booking/consignment_searching');

	}

	function reCoordinate(){

		$booking=$this->input->post("bid");

		foreach($booking as $awb){

			$awb_data = $this->base_model->get_fields('tbl_booking',array('booking_number','to_address', 'to_city', 'to_country'),array('booking_id'=>$awb));

			$get_map_json   = '';

			$address = trim($awb_data[0]->to_address).','.trim($awb_data[0]->to_city).','.trim($awb_data[0]->to_country);

           
			$get_map_json 	= $this->smart_lib->get_latlng_from_address($address);
			
		

			$obj 			= json_decode($get_map_json);
			
		

			if(empty($awb_data[0]->consignee_latitude)){

				$awb_data[0]->consignee_latitude = $obj->results[0]->geometry->location->lat;

				$awb_data[0]->consignee_longitude= $obj->results[0]->geometry->location->lng;

			}

			if(empty($awb_data[0]->consignee_latitude)){

				$awb_data[0]->consignee_latitude = 0;

				$awb_data[0]->consignee_longitude = 0;

			}

			$data = array(
				'consignee_latitude' 	=> $awb_data[0]->consignee_latitude ,
				'consignee_longitude'	=> $awb_data[0]->consignee_longitude ,
			);
			
		

			$this->base_model->update('tbl_booking',$data,array('booking_number'=>$awb_data[0]->booking_number));
		}

		$this->session->set_flashdata('success', 'Consignee address updated sucessfully ...!!!');
		redirect(base_url().'booking/consignment_searching');

	}

	function get_from_city(){
		$data=array();
		$country = $this->input->post('country');
		$iso = $this->base_model->get_fields('tbl_country',array('iso'),array('country'=>$country));
		$get = $this->input->post('get');
		
		$this->db->where('country_code', $iso[0]->iso);
		$q = $this->db->get('tbl_city');
		if($q->num_rows() >0){
			foreach($q->result() as $row){
				$data[]=$row;
			}
		}
		foreach($data as $row){
			
			$items[] =  array('optionValue' =>$row->city_name,'optionDisplay' =>$row->city_name, 'optionSelected' =>'');
		}
		echo json_encode($items);
		//$this->output->enable_profiler(TRUE);
   }

	function get_charge(){

		$to_city 	= $this->input->post('to_city');
		$from_city 	= $this->input->post('from_city');
		$from_company = $this->input->post('from_company');
		$package_type = $this->input->post('package_type');
		$weight = $this->input->post('weight');

		$this->db->select('*');
		$this->db->from('tbl_company_domestic_rate');
		$this->db->where('company_code',$from_company);
		$this->db->where('from_city' , $from_city);
		$this->db->where('to_city' , $to_city);
		$qry = $this->db->get(); 

		if($qry->num_rows() > 0){

			$qry = $qry->result();
			
			$weight= ceil($weight);
			if($weight <= $qry[0]->to_weight){
				$rate = $qry[0]->base_rate;
				
			}else{

				$bal_wieght = $weight-$qry[0]->to_weight;
				$rate = $qry[0]->base_rate+($bal_wieght*$qry[0]->additional_rate);
			}
			echo $rate;
			
// 			echo $qry[0]->base_rate;
// echo $this->db->last_query();
		}
		else{
			echo 0;
		}
	}

	
}
?>