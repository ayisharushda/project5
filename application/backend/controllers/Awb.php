<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Awb extends MY_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('base_model');
		$this->_init();
	}

	private function _init(){

		$this->template->addCss(base_url().'assets/bootstrap/css/bootstrap.min.css');
		$this->template->addCss('https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');
		$this->template->addCss('https://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css');
		$this->template->addCss(base_url().'assets/dist/css/AdminLTE.min.css');
		$this->template->addCss(base_url().'assets/dist/css/skins/_all-skins.min.css');
		$this->template->addCss(base_url().'assets/select2/dist/css/select2.min.css');
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

	function index(){

		$this->awb_status_update();

	}

	function awb_status_update(){

		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.css');

		$this->template->addCss(base_url().'assets/dist/css/jquery-ui-timepicker-addon.css');

		$this->template->addJs(base_url().'assets/dist/js/jquery-ui-timepicker-addon.js');

		$this->template->addJs(base_url().'assets/dist/js/jquery-ui-sliderAccess.js');



		$this->template->addJs(base_url().'assets/dist/js/awb.js');

		

		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());

		$this->breadcrumb->add('Master', base_url().'booking/index');

		$this->breadcrumb->add('AWB Updation', base_url().'index');		

		$this->data['breadcrumb']=$this->breadcrumb->output();

		$this->data['main_title']='AWB Status Updation';

		

		$data['status']=$this->base_model->get('tbl_status_master');

		

		if($this->input->get('awb')){

			$data['edit']=$this->base_model->edit('tbl_booking',array('booking_number'=>$this->input->get('awb')));

			$data['result']=$this->base_model->edit('tbl_booking',array('booking_number'=>$this->input->get('awb')));

			$where = "(booking_number='".$this->input->get('awb')."' OR mawb_number='".$this->input->get('awb')."' OR masterwaybill_no='".$this->input->get('awb')."' OR reference_number='".$this->input->get('awb')."' )";

			$this->db->where($where);

        	$data['result']=$this->db->get('tbl_booking')->result();

			

		}else{

			$data['edit']="";

			$data['result']="";

		}

		$config_validation=array(

			array('field' => 'status_date','label' => 'Status Date','rules' => 'trim|required|xss_clean'),

			array('field' => 'status_time','label' => 'Status Time','rules' => 'trim|required|xss_clean'),

			array('field' => 'current_status','label' => 'POD Status','rules' => 'trim|required|xss_clean'),			

		);

        $this->form_validation->set_rules($config_validation);

		if ($this->form_validation->run() == FALSE) {

			$this->template->view('awb/form',$this->data,$data);

		}else {

			foreach($this->input->post('chk') as $booking_number){
			    
			    $awb_data = $this->base_model->get_fields('tbl_booking',array('booking_date'),array('booking_number'=>$booking_number));

				$suffix = date('mY', strtotime($awb_data[0]->booking_date));

				$data="";

				$data = array(

					'status_datetime' => date('Y-m-d',strtotime($this->input->post('status_date'))).' '

					.date('H:i:s',strtotime($this->input->post('status_time'))),

					'courier_status' => $this->input->post('current_status'),

					'location' => $this->input->post('location'),

					'booking_number' => $booking_number,

					'status_details' => $this->input->post('status_details'),

				);
				
				$this->load->model('status_model');
				$this->status_model->insert_status($suffix,$data);
				// $this->base_model->add('tbl_ship_status',$data);

				$data="";

				$data = array(

					'current_status' => $this->input->post('current_status'),

				);

				$this->base_model->update('tbl_booking',$data,array('booking_number'=>$booking_number));

				$data="";

			}

		$this->session->set_flashdata('success', 'Status Updation updated sucessfully..!'); 

			redirect(base_url().'awb/awb-status-update');

		}

	}

	function awb_status(){

		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.css');

		$this->template->addJs(base_url().'assets/dist/js/jquery-ui-timepicker-addon.js');

		$this->template->addJs(base_url().'assets/dist/js/jquery-ui-sliderAccess.js');



		$this->template->addJs(base_url().'assets/dist/js/awb.js');

		

		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());

		$this->breadcrumb->add('Master', base_url().'booking/index');

		$this->breadcrumb->add('AWB Status', base_url().'index');		

		$this->data['breadcrumb']=$this->breadcrumb->output();

		$this->data['main_title']='AWB Status';

		

		

		

		if($this->input->get('awb')){
		    
		    $awb_data = $this->base_model->get_fields('tbl_booking',array('booking_date'),array('booking_number'=>$this->input->get('awb')));

			$suffix = date('mY', strtotime($awb_data[0]->booking_date));

			$where = "(booking_number='".$this->input->get('awb')."')";

			$this->db->where($where);

			$data['result']=$this->db->get('tbl_booking')->result();
			
			$data['payout_count'] = $this->base_model->get_count('tbl_invoice_items',array('awb_number'=>$this->input->get('awb')));

			$this->db->select('i.invoice_date');
			$this->db->from('tbl_invoice_master i');
			$this->db->join('tbl_invoice_items p','p.invoice_id = i.invoice_id','left');
			$this->db->where('p.awb_number',$this->input->get('awb'));
			$data['payout_date'] = $this->db->get()->result();
			
			$where = "(booking_number='".$this->input->get('awb')."')";

			$this->db->where($where);

			$this->db->order_by('status_datetime desc');

            // $data['status']=$this->db->get('tbl_ship_status')->result();
            $data['status']=$this->db->get('tbl_tracking_'.$suffix)->result();

			

		}else{

			$data['status']="";

			$data['result']="";

		}

		//$this->output->enable_profiler(TRUE);

		$this->template->view('awb/awb_status',$this->data,$data);

	}

	function tracking(){

		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.css');

		$this->template->addJs(base_url().'assets/dist/js/jquery-ui-timepicker-addon.js');

		$this->template->addJs(base_url().'assets/dist/js/jquery-ui-sliderAccess.js');



		$this->template->addJs(base_url().'assets/dist/js/awb.js');

		

		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());

		$this->breadcrumb->add('AWB', base_url().'tracking/index');

		$this->breadcrumb->add('AWB Tracking', base_url().'index');		

		$this->data['breadcrumb']=$this->breadcrumb->output();

		$this->data['main_title']='AWB Tracking';

		

		if($this->input->post('awb')){

			$where="";

			$get_track_no=str_replace(array('\n', ':', '\t',' ',chr(13)),'||',trim(ltrim($this->input->post('awb'), "0")));             

			$track_no=explode("||",$get_track_no);      

                                                   

			for($i=0;$i<count($track_no);$i++){
			    
			    $awb_data = $this->base_model->get_fields('tbl_booking',array('booking_date'),array('booking_number'=>$this->input->post('awb')));
				$suffix = date('mY', strtotime($awb_data[0]->booking_date));

				$where = "(booking_number='".trim($track_no[$i])."')";

				$this->db->where($where);

        		$data['result'][$i]=$this->db->get('tbl_booking')->result();

				            

				$this->db->where($where);

				$this->db->order_by('status_datetime desc');

				// $data['status'][$i]=$this->db->get('tbl_ship_status')->result();
				$data['status'][$i]=$this->db->get('tbl_tracking_'.$suffix)->result();

			}

			

		} else {

			$data['status']="";

			$data['result']="";

		}

		//$this->output->enable_profiler(TRUE);

		$this->template->view('awb/tracking',$this->data,$data);

	}

	

	public function bulk_status_by_scan(){

		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.css');

		

		$this->template->addJs(base_url().'assets/dist/js/jquery-ui-timepicker-addon.js');

		$this->template->addJs(base_url().'assets/dist/js/jquery-ui-sliderAccess.js');

		$this->template->addJs(base_url().'assets/plugins/select2/select2.full.min.js');

		$this->template->addJs(base_url().'assets/dist/js/awb.js');

		

		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());

		$this->breadcrumb->add('AWB', base_url().'tracking/index');

		$this->breadcrumb->add('AWB Tracking', base_url().'index');		

		$this->data['breadcrumb']=$this->breadcrumb->output();

		$this->data['main_title']='AWB Inscan';

		

		//$data['edit']=$this->base_model->edit('tbl_booking',array('current_status<>'=>'Delivered'));

		

		$this->template->view('awb/bulk_update_by_scan',$this->data,$data='');

	}

	

	public function delete($id=''){

		if(!empty($id)){

			$this->base_model->delete('tbl_ship_status',array('status_id'=>$id));

			echo 'Success';

		}

		else

			echo 'Failure';

	}

	

	public function bulk_status_delete(){

		$status_id= $this->input->post('status');

		$booking_number= $this->input->post('booking_number');

		if(count($status_id)<>0){

			foreach($status_id as $id){

				$this->base_model->delete('tbl_ship_status',array('status_id'=>$id));

			}

		}

		redirect(base_url().'awb/awb-status?awb='.$booking_number);

	}
	
	
	
	
	
		
	public function pod($id=""){
		$this->load->library('pdf');
		$data=$this->base_model->edit('tbl_booking',array('booking_id'=>$id));
		if(!empty($data)){
			
			$pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
			// set document information
			$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('TSS Smart Systems LLC');
			$pdf->SetTitle('AirwayBill - '.$data[0]->booking_number);
			$pdf->SetSubject('AirwayBill');
			$pdf->SetKeywords('TEAM Express, PDF, POD');
			$pdf->SetTopMargin(5);
			$pdf->SetLeftMargin(5);
			$pdf->SetRightMargin(6);
			$pdf->SetAutoPageBreak(TRUE, 0);
			$pdf->SetFont('Helvetica', '', 9);
			$pdf->SetFont('dejavusans', '', 10);
			$lg = Array();
			$lg['a_meta_charset'] = 'UTF-8';
			$lg['a_meta_language'] = 'ar';
			$pdf->setLanguageArray($lg);
			
			$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');
			$pdf->AddPage('P', 'A4');
			
			$service_type=$this->base_model->edit('tbl_package_type',array('service_type'=>$data[0]->service_type));
		
			//$image=base_url().'assets/images/team-logo-pdf.jpg';
			$logo = '<img src="'.$this->data['awb-logo'].'" width="'.$this->data['awb_logo_awb_size'].'"/>';
			
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
		
			//$this->_address_mapping($data);
			$from_address=$data[0]->from_company."\n".trim($data[0]->from_cperson)."\n".$data[0]->from_address."\n".trim($data[0]->from_location)."\n".trim(strtoupper($data[0]->from_country));
			$to_address=$data[0]->to_company."\n".trim($data[0]->to_cperson)."\n".$data[0]->to_address."\n".trim($data[0]->to_location)."\n".trim(strtoupper($data[0]->to_country));
			
			//$pod=$this->base_model->edit('tbl_customerid_list',array('awb_number'=>$data[0]->booking_number));
			
			$pod_path = $this->data['app-path'].'images/'.date('Y',strtotime($data[0]->booking_date)).'/'.date('m',strtotime($data[0]->booking_date)).'/';
			
			//echo $pod_path; exit;
			
			$pdf_data=array(
				'logo' 				=> $logo,
				'barcode'			=> $barcode,
				'account_number'	=> $data[0]->company_code,
				'reference_number'	=> $data[0]->reference_number,
				'booking_date'		=> "Booking Date :".date('d/m/Y',strtotime($data[0]->booking_date)),
				'from_address'		=> $from_address,
				'from_contactno'	=> $data[0]->from_contactno,
				'from_mobileno'		=> $data[0]->from_mobileno,
				'to_address'		=> $to_address,
				'to_contactno'		=> $data[0]->to_contactno,
				'to_mobileno'		=> $data[0]->to_mobileno,
				'delivered_date'	=> $data[0]->received_date,
				'delivered_time'	=> $data[0]->received_time,
				'receiver_name'		=> $data[0]->receiver_name,
				'pod_image_1'		=> $pod_path.$data[0]->booking_number.'-1.png',
				'pod_image_2'		=> $pod_path.$data[0]->booking_number.'-2.png',
				'pod_image_signature'=>$pod_path.$data[0]->booking_number.'.png',
			);
			
			$tbl=$this->load->view('awb/pod_bill',array('data'=>$pdf_data),true);
			$pdf->writeHTML($tbl, true, 0, true, 0);
			
			$pdf->lastPage();
			$pdf->Output($data[0]->booking_number.'.pdf', 'I');
		}
	}
	


