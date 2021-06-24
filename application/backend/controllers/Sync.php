<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sync extends MY_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->model('base_model');
		$this->load->helper('custom_helper');
		$this->_init();
	}
	
	private function _init(){
		$this->template->addCss(base_url().'assets/bootstrap/css/bootstrap.min.css');
		$this->template->addCss('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css');
		$this->template->addCss('https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css');
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
		$this->template->addJs(base_url().'assets/dist/js/sync.js');
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Booking', base_url().'booking/index');
		$this->breadcrumb->add('List', base_url().'index');		
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='List Consignment';
		
		$where = array('current_status <>'=>'Delivered');
		$where1 = array('current_status <>'=>'Submitted');
		$where2 = array('current_status <>'=>'Request Submitted');
		
		$this->load->library('pagination');
		$config['base_url'] = base_url().'/sync/index';
		$config['total_rows'] = $this->base_model->get_count('tbl_booking',$where,'','');
		$config['per_page'] = $data['per_page'] = 200;
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
		
		$fields=array('booking_id','booking_date','booking_reference','booking_number','reference_number','to_company','current_status');
		
		
		//$data['result']=$this->base_model->get_paged_list('tbl_booking',$where,'','booking_date desc',$config['per_page'],$offset);
		
		$this->db->select($fields);
		$this->db->from('tbl_booking');
		$this->db->where($where);
		$this->db->where($where1);
		$this->db->where($where2);
		
		$this->db->limit($config['per_page'],$offset);
		$data['result'] = $this->db->get()->result();
		$data['search'] = '';
		if($this->input->get_post('search')){
			$this->db->select($fields);
			$this->db->from('tbl_booking');
			$this->db->where($where);
			$this->db->like('booking_number',$this->input->get_post('search'));
			$this->db->or_like('booking_reference',$this->input->get_post('search'));
			$this->db->limit($config['per_page'],$offset);
			$data['result'] = $this->db->get()->result();
			$data['search']=$this->input->get_post('search');
			
		}
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$this->template->view('sync/list',$this->data,$data);
		//$this->output->enable_profiler(TRUE);
		
	}
	public function status_sync(){
		$data = array(
			'Status_Updated' => 1,
			'api_push_status' => 1,
			'current_status' => $this->input->post('current_status'),
			'status_details' => $this->input->post('status_details'),
			'status_datetime' => $this->input->post('status_datetime'),
		);
		$this->base_model->update('tbl_booking',$data,array('booking_id'=>$this->input->post('quick_update')));
		$data="";
		echo 'ok';
	}
	
	
}