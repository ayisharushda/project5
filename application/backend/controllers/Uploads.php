<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
class Uploads extends MY_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('base_model');
		$this->_init();
	}

	private function _init(){

		$this->template->addCss(base_url().'assets/bootstrap/css/bootstrap.min.css');

		$this->template->addCss('https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');

		$this->template->addCss('http://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css');

		$this->template->addCss(base_url().'assets/dist/css/AdminLTE.min.css');

		$this->template->addCss(base_url().'assets/dist/css/skins/_all-skins.min.css');

		$this->template->addCss(base_url().'assets/dist/css/custom.css');

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

		$this->excel_upload($offset=0);

	}


	function excel_upload($offset=0){

		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.css');

		$this->template->addJs(base_url().'assets/dist/js/jquery-ui-timepicker-addon.js');

		$this->template->addJs(base_url().'assets/plugins/iCheck/icheck.min.js');

		$this->template->addJs(base_url().'assets/dist/js/autotab.js');

		$this->template->addJs(base_url().'assets/dist/js/booking.min.js');

		$this->template->addJs(base_url().'assets/dist/js/pickup.js');

		// $this->load->library('excel');

		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());

		$this->breadcrumb->add('Uploads', base_url().'index');

		$this->breadcrumb->add('Excel Upload', base_url().'excel_upload');		

		$this->data['breadcrumb']=$this->breadcrumb->output();

		$this->data['main_title']='Bulk Upload';

		$data['company']=$this->base_model->get('tbl_company_master');

		$package_type=$this->base_model->edit('tbl_package_type',array('package_type_id'=>$this->input->post('package_type')));

		$data['package']=$this->base_model->edit('tbl_package_type',array('service_type'=>'CN'));
                       
		$where = array('excel_type'=>'1','service_type'=>'CN','company_division'=>$this->data['division']);
				
		$data['vehicle']=$this->base_model->get('tbl_vehicle_master');

		$this->load->library('pagination');

		$config['base_url'] = base_url().'/uploads/excel-upload';

		$config['total_rows'] = $this->base_model->get_count('tbl_excel_uploads',$where,'','');

		$config['per_page'] = $data['per_page'] = 300;

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

		$this->pagination->initialize($config);

		$data['pagination'] = $this->pagination->create_links();

		$data['edit'][0]->batch_number = 'BATCH-'.date('Ymdhis');
		
		$data['result'] =$this->base_model->get_paged_list('tbl_excel_uploads',$where,'','booking_id desc',$config['per_page'],$offset);
		           
		$config_validation=array(
			array('field' => 'package_type','label' => 'Package Type','rules' => 'trim|required|xss_clean'),
			array('field' => 'batch_number','label' => 'Batch Number','rules' => 'trim|required|xss_clean'));          
                                                  
		if (isset($_FILES['upload_file']) && empty($_FILES['upload_file']['name'])){
    		$vaildation[]=array('field' => 'upload_file','label' => 'Upload Photo','rules' => 'required');
		}
		else

			$vaildation[]=array();

		$config_validation=array_merge($config_validation,$vaildation);

        $this->form_validation->set_rules($config_validation);

		if ($this->form_validation->run() == FALSE) {

			$this->template->view('excel_uploads/bulk_upload',$this->data,$data);

		} else {

			$batch = $this->base_model->get_count('tbl_booking',array('batch_number'=>$this->input->post('batch_number')),'','');

			if($batch > 0){

				$this->session->set_flashdata('error', 'Batch Number exists'); 

				redirect(base_url().'uploads/excel-upload');

			}

			$upload_path = $this->data['excel-upload-path'];
			
			
			$config = array(          

			  'upload_path'     => $upload_path,

			  'allowed_types'   => '*',

			  'overwrite'       => TRUE,

			  'max_size'        => "1000KB");

			$this->load->library('upload');

 			$this->upload->initialize($config);

			if (!$this->upload->do_upload('upload_file')) {

				$this->session->set_flashdata('error', $this->upload->display_errors()); 

				$this->template->view('excel_uploads/bulk_upload',$this->data,$data);

			} else {

				if($this->input->post('service_type')==1)
					$service_type='CN';
				else
					$service_type='ME';
				
				$error = '';         
				$locations = array();
				$country_city_array = $this->base_model->edit('tbl_city',array('country_code'=>$this->data['country_code']));
				foreach ($country_city_array as $country_city){
					$locations[]= trim(strtolower($country_city->city_name));
				}
				$file_data = $this->upload->data();
				$file_path =  $upload_path.$file_data['file_name'];
				$excel_obj = IOFactory::load($file_path);   
				$cell_collection = $excel_obj->getActiveSheet();
				$highestRow = $cell_collection->getHighestRow(); 
				$highestColumn = $cell_collection->getHighestColumn(); 
				                       
				/*$cityarray = array();
				for($j = 2; $j <= $highestRow; $j++) {
					$checkcity=$cell_collection->rangeToArray('A'. $j .':'.$highestColumn .$j,NULL,TRUE,FALSE);
					 
					if(!empty($checkcity[0][12]))
					{
						$cityarray[]  = trim($checkcity[0][12]);
					}
				}
				
				$me = 1;
				
				foreach($cityarray as $cityval) {
					
					if (!in_array(strtolower(trim($cityval)), $locations)) {
						$error = $cityval.' Locations Not Exist in row number '.$me.' , '; 
						$this->session->set_flashdata('error', $error);
						redirect(base_url().'uploads/excel-upload');
					}
				$me++; }*/

				$company=$this->base_model->edit('tbl_company_master',array('company_id'=>$this->input->post('company_id')));
								
				for ($row = 2; $row <=$highestRow; $row++) {

					$rowData=$cell_collection->rangeToArray('A'. $row .':'.$highestColumn .$row,NULL,TRUE,FALSE);

					if(!empty($rowData[0][9])){

						$this->db->select('base_rate,to_weight,additional_rate');
						$this->db->from('tbl_company_domestic_rate');
						$this->db->where(array('from_city' => 'Dubai' ,'to_city' => $rowData[0][13], 'company_code' => $company[0]->company_code));

						$base_value = $this->db->get()->result();

						$rowData[0][20]= ceil($rowData[0][20]);
						if($rowData[0][20] <= $base_value[0]->to_weight){
							$rate = $base_value[0]->base_rate;
							
						}else{

							$bal_wieght = $rowData[0][20]-$base_value[0]->to_weight;
							$rate = $base_value[0]->base_rate+($bal_wieght*$base_value[0]->additional_rate);
						}

						$data = array(
							'booking_date' 		=> date('Y-m-d H:i:s'),
							'pickup_date' 		=> date('Y-m-d'),
							'batch_number' 		=> $this->input->post('batch_number'),
							'package_type' 		=> $this->input->post('package_type'),
							'service_type' 		=> $service_type,
							'company_division'	=> $this->data['division'],
							'current_status' 	=> 'Submitted',
							'company_code' 		=> $company[0]->company_code,
							'other_company' 	=> $company[0]->company_name,
							'other_address' 	=> $company[0]->company_address,
							'other_location' 	=> $company[0]->company_location,
							'other_country' 	=> $company[0]->company_country,
							'other_cperson' 	=> $company[0]->contact_person,
							'other_contactno' 	=> $company[0]->company_contact,
							'other_mobileno' 	=> $company[0]->company_mobile,
							'height' 			=> 0,
							'width' 			=> 0,
							'length' 			=> 0,
							'to_company' 		=> $rowData[0][8],
							'to_cperson' 		=> $rowData[0][9],
							'to_address' 		=> $rowData[0][10],
							'to_contactno' 		=> $rowData[0][11],
							'to_mobileno' 		=> $rowData[0][12],
							'to_city' 			=> $rowData[0][13],
							'to_location' 		=> $rowData[0][14],
							'to_country' 		=> $rowData[0][15],
							'payment_type' 		=> 'Account(A/c)',
							'reference_number' 	=> trim($rowData[0][22]),
							'item_description' 	=> $rowData[0][23],
							'special_instruction' =>$rowData[0][24],
							'consignee_latitude' =>$rowData[0][25],
							'consignee_longitude' =>$rowData[0][26],
							'delivery_prefered_time' =>$rowData[0][27],
							'excel_type' 		=> '1',
							'courier_charge' => $rate

						);


						if(!empty($rowData[0][0]))
							$data_part['from_company']= $rowData[0][0];
						else
							$data_part['from_company']= $company[0]->company_name;

						if(!empty($rowData[0][1]))
							$data_part['from_cperson']= $rowData[0][1];
						else
							$data_part['from_cperson']= $company[0]->contact_person;

						if(!empty($rowData[0][2]))
							$data_part['from_cperson']= $rowData[0][2];
						else
							$data_part['from_address']= $company[0]->company_address;

						if(!empty($rowData[0][3]))
							$data_part['from_contactno']= $rowData[0][3];
						else
							$data_part['from_contactno']= $company[0]->company_contact;

						if(!empty($rowData[0][4]))
							$data_part['from_mobileno']= $rowData[0][4];
						else
							$data_part['from_mobileno'] = $company[0]->company_mobile;

						if(!empty($rowData[0][5]))
							$data_part['from_city']= $rowData[0][5];
						else
							$data_part['from_city']= $company[0]->company_city;

						if(!empty($rowData[0][6]))
							$data_part['from_location']= $rowData[0][6];
						else
							$data_part['from_location']= $company[0]->company_location;

						if(!empty($rowData[0][7]))
							$data_part['from_country']= $rowData[0][7];
						else
							$data_part['from_country']= $company[0]->company_country;

						if(!empty($rowData[0][17])){
							$data_part['currency_code']= strtoupper($rowData[0][17]);
						}
						else
							$data_part['currency_code']= $this->data['currency_code'];

						if(!empty($rowData[0][18])){
							$data_part['return_service_ncnd']= 1;
							$data_part['ncnd_amount']= $rowData[0][18];
						}
						else{
							$data_part['return_service_ncnd']= 0;
							$data_part['ncnd_amount']= 0;
						}

						if(!empty($rowData[0][19]))
							$data_part['pieces']= $rowData[0][19];
						else
							$data_part['pieces']= 1;

						if(!empty($rowData[0][20]))
							$data_part['weight']= $rowData[0][20];
						else
							$data_part['weight'] = 0.5;

						if(!empty($rowData[0][21]))
							$data_part['volume_weight']= $rowData[0][21];
						else
							$data_part['volume_weight'] = 0.5;

						if($rowData[0][20] > $rowData[0][21]) 
							$data_part['chargable_weight'] = $rowData[0][20];
                        else 
                        	$data_part['chargable_weight'] = $rowData[0][21];

						$data=array_merge($data,$data_part);
// var_dump($data); exit();
						$this->base_model->add('tbl_excel_uploads',$data);
						$data="";

					}

				}

				unlink($file_path); 

				//----------------------------------------------------------------

				$schedule = $this->input->post('schedule');

				

				if($schedule==1){

					$this->db->select('route_location,route_code');

					$this->db->from('tbl_route_master');

					$this->db->where("route_status", "1");

					$this->db->order_by("route_id", "asc");

					$employee_route=$this->db->get()->result();


					foreach($employee_route as $row){

						$json_locations = json_decode($row->route_location, true);

						 foreach($json_locations as $locations) {

							if(strcmp(trim($locations) , trim($company[0]->company_location))==0) {

								$this->db->select('e.employee_code');

								$this->db->from('tbl_employee_master e');

								$this->db->join('tbl_route_master r','r.route_code = e.employee_route','left');

								$this->db->where('r.route_code',$row->route_code);

								$courier_boy = $this->db->get()->result();

							} 

						}

					}

					

					if(empty($courier_boy)) {

						$employee_code = '';

					}

					else{

						$employee_code = $courier_boy[0]->employee_code;

					}

					$prefix=$this->data['pickup-prefix'].date('ym').'-'.date('d');

					$where = array('date(booking_date)'=>date('Y-m-d'));

					$this->db->select('*');

					$this->db->from('tbl_pickup_master');

					$this->db->where($where);		

					$number=$this->db->count_all_results();

					$suffix=str_pad($number+1, 3, '0', STR_PAD_LEFT);

					$data = array(

						'pickup_number' => $prefix.$suffix,

						'company_code' =>$company[0]->company_code,

						'booking_date' => date('Y-m-d',strtotime($this->input->post('pickup_date'))).' '.date('H:i:s',strtotime($this->input->post('pickup_time'))),

						'pickup_date' =>date('Y-m-d',strtotime($this->input->post('pickup_date'))).' '.date('H:i:s',strtotime($this->input->post('pickup_time'))),

						'from_company' => $company[0]->company_name,

						'from_address' => $company[0]->company_address,

						'from_location'=> $company[0]->company_location,

						'from_country' => $company[0]->company_country,

						'from_cperson' => $company[0]->contact_person,

						'from_contactno' => $company[0]->company_contact,

						'to_company' => '',

						'to_address' => '',

						'to_location' => '',

						'to_country' => '',

						'to_cperson' => '',

						'to_contactno' => '',

						'other_company' => $company[0]->company_name,

						'other_address' => $company[0]->company_address,

						'other_location' => $company[0]->company_location,

						'other_country' => $company[0]->company_country,

						'other_cperson' => $company[0]->contact_person,

						'other_contactno' => $company[0]->company_contact,

						'package_type' => '',

						'service_type' => 'CN',

						'weight' => $this->input->post('weight'),

						'pieces' => $this->input->post('pieces'),

						'height' => 0,

						'width' => 0,

						'length' => 0,

						'item_description' => $this->input->post('item_description'),

						'special_instruction' => '',

						'vehicle_type' => $this->input->post('vehicle_type'),

						'company_division' => $this->data['division'],

						'current_status' =>'Submitted',

						'courier_boy' =>$employee_code ,

					);



					$this->base_model->add('tbl_pickup_master',$data);

					$lastid = $this->db->insert_id();



					//$this->send_mail($lastid);



					$data='';

					$data = array(

						'pickup_number' =>  $prefix.$suffix,

						'status_datetime' =>date('Y-m-d',strtotime($this->input->post('pickup_date'))).' '.date('H:i:s',strtotime($this->input->post('pickup_time'))),

						'location' => 'Customer Location',

						'pickup_status' => 'Submitted',

						'status_details' =>'Pickup Request Received',



					);

					$this->base_model->add('tbl_pickup_status',$data);

					$data='';

					if(!empty($employee_code)){

						$data = array(

							'jobDate'=>date('Y-m-d'),

							'referenceNo'		=>$prefix.$suffix,

							'runsheetNo'		=>'ON'.$suffix,

							'employeeCode'		=>$employee_code,

							'pickupCompany'		=>$company[0]->company_name,

							'pickupAddress'		=>$company[0]->company_address,

							'pickupContact'		=>$company[0]->company_contact,

							'pickupCPerson'		=>$company[0]->contact_person,

							'pickupLocation'	=>$company[0]->company_location,

							'deliveryAddress'	=>'',

							'deliveryContact'	=>'',

							'deliveryCPerson'	=>'',

							'jobDetails'		=>'Weight:'.$this->input->post('weight').', Pieces :'.$this->input->post('pieces').', Desc :'.$this->input->post('item_description'),

							'paymentMode'		=>'',

							'accountNo'			=>$company[0]->company_code,

							'companyDivision'	=>$company[0]->company_division

						);

						$this->db->insert('mobile_pickup',$data);

						$data='';

						$data = array(

							'pkpstatus_datetime'=>date('Y-m-d H:i:s'),

							'current_status' => 'Scheduled',

							'status_details' => 'Scheduled for Collection with Courier',

						);

						$this->base_model->update('tbl_pickup_master',$data,array('pickup_id'=>$lastid));

						$data='';

						$data = array(

							'pickup_number' =>  $prefix.$suffix,

							'status_datetime' =>date('Y-m-d H:i:s'),

							'location' => 'Courier Location',

							'pickup_status' => 'Scheduled',

							'status_details' =>'Scheduled for Collection with Courier',

						);

						$this->base_model->add('tbl_pickup_status',$data);

					}	

				}

				//----------------------------------------------------------------
				
                $this->session->set_flashdata('error', $error);
				
				$this->session->set_flashdata('success', 'Uploaded data successfully'); 

				redirect(base_url().'uploads/excel-upload');
				

			}

		}

		//$this->output->enable_profiler(TRUE);

	}




	function excel_upload_international($offset=0){

		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.css');

		$this->template->addJs(base_url().'assets/dist/js/jquery-ui-timepicker-addon.js');

		$this->template->addJs(base_url().'assets/plugins/iCheck/icheck.min.js');

		$this->template->addJs(base_url().'assets/dist/js/autotab.js');

		$this->template->addJs(base_url().'assets/dist/js/booking.min.js');

		$this->template->addJs(base_url().'assets/dist/js/pickup.js');

		// $this->load->library('excel');

		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());

		$this->breadcrumb->add('Uploads', base_url().'index');

		$this->breadcrumb->add('Excel Upload', base_url().'excel_upload_international');		

		$this->data['breadcrumb']=$this->breadcrumb->output();

		$this->data['main_title']='Bulk Upload';

		$data['company']=$this->base_model->get('tbl_company_master');

		$package_type=$this->base_model->edit('tbl_package_type',array('package_type_id'=>$this->input->post('package_type')));

		$data['package']=$this->base_model->edit('tbl_package_type',array('service_type<>'=>'CN'));
                       
		$where = array('excel_type'=>'1','service_type<>'=>'CN','company_division'=>$this->data['division']);
				
		$data['vehicle']=$this->base_model->get('tbl_vehicle_master');

		$this->load->library('pagination');

		$config['base_url'] = base_url().'/uploads/excel-upload/excel_upload_international';

		$config['total_rows'] = $this->base_model->get_count('tbl_excel_uploads',$where,'','');

		$config['per_page'] = $data['per_page'] = 300;

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

		$this->pagination->initialize($config);

		$data['pagination'] = $this->pagination->create_links();

		$data['edit'][0]->batch_number = 'BATCH-'.date('Ymdhis');
		
		$data['result'] =$this->base_model->get_paged_list('tbl_excel_uploads',$where,'','booking_id desc',$config['per_page'],$offset);
		           
		$config_validation=array(
			array('field' => 'package_type','label' => 'Package Type','rules' => 'trim|required|xss_clean'),
			array('field' => 'batch_number','label' => 'Batch Number','rules' => 'trim|required|xss_clean'));          
                                                  
		if (isset($_FILES['upload_file']) && empty($_FILES['upload_file']['name'])){
    		$vaildation[]=array('field' => 'upload_file','label' => 'Upload Photo','rules' => 'required');
		}
		else

			$vaildation[]=array();

		$config_validation=array_merge($config_validation,$vaildation);

        $this->form_validation->set_rules($config_validation);

		if ($this->form_validation->run() == FALSE) {

			$this->template->view('excel_uploads/bulk_upload_international',$this->data,$data);

		} else {

			$batch = $this->base_model->get_count('tbl_booking',array('batch_number'=>$this->input->post('batch_number')),'','');

			if($batch > 0){

				$this->session->set_flashdata('error', 'Batch Number exists'); 

				redirect(base_url().'uploads/excel-upload');

			}

			$upload_path = $this->data['excel-upload-path'];
			
			
			$config = array(          

			  'upload_path'     => $upload_path,

			  'allowed_types'   => '*',

			  'overwrite'       => TRUE,

			  'max_size'        => "1000KB");

			$this->load->library('upload');

 			$this->upload->initialize($config);

			if (!$this->upload->do_upload('upload_file')) {

				$this->session->set_flashdata('error', $this->upload->display_errors()); 

				$this->template->view('excel_uploads/bulk_upload_international',$this->data,$data);

			} else {

				if($this->input->post('service_type')==1)
					$service_type='CN';
				else
					$service_type='ME';
				
				$error = '';   

				/*$countrycde = array();
				$country_array = $this->base_model->get('tbl_country');
				foreach ($country_array as $country_city){
					$countrycde[]= trim(strtolower($country_city->iso));
				}*/    

				$file_data = $this->upload->data();
				     
				$file_path =  $upload_path.$file_data['file_name'];
                                                                
				$excel_obj = IOFactory::load($file_path);   

				$cell_collection = $excel_obj->getActiveSheet();

				$highestRow = $cell_collection->getHighestRow(); 
                                              
				$highestColumn = $cell_collection->getHighestColumn(); 
				                       
				/*$countryarray = array();
				for($j = 2; $j <= $highestRow; $j++) {
					$checkcountry=$cell_collection->rangeToArray('A'. $j .':'.$highestColumn .$j,NULL,TRUE,FALSE);
					 
					if(!empty($checkcountry[0][13]))
					{
						$countryarray[]  = trim($checkcountry[0][13]);
					}
				}
				
				$me = 1;
				
				foreach($countryarray as $cityval) {
					
					if (!in_array(strtolower(trim($cityval)), $countrycde)) {
						$error = $cityval.' Country Code Exist in row number '.$me.' , '; 
						$this->session->set_flashdata('error', $error);
						redirect(base_url().'uploads/excel-upload_international');
					}
				$me++; }*/

				$company=$this->base_model->edit('tbl_company_master',array('company_id'=>$this->input->post('company_id')));
				
				for ($row = 2; $row <=$highestRow; $row++) {

$rowData=$cell_collection->rangeToArray('A'. $row .':'.$highestColumn .$row,NULL,TRUE,FALSE);
//$country_name = $this->base_model->get_fields('tbl_country',array('country'),array('iso'=>trim($rowData[0][13])));

					if(!empty($rowData[0][7])){

						$data = array(

							'booking_date' => date('Y-m-d H:i:s'),

							'pickup_date' =>date('Y-m-d'),

							'batch_number' => $this->input->post('batch_number'),

							'package_type' => $this->input->post('package_type'),

							'service_type' =>$service_type,

							'company_division' => $this->data['division'],

							'current_status' => 'Submitted',

							'company_code' => $company[0]->company_code,

							'other_company' => $company[0]->company_name,

							'other_address' => $company[0]->company_address,

							'other_location' => $company[0]->company_location,

							'other_country' => $company[0]->company_country,

							'other_cperson' => $company[0]->contact_person,

							'other_contactno' => $company[0]->company_contact,

							'other_mobileno' => $company[0]->company_mobile,

							'height' =>0,

							'width' => 0,

							'length' => 0,

							'to_company' => $rowData[0][7],

							'to_cperson' =>  $rowData[0][8],

							'to_address' => $rowData[0][9],

							'to_contactno' => $rowData[0][10],

							'to_mobileno' => $rowData[0][11],

							'to_location' => $rowData[0][12],

							'to_country' => $rowData[0][13],

							'payment_type' => 'Account(A/c)',

							'reference_number' => trim($rowData[0][20]),

							'item_description' =>$rowData[0][21],

							'special_instruction' =>$rowData[0][22],

							'excel_type' => '1',
							'consignee_latitude' =>$rowData[0][23],
							'consignee_longitude' =>$rowData[0][24],
							'delivery_prefered_time' =>$rowData[0][25],

						);


						if(!empty($rowData[0][0]))

							$data_part['from_company']= $rowData[0][0];

						else

							$data_part['from_company']= $company[0]->company_name;

							

						if(!empty($rowData[0][1]))

							$data_part['from_cperson']= $rowData[0][1];

						else

							$data_part['from_cperson']= $company[0]->contact_person;

							

						if(!empty($rowData[0][2]))

							$data_part['from_cperson']= $rowData[0][2];

						else

							$data_part['from_address']= $company[0]->company_address;

							

						if(!empty($rowData[0][3]))

							$data_part['from_contactno']= $rowData[0][3];

						else

							$data_part['from_contactno']= $company[0]->company_contact;

							

						if(!empty($rowData[0][4]))

							$data_part['from_mobileno']= $rowData[0][4];

						else

							$data_part['from_mobileno'] = $company[0]->company_mobile;

							

						if(!empty($rowData[0][5]))

							$data_part['from_location']= $rowData[0][5];

						else

							$data_part['from_location']= $company[0]->company_location;

							

						if(!empty($rowData[0][6]))

							$data_part['from_country']= $rowData[0][5];

						else

							$data_part['from_country']= $company[0]->company_country;


						if(!empty($rowData[0][15])){

							$data_part['currency_code']= strtoupper($rowData[0][15]);

						}

						else

							$data_part['currency_code']= $this->data['company_currency'];

						

						if(!empty($rowData[0][16])){

							$data_part['return_service_ncnd']= 1;

							$data_part['ncnd_amount']= $rowData[0][16];

						}

						else{

							$data_part['return_service_ncnd']= 0;

							$data_part['ncnd_amount']= 0;

						}


						if(!empty($rowData[0][17]))

							$data_part['pieces']= $rowData[0][17];

						else

							$data_part['pieces']= 1;

						if(!empty($rowData[0][18]))

							$data_part['weight']= $rowData[0][18];

						else

							$data_part['weight'] = 0.5;

						if(!empty($rowData[0][19]))
							$data_part['volume_weight']= $rowData[0][19];
						else
							$data_part['volume_weight'] = 0.5;

						if($rowData[0][18] > $rowData[0][19]) 
							$data_part['chargable_weight'] = $rowData[0][18];
                        else 
                        	$data_part['chargable_weight'] = $rowData[0][19];
						
						$data=array_merge($data,$data_part);
                                          
						$this->base_model->add('tbl_excel_uploads',$data);

						$data="";

					}

				$country_name = ''; }

				unlink($file_path); 

				//----------------------------------------------------------------

				$schedule = $this->input->post('schedule');

				

				if($schedule==1){

					$this->db->select('route_location,route_code');

					$this->db->from('tbl_route_master');

					$this->db->where("route_status", "1");

					$this->db->order_by("route_id", "asc");

					$employee_route=$this->db->get()->result();


					foreach($employee_route as $row){

						$json_locations = json_decode($row->route_location, true);

						 foreach($json_locations as $locations) {

							if(strcmp(trim($locations) , trim($company[0]->company_location))==0) {

								$this->db->select('e.employee_code');

								$this->db->from('tbl_employee_master e');

								$this->db->join('tbl_route_master r','r.route_code = e.employee_route','left');

								$this->db->where('r.route_code',$row->route_code);

								$courier_boy = $this->db->get()->result();

							} 

						}

					}

					

					if(empty($courier_boy)) {

						$employee_code = '';

					}

					else{

						$employee_code = $courier_boy[0]->employee_code;

					}

					$prefix=$this->data['pickup-prefix'].date('ym').'-'.date('d');

					$where = array('date(booking_date)'=>date('Y-m-d'));

					$this->db->select('*');

					$this->db->from('tbl_pickup_master');

					$this->db->where($where);		

					$number=$this->db->count_all_results();

					$suffix=str_pad($number+1, 3, '0', STR_PAD_LEFT);

					$data = array(

						'pickup_number' => $prefix.$suffix,

						'company_code' =>$company[0]->company_code,

						'booking_date' => date('Y-m-d',strtotime($this->input->post('pickup_date'))).' '.date('H:i:s',strtotime($this->input->post('pickup_time'))),

						'pickup_date' =>date('Y-m-d',strtotime($this->input->post('pickup_date'))).' '.date('H:i:s',strtotime($this->input->post('pickup_time'))),

						'from_company' => $company[0]->company_name,

						'from_address' => $company[0]->company_address,

						'from_location'=> $company[0]->company_location,

						'from_country' => $company[0]->company_country,

						'from_cperson' => $company[0]->contact_person,

						'from_contactno' => $company[0]->company_contact,

						'to_company' => '',

						'to_address' => '',

						'to_location' => '',

						'to_country' => '',

						'to_cperson' => '',

						'to_contactno' => '',

						'other_company' => $company[0]->company_name,

						'other_address' => $company[0]->company_address,

						'other_location' => $company[0]->company_location,

						'other_country' => $company[0]->company_country,

						'other_cperson' => $company[0]->contact_person,

						'other_contactno' => $company[0]->company_contact,

						'package_type' => '',

						'service_type' => 'ME',

						'weight' => $this->input->post('weight'),

						'pieces' => $this->input->post('pieces'),

						'height' => 0,

						'width' => 0,

						'length' => 0,

						'item_description' => $this->input->post('item_description'),

						'special_instruction' => '',

						'vehicle_type' => $this->input->post('vehicle_type'),

						'company_division' => $this->data['division'],

						'current_status' =>'Submitted',

						'courier_boy' =>$employee_code ,

					);



					$this->base_model->add('tbl_pickup_master',$data);

					$lastid = $this->db->insert_id();



					//$this->send_mail($lastid);



					$data='';

					$data = array(

						'pickup_number' =>  $prefix.$suffix,

						'status_datetime' =>date('Y-m-d',strtotime($this->input->post('pickup_date'))).' '.date('H:i:s',strtotime($this->input->post('pickup_time'))),

						'location' => 'Customer Location',

						'pickup_status' => 'Submitted',

						'status_details' =>'Pickup Request Received',



					);

					$this->base_model->add('tbl_pickup_status',$data);

					$data='';

					if(!empty($employee_code)){

						$data = array(

							'jobDate'=>date('Y-m-d'),

							'referenceNo'		=>$prefix.$suffix,

							'runsheetNo'		=>'ON'.$suffix,

							'employeeCode'		=>$employee_code,

							'pickupCompany'		=>$company[0]->company_name,

							'pickupAddress'		=>$company[0]->company_address,

							'pickupContact'		=>$company[0]->company_contact,

							'pickupCPerson'		=>$company[0]->contact_person,

							'pickupLocation'	=>$company[0]->company_location,

							'deliveryAddress'	=>'',

							'deliveryContact'	=>'',

							'deliveryCPerson'	=>'',

							'jobDetails'		=>'Weight:'.$this->input->post('weight').', Pieces :'.$this->input->post('pieces').', Desc :'.$this->input->post('item_description'),

							'paymentMode'		=>'',

							'accountNo'			=>$company[0]->company_code,

							'companyDivision'	=>$company[0]->company_division

						);

						$this->db->insert('mobile_pickup',$data);

						$data='';

						$data = array(

							'pkpstatus_datetime'=>date('Y-m-d H:i:s'),

							'current_status' => 'Scheduled',

							'status_details' => 'Scheduled for Collection with Courier',

						);

						$this->base_model->update('tbl_pickup_master',$data,array('pickup_id'=>$lastid));

						$data='';

						$data = array(

							'pickup_number' =>  $prefix.$suffix,

							'status_datetime' =>date('Y-m-d H:i:s'),

							'location' => 'Courier Location',

							'pickup_status' => 'Scheduled',

							'status_details' =>'Scheduled for Collection with Courier',

						);

						$this->base_model->add('tbl_pickup_status',$data);

					}	

				}

				//----------------------------------------------------------------
				
                $this->session->set_flashdata('error', $error);
				
				$this->session->set_flashdata('success', 'Uploaded data successfully'); 

				redirect(base_url().'uploads/excel-upload-international');
				

			}

		}

		//$this->output->enable_profiler(TRUE);

	}








	private function send_mail($id=''){

		$this->load->library(array('email','my_phpmailer'));

		$data=$this->base_model->edit('tbl_pickup_master',array('pickup_id'=>$id));

		$mail = new PHPMailer();

		$mail->IsHTML(true);

		$mail->isSMTP();

		$mail->SMTPAuth = true;     

		$mail->Host       = 'smtp.sendgrid.net';

		$mail->Port       = 587; 

		$mail->SMTPSecure = 'tls';  

		$mail->Username   = 'apikey';

		$mail->Password   = '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'; 

		$message = $this->load->view('pickup/pickup_tpl',array('data'=>$data),TRUE);

		$mail->SetFrom($this->data['noreply-email'], $this->data['company-name']);  

		$mail->Subject    = $this->data['pickup-mail-subject'];

		$mail->Body  = $message;

		$mail->AddAddress($this->data['company-primary-email']);

		//$mail->AddCC($this->data['company-secondary-email']);

		$mail->Send();

	}

	

	public function do_manifest(){

		$company=$this->base_model->edit('tbl_company_master',array('company_code'=>$this->input->post('company_id')));

		$this->db->select('*');

		$this->db->from('tbl_customer_manifest');

		$number=$this->db->count_all_results();

		$prefix = $company[0]->company_code;

		$suffix = str_pad($number+1, 3, '0', STR_PAD_LEFT);

		if($this->input->post("bid")){

			$booking=$this->input->post("bid");

			for($i=0;$i<count($booking);$i++){

				$excel=$this->base_model->edit('tbl_excel_uploads',array('booking_id'=>$booking[$i]));
				
				$suffix = date('mY', strtotime($excel[0]->booking_date));
				
				$get_bnumber=$this->base_model->edit('tbl_settings',array('settings_label'=>'awb-end'));

				$awb_number=$this->data['awb-prefix'].($get_bnumber[0]->settings_value+1);
				
				$rate_cal = $this->base_model->edit('tbl_company_master',array('company_code'=>$excel[0]->company_code));
			
			if($rate_cal[0]->domestic_rate_type == 1) {
				
				if($excel[0]->chargable_weight <= $rate_cal[0]->domestic_baseweight) {
				    $courier_charge = $rate_cal[0]->domestic_rate;
				} else {
					$net_weight = $excel[0]->chargable_weight - $rate_cal[0]->domestic_baseweight;
					$net_weight = $net_weight * $rate_cal[0]->domestic_additional_rate;
					$net_weight = $net_weight + $rate_cal[0]->domestic_rate;
					$courier_charge = $net_weight; 
				}
				
			    } else {
				
				$package_code = $this->base_model->get_fields('tbl_package_type',array('package_code'),array('package_type'=>$excel[0]->package_type));
				
				$this->db->select('price_id,amount,additional_weight,additional_rate,weight_from,weight_to');
				$this->db->from('tbl_price');             
				$this->db->where('company_code',$excel[0]->company_code);
				$this->db->where('from_city',strtolower($excel[0]->from_location));
				$this->db->where('to_city',strtolower($excel[0]->to_location));
				$this->db->where('service_type',$package_code[0]->package_code);
				$price = $this->db->get()->result();

				if($excel[0]->chargable_weight >= $price[0]->weight_from &&  $excel[0]->chargable_weight <= $price[0]->weight_to) {
				$courier_charge =  $price[0]->amount; 
				}
				else if($excel[0]->chargable_weight > $price[0]->weight_to) {
				$weight = ceil($excel[0]->chargable_weight) - $price[0]->weight_to;
				$weight = $weight * $price[0]->additional_rate;
				$courier_charge = $weight + $price[0]->amount;
				} else {    
				$courier_charge = 0;
				}             
			}
				

				$data = array(

					'booking_number' => $awb_number,

					'batch_number' => $excel[0]->batch_number,

					'reference_number' => $excel[0]->reference_number,

					'company_code' => $excel[0]->company_code,

					'pickup_date' =>date('Y-m-d',strtotime($excel[0]->pickup_date)).' '.date('H:i:s'),

					'booking_date' =>date('Y-m-d',strtotime($excel[0]->booking_date)).' '.date('H:i:s'),

					'pickup_time' => '',

					'from_company' => $excel[0]->from_company,

					'from_address' => $excel[0]->from_address,

					'from_location' => $excel[0]->from_location,

					'from_country' => $excel[0]->from_country,

					'from_cperson' => $excel[0]->from_cperson,

					'from_contactno' => $excel[0]->from_contactno,

					'from_mobileno' => $excel[0]->from_mobileno,

					'to_company' => $excel[0]->to_company,

					'to_address' => $excel[0]->to_address,

					'to_location' => $excel[0]->to_location,
					'to_city' 	=> $excel[0]->to_city,
					'to_country' => $excel[0]->to_country,

					'to_cperson' => $excel[0]->to_cperson,

					'to_contactno' => $excel[0]->to_contactno,

					'to_mobileno' => $excel[0]->to_mobileno,

					'other_company' => $excel[0]->other_company,

					'other_address' => $excel[0]->other_address,

					'other_location' => $excel[0]->other_location,

					'other_country' => $excel[0]->other_country,

					'other_cperson' => $excel[0]->other_cperson,

					'other_contactno' => $excel[0]->other_contactno,
					
					'payment_type' => 'Account(A/c)',

					'package_type' => $excel[0]->package_type,

					'service_type' => $excel[0]->service_type,

					'weight' => $excel[0]->weight,

					'volume_weight' => $excel[0]->volume_weight,

				    'chargable_weight' => $excel[0]->chargable_weight,

					'pieces' => $excel[0]->pieces,

					'height' => $excel[0]->height,

					'width' => $excel[0]->width,

					'length' => $excel[0]->length,

					'item_description' => $excel[0]->item_description,

					'special_instruction' => $excel[0]->special_instruction,

					'return_service_ncnd' => $excel[0]->return_service_ncnd,

					'ncnd_amount' => $excel[0]->ncnd_amount,
					
					'courier_charge' => $courier_charge,

					'currency_code' => $excel[0]->currency_code,

					'company_division' => $excel[0]->company_division,

					'mawb_number' => $prefix.'-'.$suffix,

					'current_status' => 'Submitted',

					'status_datetime' => $excel[0]->booking_date,
					
					'payment_mode' => 3,
					
					'account_type' => 0,
					'consignee_longitude' =>$excel[0]->consignee_longitude,
					'consignee_latitude' =>$excel[0]->consignee_latitude,
					'delivery_prefered_time' =>$excel[0]->delivery_prefered_time,
					'courier_charge' => $excel[0]->courier_charge

				);

				$this->base_model->add('tbl_booking',$data);

				$data='';
				

				$data = array('settings_value' => $get_bnumber[0]->settings_value+1);

				$this->base_model->update('tbl_settings',$data,array('settings_label'=>'awb-end'));

				$data='';

				$data = array(

					'booking_number' =>  $awb_number,

					'status_datetime' =>date('Y-m-d',strtotime($excel[0]->booking_date)).' '.date('H:i:s'),

					'location' => 'Customer Location',

					'courier_status' => 'Submitted',

					'status_details' =>'Shipment at Collection Point',

				);
				
				$this->load->model('status_model');
				$this->status_model->insert_status($suffix,$data);
				// $this->base_model->add('tbl_ship_status',$data);

				$data='';

				$this->base_model->delete('tbl_excel_uploads',array('booking_id'=>$booking[$i]));

				$awb[]=$awb_number;

		$rate_cal =''; $net_weight ='';  $weight = ''; $courier_charge=''; $price = '';  }

			$data = array(       

				'mawb_number' => $prefix.'-'.$suffix,

				'company_code' => $company[0]->company_code,

				'manifest_date' =>date('Y-m-d H:i:s'),

			);

			$this->base_model->add('tbl_customer_manifest',$data);

			$data="";

			$this->session->set_userdata('mawb',$prefix.'-'.$suffix);

			$this->session->set_flashdata('manifest', 'Manifest created sucessfully..!'); 

			redirect(base_url().'uploads/excel-upload');

			//$this->output->enable_profiler(TRUE);

		} else {

			$this->session->set_flashdata('manifest_error', 'Please select atleast one shipment..!'); 

			redirect(base_url().'uploads/excel-upload');

		}

	}

	

	public function _address_mapping($data=''){

		$company=$this->base_model->edit('tbl_company_master',array('company_id'=>$this->session->userdata['user_logged_in']['company_id']));

		if(!empty($company[0]->address_mapping)){

			$mapped_address=json_decode($company[0]->address_mapping);

			$this->from_address = $mapped_address->company_name."\n";

			$this->from_address.= $mapped_address->contact_person."\n";

			$this->from_address.= $mapped_address->company_address."\n";

			$this->from_address.= $mapped_address->company_city."\n";

			$this->from_address.= $mapped_address->company_country;

			$this->from_contactno = $mapped_address->company_contact;

			$this->from_mobileno = $mapped_address->company_mobile;

		}else{

			$this->from_address =trim($data[0]->from_company)."\n".trim($data[0]->from_cperson)."\n".trim($data[0]->from_address)."\n".

			trim($data[0]->from_location."\n".trim(strtoupper($data[0]->from_country)));

			$this->from_contactno = $data[0]->from_contactno;

			$this->from_mobileno = $data[0]->from_mobileno;

		}

		

	}

	

	public function bulk_print($booking){

		$this->load->library('pdf');

		$this->load->library('Barcode39');

		$pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$pdf->SetCreator(PDF_CREATOR);

		$pdf->SetAuthor('TSS Smart Systems LLC');

		$pdf->SetTitle('AirwayBill');

		$pdf->SetSubject('AirwayBill');

		$pdf->SetKeywords($this->data['company-name'].', PDF, AWB Bill');

		$pdf->SetTopMargin(5);

		$pdf->SetAutoPageBreak(TRUE, 0);

		$pdf->SetFont('Helvetica', '', 8);

		$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');

		$mawb=$this->base_model->edit('tbl_booking',array('mawb_number'=>str_replace('_','-',$booking)));

		if(!empty($mawb)){

			for($i=0;$i<count($mawb);$i++){

				$data=$this->base_model->edit('tbl_booking',array('booking_number'=>$mawb[$i]->booking_number));

				if(!empty($data)){

					$pdf->AddPage('L', 'A5');

					$logo = '<img src="'.$this->data['awb-logo'].'" width="150"/>';



					$style =  array(

						'position'=>'C', 

						'border'=>0, 

						'padding'=>1, 

						'fgcolor'=>array(0,0,0), 

						'bgcolor'=>array(255,255,255), 

						'text'=>true, 

						'font'=>'helvetica', 

						'fontsize'=>12, 

						'stretchtext'=>4

					);

					$params = $pdf->serializeTCPDFtagParameters(array($data[0]->booking_number, 'C39', '', '', 80, 20, 0.4,$style, 'N'));

					$barcode = '<tcpdf method="write1DBarcode" params="'.$params.'"/>';

					/*$logo = '<img src="'.$this->data['awb-logo'].'" width="120"/>';

					$bc = new Barcode39($data[0]->booking_number);

					$bc->draw($this->data['awb-barcode-path'].$data[0]->booking_number.".gif");

					$image=$this->data['awb-barcode-path'].$data[0]->booking_number.'.gif';

					$barcode='<img src="'.$image.'" width="150"/>';*/

					

					$from_address=trim(strtoupper($data[0]->from_company));

					if(!empty($data[0]->from_cperson)){$from_address.=' / '.trim($data[0]->from_cperson).'<br/>';}

					$from_address.= substr(trim(preg_replace('/\s\s+/', ' ', $data[0]->from_address)), 0, 60).'<br/>';

					$from_address.= trim($data[0]->from_location);

					$from_address.= ','.trim($data[0]->from_country);

					

					$to_address=trim(strtoupper($data[0]->to_company));

					if(!empty($data[0]->to_cperson)){$to_address.=' / '.trim($data[0]->to_cperson).'<br/>';}

					$to_address.= substr(trim(preg_replace('/\s\s+/', ' ', $data[0]->to_address)), 0, 60).'<br/>';

					$to_address.= trim($data[0]->to_location);

					$to_address.= ','.trim($data[0]->to_country);

				

					

					if($data[0]->return_service_ncnd==1) { 

						$payment_mode= ' COD'; 

						$cod_amount= ' COD Amount '.number_format($data[0]->ncnd_amount,2,'.','');

					}else{

						$payment_mode= ''; 

						$cod_amount= '';

					}

					

					$return_service="";

					if($data[0]->return_service_do==1) { $return_service= 'Do Copy';}

					if($data[0]->return_service_material==1) { $return_service.= 'Material';}

					if($data[0]->return_service_ncnd==1) { $return_service.= '<br/>AED '.number_format($data[0]->ncnd_amount,2,'.','');}

					

					$pdf_data=array(

						'logo' => $logo,

						'address' => $this->data['awb-address'],

						'barcode'=>$barcode,

						'account_number'=> $data[0]->company_code,

						'reference_number'=> $data[0]->reference_number,

						'booking_date'=> "Booking Date :".date('d/m/Y',strtotime($data[0]->booking_date)),

						'from_address'=>$from_address,

						'to_address'=>$to_address,

						'from_contact'=>$data[0]->from_contactno,

						'to_contact'=>$data[0]->to_contactno,

						'service'=>$data[0]->package_type,

						'pieces'=>$data[0]->pieces,

						'weight'=>$data[0]->weight,

						'remarks'=>"H :".$data[0]->width."\nW :".$data[0]->width."\nL :".$data[0]->length,

						'item_description'=>substr(trim(preg_replace('/\s\s+/', ' ', $data[0]->item_description)), 0, 100),

						'special_instruction'=>substr(trim(preg_replace('/\s\s+/', ' ', $data[0]->special_instruction)), 0, 100),

						'return_service'=>$return_service,

						'payment_type'=>$data[0]->payment_type,

						'proof_of_delivery'=>"Consignment Received in Good Condition\n\nName & Signature&nbsp;&nbsp;&nbsp;&nbsp;Date & Time",

					);

					

					$tbl=$this->load->view('booking/awb_bill',array('data'=>$pdf_data),true);

					$pdf->writeHTML($tbl, true, 0, true, 0);

					//$pdf->writeHTML('<p align="center">-------------------------------------------------------------------------------------------------------</p><br/>', true, 0, true, 0);

					//$pdf->writeHTML($tbl, true, 0, true, 0);

				}

			}

		}else{

			$pdf->AddPage('L', 'A5');

			$pdf->SetFont('Helvetica', '', 25);

			$pdf->Cell(0,137, 'Sorry ... No Ariwaybill Found', 1, 1, 'C', 0, '', 0);

		}

		$pdf->lastPage();

		$pdf->Output(date('d-m-Y h-i-s').'.pdf', 'I');

	}

	

	

	public function edit($id=''){

		$data['edit']=$this->base_model->edit('tbl_excel_uploads',array('booking_id'=>$id));

		

		if($data['edit'][0]->current_status<>'Submitted')

			redirect(base_url().'booking/index');

		

		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.min.css');

		$this->template->addCss(base_url().'assets/plugins/iCheck/all.css');

		$this->template->addJs(base_url().'assets/plugins/iCheck/icheck.min.js');

		$this->template->addJs(base_url().'assets/dist/js/autotab.js');

		$this->template->addJs(base_url().'assets/dist/js/booking.js');

		

		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());

		$this->breadcrumb->add('Booking', base_url().'index');

		$this->breadcrumb->add('Create', base_url().'create');		

		$this->data['breadcrumb']=$this->breadcrumb->output();

		$this->data['main_title']='Consignment Entry';

		

		$config_validation=array(

			array('field' => 'booking_date','label' => 'Pickup date','rules' => 'trim|required|xss_clean'),

			array('field' => 'pickup_time','label' => 'Pickup time','rules' => 'trim|required|xss_clean'),

			array('field' => 'from_company','label' => 'Company','rules' => 'trim|required|xss_clean'),

			array('field' => 'from_address','label' => 'Address','rules' => 'trim|required|xss_clean'),

			array('field' => 'from_location','label' => 'Location','rules' => 'trim|required|xss_clean'),

			array('field' => 'from_cperson','label' => 'Contact Person','rules' => 'trim|required|xss_clean'),

			array('field' => 'from_contactno','label' => 'Contact Number','rules' => 'trim|required|xss_clean'),

			array('field' => 'from_country','label' => 'Country','rules' => 'trim|required|xss_clean'),

			array('field' => 'to_company','label' => 'Company','rules' => 'trim|required|xss_clean'),

			array('field' => 'to_address','label' => 'Address','rules' => 'trim|required|xss_clean'),

			array('field' => 'to_location','label' => 'Location','rules' => 'trim|required|xss_clean'),

			array('field' => 'to_cperson','label' => 'Contact Person','rules' => 'trim|required|xss_clean'),

			array('field' => 'to_contactno','label' => 'Contact Number','rules' => 'trim|required|xss_clean'),

			array('field' => 'to_country','label' => 'Country','rules' => 'trim|required|xss_clean'),

			array('field' => 'package_type','label' => 'Package Type','rules' => 'trim|required|xss_clean'),

			array('field' => 'weight','label' => 'Weight','rules' => 'trim|required|xss_clean'),

			array('field' => 'pieces','label' => 'Pieces','rules' => 'trim|required|xss_clean'),

			array('field' => 'width','label' => 'Width','rules' => 'trim|numeric|xss_clean'),

			array('field' => 'height','label' => 'Height','rules' => 'trim|numeric|xss_clean'),

			array('field' => 'length','label' => 'Length','rules' => 'trim|numeric|xss_clean'),

			array('field' => 'ncnd_amount','label' => 'Metrial Cost','rules' => 'trim|xss_clean'),

			array('field' => 'item_description','label' => 'Item Description','rules' => 'trim|xss_clean'),

			array('field' => 'special_instruction','label' => 'Special Instruction','rules' => 'trim|xss_clean'),

		);

		$this->form_validation->set_rules($config_validation);

		if ($this->form_validation->run() == FALSE) {

			$data['package']=$this->base_model->edit('tbl_package_type',array('service_type'=>'CN'));

			$data['currency']=$this->base_model->get('tbl_currencies');

			$this->template->view('excel_uploads/form',$this->data,$data);

		}else {

			$company=$this->base_model->edit('tbl_company_master',array('company_id'=>$this->session->userdata['user_logged_in']['company_id']));

			$package_type=$this->base_model->edit('tbl_package_type',array('package_type_id'=>$this->input->post('package_type')));

			

			$ncnd=$this->input->post('ncnd_amount');

			if(empty($ncnd) || $ncnd==0){

				$return_service_ncnd=0;

				$ncnd_amount=0;

			}else{

				

				$return_service_ncnd=1;

				$ncnd_amount=$this->input->post('ncnd_amount');

			}

			

			$h=$this->input->post('height');

			$w=$this->input->post('width');

			$l=$this->input->post('length');

			if(empty($h)){$h=0;} if(empty($w)){$w=0;} if(empty($l)){$l=0;}

			

			$data = array(

				'company_code' => $company[0]->company_code,

				'pickup_date' =>date('Y-m-d',strtotime($this->input->post('booking_date'))),

				'pickup_time' => $this->input->post('pickup_time'),

				'from_company' => $this->input->post('from_company'),

				'from_address' => $this->input->post('from_address'),

				'from_location' => $this->input->post('from_location'),

				'from_country' => $this->input->post('from_country'),

				'from_cperson' => $this->input->post('from_cperson'),

				'from_contactno' => $this->input->post('from_contactno'),

				'to_company' => $this->input->post('to_company'),

				'to_address' => $this->input->post('to_address'),

				'to_location' => $this->input->post('to_location'),

				'to_country' => $this->input->post('to_country'),

				'to_cperson' => $this->input->post('to_cperson'),

				'to_contactno' => $this->input->post('to_contactno'),

				'other_company' => $company[0]->company_name,

				'other_address' => $company[0]->company_address,

				'other_location' => $company[0]->company_location,

				'other_country' => $company[0]->company_country,

				'other_cperson' => $company[0]->contact_person,

				'other_contactno' => $company[0]->company_contact,

				'payment_type' => 'Account(A/c)',

				'package_type' => $package_type[0]->package_type,

				'service_type' => $package_type[0]->service_type,

				'weight' => $this->input->post('weight'),

				'pieces' => $this->input->post('pieces'),

				'height' => $h,

				'width' => $w,

				'length' => $l,

				'item_description' => $this->input->post('item_description'),

				'special_instruction' => $this->input->post('special_instruction'),

				'return_service_ncnd' => $return_service_ncnd,

				'ncnd_amount' => $ncnd_amount,

				'company_division' => $this->data['division'],

			);

			$this->base_model->update('tbl_excel_uploads',$data,array('booking_id'=>$id));

			$this->session->set_flashdata('success', 'Consigment updated sucessfully..!'); 

			redirect(base_url().'uploads/edit/'.$id);

				

		}

	}

	public function delete($id=''){

		if(!empty($id)){

			$this->base_model->delete('tbl_excel_uploads',array('booking_id'=>$id));

			echo 'Success';

		}
		else
			echo 'Failure';
	}
	
	// Excel upload by AWB number
	function excel_with_awb_upload($offset=0){
		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.css');
		$this->template->addJs(base_url().'assets/plugins/iCheck/icheck.min.js');
		$this->template->addJs(base_url().'assets/dist/js/autotab.js');
		$this->template->addJs(base_url().'assets/dist/js/booking.min.js');
		
		// $this->load->library('excel');
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Uploads', base_url().'index');
		$this->breadcrumb->add('Excel Upload with AWB', base_url().'excel_upload');		
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='Bulk Upload with AWB';

		$data['company']=$this->base_model->get('tbl_company_master');

		// $company=$this->base_model->edit('tbl_company_master',array('company_id'=>$this->session->userdata['user_logged_in']['company_id']));
		$company=$this->base_model->edit('tbl_company_master',array('company_id'=>$this->input->post('company_id')));
		// var_dump($company); exit();
		// var_dump($company[0]->company_code); exit();
		$package_type=$this->base_model->edit('tbl_package_type',array('package_type_id'=>$this->input->post('package_type')));
		$data['package']=$this->base_model->get('tbl_package_type');
		$where =array('company_code'=>$company[0]->company_code,'company_division'=>$this->data['division']);
		
		
		$this->load->library('pagination');
		$config['base_url'] = base_url().'/uploads/excel-with-awb-upload';
		$config['total_rows'] = $this->base_model->get_count('tbl_excel_uploads',$where,'','');
		$config['per_page'] = $data['per_page'] = 300;
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
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		
		$data['result'] =$this->base_model->get_paged_list('tbl_excel_uploads',$where,'','booking_id desc',$config['per_page'],$offset);
		
		$data['edit']=array();
		$data['edit'][0]=new StdClass;
		$data['edit'][0]->batch_number = $company[0]->company_code.date('Ymdhis');
		
		$config_validation=array(
			array('field' => 'package_type','label' => 'Package Type','rules' => 'trim|required|xss_clean'),
			array('field' => 'batch_number','label' => 'Batch Number','rules' => 'trim|required|xss_clean'),
		);
		if (isset($_FILES['upload_file']) && empty($_FILES['upload_file']['name'])){
    		$vaildation[]=array('field' => 'upload_file','label' => 'Upload Photo','rules' => 'required');
		}
		else
			$vaildation[]=array();
		$config_validation=array_merge($config_validation,$vaildation);
        $this->form_validation->set_rules($config_validation);
		if ($this->form_validation->run() == FALSE) {
			$this->template->view('excel_uploads/bulk_upload_awb',$this->data,$data);
		}else {

			$duplicate='';
			$batch = $this->base_model->get_count('tbl_booking',array('batch_number'=>$this->input->post('batch_number')),'','');
			if($batch > 0){
				$this->session->set_flashdata('error', 'Batch Number exists'); 
				redirect(base_url().'excel_uploads/excel-with-awb-upload');
			}
			$upload_path = $this->data['excel-upload-path'];
			// var_dump($upload_path); exit();
			$config =array(
			  'upload_path'     => $upload_path,
			  'allowed_types'   => '*',
			  'overwrite'       => TRUE,
			  'max_size'        => "1000KB",
			);
			$this->load->library('upload');
 			$this->upload->initialize($config);
			if (!$this->upload->do_upload('upload_file')) {
				$this->session->set_flashdata('error', $this->upload->display_errors()); 
				$this->template->view('excel_uploads/bulk_upload_awb',$this->data,$data);
			} else {
				if($this->input->post('service_type')==1)
					$service_type='CN';
				else
					$service_type='ME';
				
				$file_data = $this->upload->data();
				$file_path =  $upload_path.$file_data['file_name'];
				$excel_obj = IOFactory::load($file_path);
				$cell_collection = $excel_obj->getActiveSheet();
				$highestRow = $cell_collection->getHighestRow(); 
				$highestColumn = $cell_collection->getHighestColumn(); 
				for ($row = 2; $row <=$highestRow; $row++) {
					$rowData=$cell_collection->rangeToArray('A'. $row .':'.$highestColumn .$row,NULL,TRUE,FALSE);
					if(!empty($rowData[0][8]) && !empty($rowData[0][28])){

						$this->db->select('booking_number')->from('tbl_booking')->where(array('booking_number'=>$rowData[0][28]));
						$q= $this->db->get();
						$get_count = $q->num_rows();
						if($get_count==1){
							$duplicate.=$rowData[0][28].',';
						}else{
							// var_dump($rowData[0][13]); exit()

							$this->db->select('base_rate,to_weight,additional_rate');
							$this->db->from('tbl_company_domestic_rate');
							$this->db->where(array('from_city' => 'Dubai' ,'to_city' => $rowData[0][13], 'company_code' => $company[0]->company_code));

							$base_value = $this->db->get()->result();

							$data_awb[0]->weight= ceil($data_awb[0]->weight);
							if($data_awb[0]->weight <= $base_value[0]->to_weight){
								$rate = $base_value[0]->base_rate;
								
							}else{

								$bal_wieght = $data_awb[0]->weight-$base_value[0]->to_weight;
								$rate = $base_value[0]->base_rate+($bal_wieght*$base_value[0]->additional_rate);
							}

							$data = array(
								'booking_date' => date('Y-m-d H:i:s'),
								'pickup_date' =>date('Y-m-d'),
								'batch_number' => $this->input->post('batch_number'),
								'package_type' => $this->input->post('package_type'),
								'service_type' =>$service_type,
								'company_division' => $this->data['division'],
								'current_status' => 'Submitted',
								'company_code' => $company[0]->company_code,
								'other_company' => $company[0]->company_name,
								'other_address' => $company[0]->company_address,
								'other_location' => $company[0]->company_location,
								'other_country' => $company[0]->company_country,
								'other_cperson' => $company[0]->contact_person,
								'other_contactno' => $company[0]->company_contact,
								'other_mobileno' => $company[0]->company_mobile,
								'height' =>0,
								'width' => 0,
								'length' =>0,
								'to_company' => $rowData[0][8],
								'to_cperson' =>  $rowData[0][9],
								'to_address' => $rowData[0][10],
								'to_contactno' => $rowData[0][11],
								'to_mobileno' => $rowData[0][12],
								'to_city' 	=> $rowData[0][13],
								'to_location' => $rowData[0][14],
								'to_country' => $rowData[0][15],
								'payment_type' => 'Account(A/c)',
								'reference_number' => trim($rowData[0][22]),
								'item_description' =>$rowData[0][23],
								'special_instruction' =>$rowData[0][24],
								'booking_number' =>$rowData[0][28],
								'courier_charge' => $rate
							);
							if(!empty($rowData[0][0]))
								$data_part['from_company']= $rowData[0][0];
							else
								$data_part['from_company']= $company[0]->company_name;
							if(!empty($rowData[0][1]))
								$data_part['from_cperson']= $rowData[0][1];
							else
								$data_part['from_cperson']= $company[0]->contact_person;
							if(!empty($rowData[0][2]))
								$data_part['from_cperson']= $rowData[0][2];
							else
								$data_part['from_address']= $company[0]->company_address;

							if(!empty($rowData[0][3]))
								$data_part['from_contactno']= $rowData[0][3];
							else
								$data_part['from_contactno']= $company[0]->company_contact;

							if(!empty($rowData[0][4]))
								$data_part['from_mobileno']= $rowData[0][4];
							else
								$data_part['from_mobileno'] = $company[0]->company_mobile;

							if(!empty($rowData[0][5]))
								$data_part['from_city']= $rowData[0][5];
							else
								$data_part['from_city']= $company[0]->company_location;

							if(!empty($rowData[0][6]))
								$data_part['from_location']= $rowData[0][6];
							else
								$data_part['from_location']= $company[0]->company_location;

							if(!empty($rowData[0][7]))
								$data_part['from_country']= $rowData[0][7];
							else
								$data_part['from_country']= $company[0]->company_country;
							if(!empty($rowData[0][17])){
								$data_part['currency_code']= $rowData[0][17];
							}
							else
								$data_part['currency_code']= 'AED';

							if(!empty($rowData[0][18])){
								$data_part['return_service_ncnd']= 1;
								$data_part['ncnd_amount']= $rowData[0][18];
							}
							else{
								$data_part['return_service_ncnd']= 0;
								$data_part['ncnd_amount']= 0;
							}
							if(!empty($rowData[0][19]))
								$data_part['pieces']= $rowData[0][19];
							else
								$data_part['pieces']= 1;
							if(!empty($rowData[0][20]))
								$data_part['weight']= $rowData[0][20];
							else
								$data_part['weight']= 0.5;
							// var_dump($data); exit();
							
							$data=array_merge($data,$data_part);
							$this->base_model->add('tbl_booking',$data);
							$data="";
						}
					}

				}
				unlink($file_path); 
				if(!empty($duplicate))
					$duplicate =' Duplicate airwaybills - '.$duplicate;
				$this->session->set_flashdata('success', 'Uploaded data successfully');
				$this->session->set_flashdata('duplicate', $duplicate); 
				redirect(base_url().'uploads/excel-with-awb-upload');
			}
		}
		//$this->output->enable_profiler(TRUE);
	}

}