public function bulk_print(){
		$this->load->library('pdf');
		$pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('TSS Smart Systems LLC');
		$pdf->SetTitle('AirwayBill');
		$pdf->SetSubject('AirwayBill');
		$pdf->SetKeywords($this->data['company-name'].', PDF, AWB Bill');
		$pdf->SetTopMargin(5);
		$pdf->SetLeftMargin(5);
		$pdf->SetRightMargin(6);
		$pdf->SetAutoPageBreak(TRUE, 0);
		$pdf->SetFont('Helvetica', '', 9);
		$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');
		if($this->input->post("bid")){
			$booking=$this->input->post("bid");
			for($i=0;$i<count($booking);$i++){
				$data=$this->base_model->edit('tbl_booking',array('booking_id'=>$booking[$i]));
				if(!empty($data)){
					$pdf->AddPage('L', 'A5');
					$service_type=$this->base_model->edit('tbl_package_type',array('service_type'=>$data[0]->service_type));
					//$logo = '<img src="'.$this->data['awb-logo'].'" width="50"/>';

					$logo = '<img src="'.$this->data['awb-logo'].'" width="'.$this->data['awb_logo_awb_size'].'"/>';
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
					if(!empty($data[0]->currency_code)) { $currency_code = $data[0]->currency_code;} else{ $currency_code = $this->data['base_company'][0]->company_curreny;}

					if(!empty($data[0]->ncnd_amount)) { $return_service = '<br/>'.$currency_code.' '.number_format($data[0]->ncnd_amount,2,'.','');}
					$pdf_data=array(
						'logo' 				=> $logo,
						'address' 			=> $this->data['awb-address'],
						'barcode'			=> $barcode,
						'account_number'	=> $data[0]->company_code,
						'reference_number'	=> $data[0]->reference_number,
						'booking_date'		=> "Booking Date :".date('d/m/Y',strtotime($data[0]->booking_date)),
						'origin'			=> $data[0]->from_location,
						'destination'		=> $data[0]->to_location,
						'from_address'		=> $from_address,
						'to_address'		=> $to_address,
						'from_contact'		=> $data[0]->from_contactno,
						'from_mobile'		=> $data[0]->from_mobileno,
						'to_contact'		=> $data[0]->to_contactno,
						'to_mobile'			=> $data[0]->to_mobileno,
						'service'			=> $data[0]->package_type,
						'pieces'			=> $data[0]->pieces,
						'weight'			=> $data[0]->weight,
						'volume_weight'		=> $data[0]->volume_weight,
						'width' 			=> "W :".$data[0]->width,
						'height' 			=> "H :".$data[0]->height,
						'length' 			=> "L :".$data[0]->length,
						'remarks'			=> "H :".$data[0]->width."\nW :".$data[0]->width."\nL :".$data[0]->length,
						'item_description'	=>substr(trim(preg_replace('/\s\s+/', ' ', $data[0]->item_description)), 0, 600),
						'special_instruction'=>substr(trim(preg_replace('/\s\s+/', ' ', $data[0]->special_instruction)), 0, 600),
						'return_service'	=>$return_service,
						'payment_type'		=>$data[0]->payment_type,
						'proof_of_delivery'	=>"Consignment Received in Good Condition\n\nName & Signature&nbsp;&nbsp;&nbsp;&nbsp;Date & Time",
						'pod_image_1'=>'',
						'pod_image_2'=>'',
						'pod_image_signature'=>'',
					);
					$tbl=$this->load->view('awb/awb_bill',array('data'=>$pdf_data),true);
					$pdf->writeHTML($tbl, true, 0, true, 0);
				}
				$return_service = ''; 
			}
		}else{
			$pdf->AddPage('L', 'A5');
			$pdf->SetFont('Helvetica', '', 25);
			$pdf->Cell(0,137, 'Sorry ... No Airwaybill Found', 1, 1, 'C', 0, '', 0);
		}
		$pdf->lastPage();
		$pdf->Output(date('d-m-Y h-i-s').'.pdf', 'D');
	}

	

