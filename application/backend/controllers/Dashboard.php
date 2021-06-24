<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends MY_Controller {
	function __construct(){
		parent::__construct();
		$this->_init();
		$this->load->model('base_model');
	}
	
	public function _init(){
	//Css
		$this->template->addCss(base_url().'assets/bootstrap/css/bootstrap.min.css');
		$this->template->addCss('https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');
		$this->template->addCss('https://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css');
		$this->template->addCss(base_url().'assets/dist/css/AdminLTE.min.css');
		$this->template->addCss(base_url().'assets/dist/css/skins/_all-skins.min.css');
		$this->template->addCss(base_url().'assets/plugins/iCheck/flat/blue.css');
		$this->template->addCss(base_url().'assets/plugins/morris/morris.css');
		$this->template->addCss(base_url().'assets/plugins/jvectormap/jquery-jvectormap-1.2.2.css');
		$this->template->addCss(base_url().'assets/plugins/datepicker/datepicker3.css');
		$this->template->addCss(base_url().'assets/plugins/daterangepicker/daterangepicker-bs3.css');
		$this->template->addCss(base_url().'assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css');
		
		//Js
		
		$this->template->addJs(base_url().'assets/plugins/jQuery/jQuery-2.1.3.min.js');
		$this->template->addJs(base_url().'assets/dist/js/jquery-ui.min.js');
		$this->template->addJs(base_url().'assets/bootstrap/js/bootstrap.min.js');
		$this->template->addJs('https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js');
		$this->template->addJs(base_url().'assets/plugins/sparkline/jquery.sparkline.min.js');
		$this->template->addJs(base_url().'assets/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js');
		$this->template->addJs(base_url().'assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js');
		$this->template->addJs(base_url().'assets/plugins/knob/jquery.knob.js');
		$this->template->addJs(base_url().'assets/plugins/daterangepicker/daterangepicker.js');
		$this->template->addJs(base_url().'assets/plugins/datepicker/bootstrap-datepicker.js');
		$this->template->addJs(base_url().'assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js');
		$this->template->addJs(base_url().'assets/plugins/iCheck/icheck.min.js');
		$this->template->addJs(base_url().'assets/plugins/slimScroll/jquery.slimscroll.min.js');
		$this->template->addJs(base_url().'assets/plugins/fastclick/fastclick.min.js');
		$this->template->addJs(base_url().'assets/dist/js/app.min.js');
		$this->template->addJs(base_url().'assets/dist/js/pages/dashboard.js');
	}
	public function index(){
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Dashboard', base_url().'change-password');		
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='Dashboard';
		
		$data=$this->_dashcount();
		$where = array('company_division'=>$this->data['division']);
		
		$this->db->from('tbl_booking');
		$this->db->where($where);
	    $this->db->order_by('booking_date desc');
		$this->db->limit(15,0);
		$data['recent_booking'] = $this->db->get()->result();
		
		// $this->db->from('tbl_pickup_master');
		// $this->db->where($where);
		// $this->db->order_by('booking_date desc');
		// $this->db->limit(15,0);
		// $data['pickup_booking'] = $this->db->get()->result();
		
		$this->template->view('dashboard/dashboard',$this->data,$data);
		//$this->output->enable_profiler(TRUE);
	}
	private function _dashcount(){
		$where = array('company_division'=>$this->data['division']);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->where('DATE(booking_date)',date('Y-m-d'));
		$this->db->from('tbl_booking');
		$q = $this->db->get();
		$data['total_booking']=$q->num_rows();	
		
		$this->db->select('*');
		$this->db->where($where);
		$this->db->where('DATE(booking_date)',date('Y-m-d'));
		$this->db->where('current_status','Delivered');
		$this->db->from('tbl_booking');
		$q = $this->db->get();
		$data['delivered']=$q->num_rows();	
		
		$this->db->select('*');
		$this->db->where($where);
		$this->db->where('DATE(booking_date)',date('Y-m-d'));
		$this->db->where('current_status <>','Delivered');
		$this->db->from('tbl_booking');
		$q = $this->db->get();
		$data['undelivered']=$q->num_rows();
			
		// $this->db->select('*');
		// $this->db->where($where);
		// $this->db->where('DATE(booking_date)',date('Y-m-d'));
		// $this->db->from('tbl_pickup_master');
		// $q = $this->db->get();
		// $data['pkpbooking']=$q->num_rows();
		
		// $this->db->select('*');
		// $this->db->where($where);
		// $this->db->where('DATE(booking_date)',date('Y-m-d'));
		// $this->db->where('current_status','Collected');
		// $this->db->from('tbl_pickup_master');
		// $q = $this->db->get();
		// $data['pkpcollected']=$q->num_rows();
		
		return $data;
	}
	
	public function booking_report($offest=0){
		$offset=$this->uri->segment(4);
		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.css');
		$this->template->addJs(base_url().'assets/dist/js/report.js');
		
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Dashboard', base_url().'dashboard/index');
		$this->breadcrumb->add('Booking Report', base_url().'dashboard/booking-report');
		$this->breadcrumb->add(ucfirst($this->uri->segment(3)), base_url().'dashboard/booking-report'.$this->uri->segment(3));		
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='Booking Report';
		$this->load->library('pagination');
		
	
		
		$where=array('booking_date'=>date('Y-m-d'),'company_division'=>$this->data['division']);
		if(ucfirst($this->uri->segment(3))=='Delivered')
			$where=array_merge($where,array('current_status'=>'Delivered'));
		if(ucfirst($this->uri->segment(3))=='Undelivered')
			$where=array_merge($where,array('current_status <>'=>'Delivered'));
		
		
		$config['base_url'] = base_url().'/dashboard/booking-report/'	;
		$config['total_rows'] = $this->base_model->get_count('tbl_booking','','',$where);
		$config['per_page'] = $data['per_page'] = 1000;
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
		
		$offset = $offset == 0 ? 0 : ($offset-1)*$config["per_page"];
		
		$data['result']=$this->base_model->get_paged_list('tbl_booking',$where,'','booking_date desc',$config['per_page'],$offset,$where);
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$this->template->view('dashboard/booking_report',$this->data,$data);
		//$this->output->enable_profiler(TRUE);
		
	}
	
	

	public function dashboard_count(){
		$data=$this->_dashcount();
		$this->load->view('dashboard/dashboard_count',array('data'=>$data));
	}
	
}
?>