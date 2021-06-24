<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Label extends MY_Controller {

	

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

	function index($id=''){

		$this->get_label($id);

	}

	

	function get_label($id=''){

		if(empty($id))

			redirect(base_url().'booking/index');

		$this->load->library('pdf');

		$data=$this->base_model->edit('tbl_booking',array('booking_id'=>$id));

		if(!empty($data)){

			$pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

			// set document information

			$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('TSS Smart Systems LLC');
			$pdf->SetTitle('Label - '.$data[0]->booking_number);
			$pdf->SetSubject('Label');
			$pdf->SetKeywords('TSS, PDF, AWB Label');
			$pdf->SetTopMargin(2);
			$pdf->SetLeftMargin(1);
			$pdf->SetRightMargin(2);
			$pdf->SetAutoPageBreak(TRUE, 0);
			$pdf->SetFont('Helvetica', '', 9);
			$pdf->SetFont('dejavusans', '', 10);

			$lg = Array();
			$lg['a_meta_charset'] = 'UTF-8';
			$lg['a_meta_language'] = 'ar';
			$pdf->setLanguageArray($lg);
			$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');

			/*$resolution= array(101.6,165.1);

			$pdf->AddPage('P', $resolution);*/

			$logo = '<img src="'.$this->data['awb-logo'].'" width="'.$this->data['label_logo_size'].'"/>';

			$pdf->SetTextColor(0,0,0);
			$text = $pdf->serializeTCPDFtagParameters(array(65,20, strtoupper($data[0]->to_location)));

			$style =  array(
				'position'=>'L', 
				'border'=>0, 
				'padding'=>0, 
				'fgcolor'=>array(0,0,0), 
				'bgcolor'=>array(255,255,255), 
				'text'=>true, 
				'font'=>'helvetica', 
				'fontsize'=>14, 
				'stretchtext'=>4
			);

			$params = $pdf->serializeTCPDFtagParameters(array($data[0]->booking_number, 'C128', '', '', 66, 23, 0.5,$style, 'N'));

			$barcode = '<tcpdf method="write1DBarcode" params="'.$params.'" />';

			// var_dump($barcode); exit();

			if(!empty($data[0]->ncnd_amount)){

				$cod_amount= number_format($data[0]->ncnd_amount,2,'.','');

			}

			else{

				$cod_amount= '0.00';

			}

			if(empty($data[0]->currency_code)){

				$currency_code=$this->data['base_company'][0]->company_curreny;

			}else{

				$currency_code=$data[0]->currency_code;

			}



for($i=1;$i<=$data[0]->pieces;$i++){



			$this->db->select('*');
			$this->db->from('tbl_box');
			$this->db->where('booking_number',$data[0]->booking_number);
			$this->db->where('box_number',$i);
			$box = $this->db->get()->result();


			$pdf_data=array(

				'logo' => $logo,
				'barcode'=>$barcode,
				'booking_number'	=> $data[0]->booking_number,
				'account_number'	=> $data[0]->company_code,
				'reference_number'	=> $data[0]->reference_number,
				'origin'			=> strtoupper($data[0]->from_location),
				'destination'		=> strtoupper($data[0]->to_location),
				'product'			=> $data[0]->package_type,
				'booking_date'		=> date('d/m/Y',strtotime($data[0]->booking_date)),
				'from_company'		=> strtoupper($data[0]->from_company),
				'from_address'		=> strtoupper($data[0]->from_address),
				'from_cperson'		=> strtoupper($data[0]->from_cperson),
				'from_location'		=> strtoupper($data[0]->from_location),
				'from_contactno'	=> $data[0]->from_contactno,
				'from_mobileno'		=> $data[0]->from_mobileno,
				'from_location'		=> strtoupper($data[0]->from_location),
				'from_country'		=> strtoupper($data[0]->from_country),
				'to_company'		=> strtoupper($data[0]->to_company),
				'to_address'		=> strtoupper($data[0]->to_address),
				'to_location'		=> strtoupper($data[0]->to_location),
				'to_cperson'		=> strtoupper($data[0]->to_cperson),
				'to_contactno'		=> $data[0]->to_contactno,
				'to_mobileno'		=> $data[0]->to_mobileno,
				'to_location'		=> strtoupper($data[0]->to_location),
				'to_country'		=> strtoupper($data[0]->to_country),
				'services'			=> $data[0]->package_type,
				'service_type'		=> $data[0]->service_type,
				'payment_type'		=> $data[0]->payment_type,
				'box_number'        => $i,
				'box_vol_weight'    => $box[0]->box_vol_weight,
				'pieces'			=> $data[0]->pieces,
				'weight'			=> $data[0]->weight,
				'cod_amount'		=> $cod_amount,
				'currency_code'		=> $currency_code,
				'item_description'	=> $data[0]->item_description,
				'special_instruction'=> $data[0]->special_instruction,

			);
// var_dump($pdf_data); exit;


			$resolution= array(101.6,165.1);

					$pdf->AddPage('P', $resolution);



			$tbl=$this->load->view('label/view-label',array('data'=>$pdf_data),true);

			$pdf->writeHTML($tbl, true, 0, true, 0);



		}

			$pdf->lastPage();

			$pdf->Output($data[0]->booking_number.'.pdf', 'I');

			

		}

	}

	

	function bulk_label(){

		$this->load->library('pdf');

		//$this->load->library('Barcode39');

		

		$pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information

		$pdf->SetCreator(PDF_CREATOR);

		$pdf->SetAuthor('TSS Smart Systems LLC');

		$pdf->SetTitle('Bulk Teamex Label');

		$pdf->SetSubject('Teamex Label');

		$pdf->SetKeywords('TSS, PDF, AWB Bill');

		$pdf->SetTopMargin(4);

		$pdf->SetLeftMargin(1);

		$pdf->SetRightMargin(2);

		$pdf->SetAutoPageBreak(TRUE, 0);

		$pdf->SetFont('Helvetica', '', 9);

			$pdf->SetFont('dejavusans', '', 10);

			$lg = Array();

			$lg['a_meta_charset'] = 'UTF-8';

			$lg['a_meta_language'] = 'ar';

			$pdf->setLanguageArray($lg);

		$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');

		//$pdf->AddPage('p','A5');

		

		$logo = '<img src="'.$this->data['awb-logo'].'" width="'.$this->data['label_logo_size'].'"/>';

		

		if($this->input->post("bid")){

			$booking=$this->input->post("bid");

			for($i=0;$i<count($booking);$i++){

				$data=$this->base_model->edit('tbl_booking',array('booking_id'=>$booking[$i]));

				if(!empty($data)){

				//	$resolution= array(101.6,165.1);

					//$pdf->AddPage('P', $resolution);

					

					$pdf->SetTextColor(0,0,0);

					$text = $pdf->serializeTCPDFtagParameters(array(65,20, strtoupper($data[0]->to_location)));



					$style =  array(

						'position'=>'L', 
						'border'=>0, 
						'padding'=>0, 
						'fgcolor'=>array(0,0,0), 
						'bgcolor'=>array(255,255,255), 
						'text'=>true, 
						'font'=>'helvetica', 
						'fontsize'=>14, 
						'stretchtext'=>4

					);

					$params = $pdf->serializeTCPDFtagParameters(array($data[0]->booking_number, 'C128', '', '', 66, 23, 0.5,$style, 'N'));

					$barcode = '<tcpdf method="write1DBarcode" params="'.$params.'" />';

					

			

					if(!empty($data[0]->ncnd_amount)){

						$cod_amount= number_format($data[0]->ncnd_amount,2,'.','');

					}

					else{

						$cod_amount= '0.00';

					}

					if(empty($data[0]->currency_code)){

						$currency_code=$this->data['base_company'][0]->company_curreny;

					}else{

						$currency_code=$data[0]->currency_code;

					}



for($j=1;$j<=$data[0]->pieces;$j++){



	$resolution = array(101.6,165.1);

	$pdf->AddPage('P', $resolution);



	//$j = 

//$box = $this->base_model->edit('');



			$this->db->select('*');

			$this->db->from('tbl_box');

			$this->db->where('booking_number',$data[0]->booking_number);

			$this->db->where('box_number',$j);

			$box = $this->db->get()->result();



				$pdf_data=array(

				'logo' => $logo,

				'barcode'=>trim($barcode),

				'booking_number'	=> $data[0]->booking_number,

				'account_number'	=> $data[0]->company_code,

				'reference_number'	=> $data[0]->reference_number,

				'origin'			=> strtoupper($data[0]->from_location),

				'destination'		=> strtoupper($data[0]->to_location),

				'product'			=> $data[0]->package_type,

				'booking_date'		=> date('d/m/Y',strtotime($data[0]->booking_date)),

				'from_company'		=> strtoupper($data[0]->from_company),

				'from_address'		=> strtoupper($data[0]->from_address),

				'from_cperson'		=> strtoupper($data[0]->from_cperson),

				'from_location'		=> strtoupper($data[0]->from_location),

				'from_contactno'	=> $data[0]->from_contactno,

				'from_mobileno'		=> $data[0]->from_mobileno,

				'from_location'		=> strtoupper($data[0]->from_location),

				'from_country'		=> strtoupper($data[0]->from_country),

				'to_company'		=> strtoupper($data[0]->to_company),

				'to_address'		=> strtoupper($data[0]->to_address),

				'to_location'		=> strtoupper($data[0]->to_location),

				'to_cperson'		=> strtoupper($data[0]->to_cperson),

				'to_contactno'		=> $data[0]->to_contactno,

				'to_mobileno'		=> $data[0]->to_mobileno,

				'to_location'		=> strtoupper($data[0]->to_location),

				'to_country'		=> strtoupper($data[0]->to_country),

				'services'			=> $data[0]->package_type,

				'service_type'		=> $data[0]->service_type,

				'payment_type'		=> $data[0]->payment_type,

				'pieces'			=> $data[0]->pieces,

				'box_number'        => $j,

				'box_vol_weight'    => $box[0]->box_vol_weight,

				'weight'			=> $data[0]->weight,

				'cod_amount'=>$cod_amount,

				'currency_code'=>$currency_code,

				'item_description'=>$data[0]->item_description,

				'special_instruction'=>$data[0]->special_instruction);

				$tbl=$this->load->view('label/view-label',array('data'=>$pdf_data),true);

				$pdf->writeHTML($tbl, true, 0, true, 0);

$box = ''; }



				}

			}

		}

		else{

			$resolution= array(100, 100);

			$pdf->AddPage('P', $resolution);

			$pdf->SetFont('Helvetica', '', 12);

			$pdf->Cell(0,137, 'Sorry ... No Ariwaybill Found', 1, 1, 'C', 0, '', 0);

		}

		$pdf->lastPage();

		$pdf->Output(date('d-m-Y h-i-s').'.pdf', 'D');

	}

	

	function label_from_manifest($manifest=''){

		$this->load->library('pdf');

		

		

		$pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information

		$pdf->SetCreator(PDF_CREATOR);

		$pdf->SetAuthor('TSS Smart Systems LLC');

		$pdf->SetTitle('Bulk Teamex Label');

		$pdf->SetSubject('Teamex Label');

		$pdf->SetKeywords('TSS, PDF, AWB Bill');

		$pdf->SetTopMargin(2);

		$pdf->SetLeftMargin(1);

		$pdf->SetRightMargin(2);

		$pdf->SetAutoPageBreak(TRUE, 0);

		$pdf->SetFont('Helvetica', '', 8);

		$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');

		$logo = '<img src="'.$this->data['awb-logo'].'" width="'.$this->data['label_logo_size'].'"/>';

		$mawb=$this->base_model->edit('tbl_booking',array('mawb_number'=>str_replace('_','-',$manifest)));

		if(!empty($mawb)){

			for($i=0;$i<count($mawb);$i++){

				$data=$this->base_model->edit('tbl_booking',array('booking_number'=>$mawb[$i]->booking_number));

				if(!empty($data)){

					$resolution= array(101.6,165.1);

					$pdf->AddPage('P', $resolution);

					

					$pdf->SetTextColor(0,0,0);

					$text = $pdf->serializeTCPDFtagParameters(array(65,20, strtoupper($data[0]->to_location)));



					$style =  array(

						'position'=>'C', 

						'border'=>0, 

						'padding'=>0, 

						'fgcolor'=>array(0,0,0), 

						'bgcolor'=>array(255,255,255), 

						'text'=>true, 

						'font'=>'helvetica', 

						'fontsize'=>10, 

						'stretchtext'=>4

					);

					$params = $pdf->serializeTCPDFtagParameters(array($data[0]->booking_number, 'C39', '', '', 80, 12, 0.4,$style, 'N'));

					$barcode = '<tcpdf method="write1DBarcode" params="'.$params.'" />';

					

			

					if(!empty($data[0]->ncnd_amount)){

						$cod_amount= number_format($data[0]->ncnd_amount,2,'.','');

					}

					else{

						$cod_amount= '0.00';

					}

					if(empty($data[0]->currency_code)){

						$currency_code='AED';

					}else{

						$currency_code=$data[0]->currency_code;

					}

					$pdf_data=array(

						'logo' => $logo,

						'barcode'=>$barcode,

						'booking_number'	=> $data[0]->booking_number,

						'account_number'	=> $data[0]->company_code,

						'reference_number'	=> $data[0]->reference_number,

						'origin'			=> strtoupper($data[0]->from_location),

						'destination'		=> strtoupper($data[0]->to_location),

						'product'			=> $data[0]->package_type,

						'booking_date'		=> date('d/m/Y',strtotime($data[0]->booking_date)),

						'from_company'		=> strtoupper($data[0]->from_company),

						'from_address'		=> strtoupper($data[0]->from_address),

						'from_cperson'		=> strtoupper($data[0]->from_cperson),

						'from_location'		=> strtoupper($data[0]->from_location),

						'from_contactno'	=> $data[0]->from_contactno,

						'from_mobileno'		=> $data[0]->from_mobileno,

						'from_location'		=> strtoupper($data[0]->from_location),

						'from_country'		=> strtoupper($data[0]->from_country),

						'to_company'		=> strtoupper($data[0]->to_company),

						'to_address'		=> strtoupper($data[0]->to_address),

						'to_location'		=> strtoupper($data[0]->to_location),

						'to_cperson'		=> strtoupper($data[0]->to_cperson),

						'to_contactno'		=> $data[0]->to_contactno,

						'to_mobileno'		=> $data[0]->to_mobileno,

						'to_location'		=> strtoupper($data[0]->to_location),

						'to_country'		=> strtoupper($data[0]->to_country),

						'services'			=> $data[0]->package_type,

						'service_type'		=> $data[0]->service_type,

						'payment_type'		=> $data[0]->payment_type,

						'pieces'			=> $data[0]->pieces,

						'weight'			=> $data[0]->weight,

						'cod_amount'=>$cod_amount,

						'currency_code'=>$currency_code,

						'item_description'=>$data[0]->item_description,

						'special_instruction'=>$data[0]->special_instruction,



					);

					$tbl=$this->load->view('label/view-label',array('data'=>$pdf_data),true);

					$pdf->writeHTML($tbl, true, 0, true, 0);

				}

			}

		}

		else{

			$pdf->AddPage('L', 'A5');

			$pdf->SetFont('Helvetica', '', 25);

			$pdf->Cell(0,137, 'Sorry ... No Ariwaybill Found', 1, 1, 'C', 0, '', 0);

		}

		$pdf->lastPage();

		$pdf->Output(date('d-m-Y h-i-s').'.pdf', 'D');

	}

	

	

}