public function awb_multi_status_update(){

	$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.css');
	$this->template->addCss(base_url().'assets/dist/css/jquery-ui-timepicker-addon.css');
	$this->template->addJs(base_url().'assets/dist/js/jquery-ui-timepicker-addon.js');
	$this->template->addJs(base_url().'assets/dist/js/jquery-ui-sliderAccess.js');
	$this->template->addJs(base_url().'assets/dist/js/pod.js');
	$this->template->addCss(base_url().'assets/plugins/iCheck/all.css');
	$this->template->addCss(base_url().'assets/dist/css/inscan.css');
	$this->template->addJs(base_url().'assets/plugins/iCheck/icheck.min.js');
	$this->template->addJs(base_url().'assets/dist/js/pickup.js');
	$this->template->addhJs(base_url().'assets/dist/js/jquery.min.m.js');
	$this->template->addhJs(base_url().'assets/dist/js/selectize.js');
	$this->template->addhJs(base_url().'assets/dist/js/index.js');
	$this->template->addJs(base_url().'assets/dist/js/inscan.js');

	$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
	$this->breadcrumb->add('Multi Status Update', base_url().'awb/multple_status_upload');
	$this->data['breadcrumb']=$this->breadcrumb->output();
	$this->data['main_title']='Multiple Status Update';
	// $this->load->library('excel');

	$config_validation = array(

		array('field' => 'delivery_date','label' => 'Date','rules' => 'trim|required|xss_clean'),

		array('field' => 'delivery_time','label' => 'Time','rules' => 'trim|required|xss_clean'),

		array('field' => 'location','label' => 'Location','rules' => 'trim|required|xss_clean'),

		array('field' => 'status_details','label' => 'Status Details','rules' => 'trim|required|xss_clean'),

		array('field' => 'status_name','label' => 'Current Status','rules' => 'trim|required|xss_clean')); 

	                                       

	$data['currentstatus'] = $this->base_model->edit('tbl_status_master', array('status_type_status = ' => 1));

	

	$this->form_validation->set_rules($config_validation);

	if ($this->form_validation->run() == FALSE) {

		$this->template->view('awb/awb_multiple',$this->data,$data);

	} else {

	

		$outscantype = $this->input->post('pickup');

		if($outscantype == 1){

			$upload_path = $this->data['excel-upload-path']."awbexcel";

			    $config = array(           

				'upload_path'    => $upload_path,

				'allowed_types'  => 'xlsx|csv|xls',

				'overwrite'      => TRUE,

				'max_size'       => "1000KB",

				'encrypt_name'   => TRUE);

				                            

		$this->load->library('upload');       

		$this->upload->initialize($config);

		if (!$this->upload->do_upload('upload_file')) {

			$this->session->set_flashdata('error', $this->upload->display_errors());

			$this->template->view('awb/awb_multiple',$this->data,$data);

		}

   	 	else{

			$file_data = $this->upload->data();

			$file_path =  $upload_path."/".$file_data['file_name'];

			$excel_obj = IOFactory::load($file_path);

			$cell_collection = $excel_obj->getActiveSheet();

			$highestRow = $cell_collection->getHighestRow();

			$highestColumn = $cell_collection->getHighestColumn();

			$rows = $cell_collection->toArray();

			$num_for_err='';



			for ($i=1; $i < $highestRow; $i++) {

				

				$count_box = $this->base_model->get_count('tbl_scan',array('tbl_booking'=>array('booking_number' => $rows[$i][0])));

				

				if($count_box > 0){



    				$data['chkdata'] = $this->base_model->edit('tbl_booking', $bookid);

    				if($data['chkdata'][0]->current_status == "Delivered" || $data['chkdata'][0]->current_status == "RTN-Delivered"){

						$num_for_err .= $data['chkdata'][0]->current_status . ' - ' .$rows[$i][0] . ' | ';

					}

            		else{

					$updatestatus = array('current_status' => $this->input->post('status_name'),

					'status_datetime' =>  date('Y-m-d',strtotime($this->input->post('delivery_date'))).' '

					.date('H:i:s',strtotime($this->input->post('delivery_time'))));

					$this->base_model->update('tbl_booking',$updatestatus,array('booking_id'=>$data['chkdata'][0]->booking_id));

						     

					$datashipstat = array(

						'booking_number' => $rows[$i][0],

						'status_datetime' =>  date('Y-m-d',strtotime($this->input->post('delivery_date'))).' '

						.date('H:i:s',strtotime($this->input->post('delivery_time'))),

						'courier_status' => $this->input->post('status_name'),

						'status_details' =>  $this->input->post('status_details'),

						'location' => $this->input->post('location'));

					$this->base_model->add('tbl_ship_status',$datashipstat);

				}

	     	 }

             else{

				$error_chkdata = "This AWB Number Not Available !!!";

				$num_for_err .= $error_chkdata . ' - ' .$rows[$i][0] . ' | ';

             }

        }

		$this->session->set_flashdata('success', 'Multiple Upload Process Successfull !!!');

		$this->session->set_flashdata('error', $num_for_err);

		redirect(base_url().'awb/awb-multi-status-update'); 

        }

	}

	else {



		$scan_results =  explode(',', $this->input->post('multipunch'));

		foreach ($scan_results as $itemnumber) {

		$bookid = array('booking_number' => $itemnumber);

			if($data['chkdata'] = $this->base_model->edit('tbl_booking', $bookid)){

			if($data['chkdata'][0]->current_status == "Delivered" || $data['chkdata'][0]->current_status == "RTN-Delivered"){

			$num_for_err .= $data['chkdata'][0]->current_status . ' - ' .$itemnumber . ' | ';

			} else {

		

			$updatestatus = array('current_status' => $this->input->post('status_name'),

			'status_datetime' => date('Y-m-d',strtotime($this->input->post('delivery_date'))).' '

			.date('H:i:s',strtotime($this->input->post('delivery_time'))));

			$this->base_model->update('tbl_booking',$updatestatus,array('booking_id'=>$data['chkdata'][0]->booking_id));      

				                       

			$datashipstat = array(

            'booking_number' => $itemnumber,

			'status_datetime' =>  date('Y-m-d',strtotime($this->input->post('delivery_date'))).' '

			.date('H:i:s',strtotime($this->input->post('delivery_time'))),

			'courier_status' => $this->input->post('status_name'),

			'status_details' =>  $this->input->post('status_details'),

			'location' => $this->input->post('location'));

			$this->base_model->add('tbl_ship_status',$datashipstat);

			} } else {

			$error_chkdata = "This AWB Number Not Available !!!";

			$num_for_err .= $error_chkdata . ' - ' .$itemnumber . ' | ';

			}

        }

		

		$this->session->set_flashdata('success', 'Status Updation Successfull !!!');

		$this->session->set_flashdata('error', $num_for_err);

		redirect(base_url().'awb/awb-multi-status-update');

		//$this->output->enable_profiler(TRUE);

	  }

   }

}

