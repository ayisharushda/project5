<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cron extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('base_model');
		$this->load->library(array('email','my_phpmailer'));
		$this->load->helper('file');
		$this->load->library('pdf');
		$this->pdf_path =$_SERVER['DOCUMENT_ROOT'].'/';
	}
	
	
	
	public function aging_report(){
		$this->db->start_cache();
		$this->db->select("*");
		$this->db->from('tbl_booking');
		$this->db->group_start();
		$this->db->where(array('current_status<>'=>'Delivered'));
		$this->db->or_where('current_status IS NULL');
		$this->db->group_end();
		$this->db->where(array('DATE(booking_date)<' => date('Y-m-d', strtotime("-4 days"))));
		$q = $this->db->get();
		$this->db->flush_cache();	
		$this->db->stop_cache();
		
		if($q->num_rows() >0){
			
			
				$pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
				// set document information
				$pdf->SetCreator(PDF_CREATOR);
				$pdf->SetAuthor('TSS Smart Systems LLC');
				$pdf->SetTitle('Aging Report');
				$pdf->SetSubject('Aging Report');
				$pdf->SetKeywords('PDF, Manifest');
				$pdf->SetTopMargin(10);
				$pdf->SetAutoPageBreak(TRUE, 0);
				$pdf->SetFont('Helvetica', '', 9);
				$pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');
				$pdf->AddPage('P', 'A4');
			
			$file_name = 'aging-report-'.date('Y-m-d').'.pdf';
			$pdf_data=array(
				'report_name' => 'Aging Rreport',
				'report_date'=>'Date - '.date('d/m/Y'),
			);
			$result=$q->result();
			$tbl=$this->load->view('cron/aging_pdf',array('data'=>$pdf_data,'result'=>$result),true);
			$pdf->writeHTML($tbl, true, 0, true, 0);

			
			ob_clean();
			$pdf->Output($this->pdf_path.$file_name, 'F');
			
			
			$mail = new PHPMailer();
			$mail->IsHTML(true);
			$mail->SetFrom('no-reply@karfrieght.com', 'Karfrieght');  
			$mail->Subject    = 'Aging Report - '.date('d/m/Y');
			$mail->Body      = 'Aging Report';
			$mail->AddAttachment($this->pdf_path.$file_name);
			$mail->AddAddress('sktirur@gmail.com', "Santhosh");
			$mail->Send();
			unlink($this->pdf_path.$file_name);
		}
	}
}