public function multiple_status_updation(){

	$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.css');

	$this->template->addCss(base_url().'assets/dist/css/jquery-ui-timepicker-addon.css');

	$this->template->addJs(base_url().'assets/dist/js/jquery-ui-timepicker-addon.js');

	$this->template->addJs(base_url().'assets/dist/js/jquery-ui-sliderAccess.js');

	$this->template->addJs(base_url().'assets/dist/js/pod.js');

	$this->template->addCss(base_url().'assets/plugins/iCheck/all.css');

	$this->template->addCss(base_url().'assets/dist/css/inscan.css');

	$this->template->addJs(base_url().'assets/plugins/iCheck/icheck.min.js');

	$this->template->addJs(base_url().'assets/dist/js/pickup.js');

	$this->template->addhJs(base_url().'assets/dist/js/jquery.min.m.js');

	$this->template->addhJs(base_url().'assets/dist/js/selectize.js');

	$this->template->addhJs(base_url().'assets/dist/js/index.js');

	$this->template->addJs(base_url().'assets/dist/js/inscan.js');

	$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());

	$this->breadcrumb->add('Multi Status Update', base_url().'awb/multple_status_upload');

	$this->data['breadcrumb']=$this->breadcrumb->output();

	$this->data['main_title']='Multiple Status Update';

	$data['currentstatus'] = $this->base_model->edit('tbl_status_master', array('status_type_status = ' => 1));

	$config_validation = array(

		array('field' => 'location','label' => 'Location','rules' => 'trim|required|xss_clean'),

		array('field' => 'status_details','label' => 'Status Details','rules' => 'trim|required|xss_clean'),

		array('field' => 'status_name','label' => 'Current Status','rules' => 'trim|required|xss_clean'));
	$this->form_validation->set_rules($config_validation);

	if ($this->form_validation->run() == FALSE) {

		$this->template->view('awb/awb_multiple_update', $this->data,$data);

	}
	else{

		$awbNumber = explode(',', $this->input->post('awb'));

		foreach($awbNumber as $awb){

			$awb_data = $this->base_model->get_fields('tbl_booking',array('to_location', 'to_contactno','booking_date'),array('booking_number'=>$awb));
			$suffix = date('mY', strtotime($awb_data[0]->booking_date));
// 			var_dump($suffix); exit;
			$updatestatus[] = array(
				'booking_number'	=>$awb,
				'current_status' 	=> $this->input->post('status_name'),
				'status_datetime' 	=> date('Y-m-d',strtotime($this->input->post('delivery_date'))).' '.date('H:i:s',strtotime($this->input->post('delivery_time')))
			);
			if(!empty($updatestatus)){
				// echo "hit"; exit();
				// $this->base_model->update('tbl_booking',$updatestatus,array('booking_number'=>$awb));
				$this->db->update_batch('tbl_booking', $updatestatus,'booking_number');
			}

			$datashipstat = array(

				'booking_number' => $awb,

				'status_datetime' =>  date('Y-m-d',strtotime($this->input->post('delivery_date'))).' '

				.date('H:i:s',strtotime($this->input->post('delivery_time'))),

				'courier_status' => $this->input->post('status_name'),

				'status_details' =>  $this->input->post('status_details'),

				'location' => $this->input->post('location'));
			if(!empty($datashipstat)){
			    $this->load->model('status_model');
				$this->status_model->insert_status($suffix,$datashipstat);
				// $this->base_model->add('tbl_ship_status',$datashipstat);
			}

		}
		$this->session->set_flashdata('success', 'Inscanned Sucessfully ...!!!');
// 		redirect(base_url().'awb/awb_multiple_update');
		redirect(base_url().'awb/multiple_status_updation');
	}


	
}

public function print_from_manifest($booking){
	$this->load->library('pdf');
	//$this->load->library('Barcode39');
	$pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('TSS Smart Systems LLC');
	$pdf->SetTitle('AirwayBill');
	$pdf->SetSubject('AirwayBill');
	$pdf->SetKeywords($this->data['company-name'].', PDF, AWB Bill');
	$pdf->SetTopMargin(5);
		$pdf->SetLeftMargin(5);
		$pdf->SetRightMargin(6);
	$pdf->SetAutoPageBreak(TRUE, 0);
	$pdf->SetFont('Helvetica', '', 8);
	$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');
	$mawb=$this->base_model->edit('tbl_booking',array('mawb_number'=>str_replace('_','-',$booking)));
	if(!empty($mawb)){
		for($i=0;$i<count($mawb);$i++){
			$data=$this->base_model->edit('tbl_booking',array('booking_number'=>$mawb[$i]->booking_number));
			if(!empty($data)){
				$pdf->AddPage('P', 'A4');
				$service_type=$this->base_model->edit('tbl_package_type',array('service_type'=>$data[0]->service_type));
		
				$logo = '<img src="'.$this->data['awb-logo'].'" width="'.$this->data['awb_logo_awb_size'].'"/>';
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
				
				$from_address=trim($data[0]->from_company)."\n".trim($data[0]->from_cperson)."\n".trim($data[0]->from_address)."\n".
	trim($data[0]->from_location."\n".trim(strtoupper($data[0]->from_country)));
		$to_address=$data[0]->to_company.' / '.trim($data[0]->to_cperson)."\n".$data[0]->to_address."\n".trim($data[0]->to_location)."\n".trim(strtoupper($data[0]->to_country));
			
				$return_service="";
		
		if(!empty($data[0]->currency_code)) { $currency_code = $data[0]->currency_code;} else{ $currency_code = $this->data['currency_code'];}

		//echo $this->data['currency_code']; exit;

		if(!empty($data[0]->ncnd_amount)) { $return_service = '<br/>'.$currency_code.' '.number_format($data[0]->ncnd_amount,2,'.','');}
				
				$pdf_data=array(
					'logo' 				=> $logo,
					'address' 			=> $this->data['awb-address'],
					'barcode'			=> $barcode,
					'account_number'	=> $data[0]->company_code,
					'reference_number'	=> $data[0]->reference_number,
					'booking_date'		=> "Booking Date :".date('d/m/Y',strtotime($data[0]->booking_date)),
					'origin'			=> $data[0]->from_location,
					'destination'		=> $data[0]->to_location,
					'from_address'		=> $from_address,
					'to_address'		=> $to_address,
					'from_contact'		=> $data[0]->from_contactno,
					'from_mobile'		=> $data[0]->from_mobileno,
					'to_contact'		=> $data[0]->to_contactno,
					'to_mobile'			=> $data[0]->to_mobileno,
					'service'			=> $data[0]->package_type,
					'pieces'			=> $data[0]->pieces,
					'weight'			=> $data[0]->weight,
					'volume_weight'		=> $data[0]->volume_weight,
					'width' 			=> "W :".$data[0]->width,
					'height' 			=> "H :".$data[0]->height,
					'length' 			=> "L :".$data[0]->length,
					'remarks'			=> "H :".$data[0]->width."\nW :".$data[0]->width."\nL :".$data[0]->length,
					'item_description'	=>substr(trim(preg_replace('/\s\s+/', ' ', $data[0]->item_description)), 0, 600),
					'special_instruction'=>substr(trim(preg_replace('/\s\s+/', ' ', $data[0]->special_instruction)), 0, 600),
					'return_service'	=>$return_service,
					'payment_type'		=>$data[0]->payment_type,
					'proof_of_delivery'	=>"Consignment Received in Good Condition\n\nName & Signature&nbsp;&nbsp;&nbsp;&nbsp;Date & Time",
					'pod_image_1'=>$this->data['app-path'].'/images/'.$data[0]->booking_number.'-1.png',
					'pod_image_2'=>$this->data['app-path'].'/images/'.$data[0]->booking_number.'-2.png',
					'pod_image_signature'=>$this->data['app-path'].'/images/'.$data[0]->booking_number.'.png',

				);
				$tbl=$this->load->view('awb/awb_bill',array('data'=>$pdf_data),true);
				$pdf->writeHTML($tbl, true, 0, true, 0);
				$pdf->writeHTML('<p align="center">-------------------------------------------------------------------------------------------------------</p><br/>', true, 0, true, 0);
				$pdf->writeHTML($tbl, true, 0, true, 0);
				
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

	// Check AWB Number
	function checkbookingnumber() {

		$booking_number = $this->input->post('id');

		$this->db->select(array('booking_number','company_code','to_location','from_location','package_type','pieces','from_company','to_company','to_contactno'));
		$this->db->from('tbl_booking');
		$this->db->where(array('booking_number'=>$booking_number,'inscan_status'=>1));
		$this->db->group_start();
			$this->db->where('current_status<>', 'Delivered');
			$this->db->where('current_status<>', 'RTO');
		$this->db->group_end();
		$q = $this->db->get();
		if($q->num_rows() >0){
			$companydata =$q->result();

			echo $companydata[0]->booking_number."|||".$companydata[0]->company_code."|||".$companydata[0]->to_location."|||".$companydata[0]->from_location."|||".$companydata[0]->package_type."|||".$companydata[0]->pieces."|||".$companydata[0]->from_company."|||".$companydata[0]->to_company."|||".$companydata[0]->to_contactno;
		} else{
	     	echo 1;
	    }
	}

}