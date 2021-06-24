<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Menu extends MY_Controller {
	
	function __construct() {
		parent::__construct(); 
		$this->load->model('base_model');
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
		$this->template->addCss('https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css');
		$this->template->addJs('https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js');
		$this->template->addJs(base_url().'assets/dist/js/menu.js');
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
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url().'backend');
		$this->breadcrumb->add('Setup', base_url().'setup/index');
		$this->breadcrumb->add('menu', base_url().'menu');
		$this->breadcrumb->add('List', base_url().'menu/index');		
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='List Menu';
		$where = array('menu_type'=>'0');
		$fields=array('*');
		$this->db->start_cache();
			$this->db->select($fields);
			$this->db->from('tbl_menu');
			$this->db->where($where);
			if($this->input->get_post('search')){
				$search = $this->input->get_post('search');
				$this->db->like('menu_name',$search);
			}
		$this->db->stop_cache();
			$get_count = $this->db->get()->num_rows();
			$this->db->order_by("menu_id", "desc");
			$this->db->limit($per_page,$offset);
			$q =  $this->db->get();
			$data['result']=  $q->result();
		$this->db->flush_cache();	
		
		$params=array(
			'base_url' 	=> base_url().'menu/index',
			'get_count' => $get_count,
			'per_page' 	=> $per_page,
			'suffix'	=> '?'.urldecode(http_build_query($_REQUEST,'', "&"))
		);
		$config = $this->pagination_config($params);
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['edit']=array(
			'search'	=>$this->input->get_post('search'),
		);
		$this->template->view('menu/list',$this->data,$data);
	}
	
	
	function create(){

		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.min.css');
		$this->template->addCss(base_url().'assets/plugins/iCheck/all.css');
		$this->template->addCss(base_url().'assets/dist/css/passwordValidation.css');
		$this->template->addJs(base_url().'assets/plugins/select2/select2.full.min.js');
		$this->template->addJs(base_url().'assets/dist/js/menu.js');
		
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Setup ', base_url().'setup');
		$this->breadcrumb->add('Menu', base_url().'menu');
		$this->breadcrumb->add('Create', base_url().'create');		
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='Menu Entry';
		
		$config_validation=array(
			array('field' => 'menu_name','label' => 'menu Name','rules' => 'trim|required|xss_clean'),
			array('field' => 'menu_icon','label' => 'Menu Icon','rules' => 'trim|required|xss_clean'),
			array('field' => 'menu_active','label' => 'Menu Active','rules' => 'trim|required|xss_clean'),
			array('field' => 'menu_category','label' => 'Menu Category','rules' => 'trim|required|xss_clean'),
			array('field' => 'status','label' => 'Status','rules' => 'trim|required|xss_clean'));
		
$this->form_validation->set_rules($config_validation);
	 	
if ($this->form_validation->run() == FALSE) {
	  $this->template->view('menu/form',$this->data,$data);
} else {
	
		$data = array(
			'menu_name' => $this->input->post('menu_name'),
			'menu_category' => $this->input->post('menu_category'),
			'menu_icon' => $this->input->post('menu_icon'),
			'menu_active' => $this->input->post('menu_active'),
			'menu_route' => $this->input->post('menu_route'),
			'menu_type' => '0',
			'status' => $this->input->post('status'));
	
			$this->base_model->add('tbl_menu',$data);
			$this->session->set_flashdata('success', 'Menu Created Successfully..!'); 
			redirect(base_url().'menu/create');
			//$this->output->enable_profiler(TRUE);
		}
	}
	
	public function edit($id=''){
		
		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.min.css');
		$this->template->addCss(base_url().'assets/plugins/iCheck/all.css');
		$this->template->addJs(base_url().'assets/plugins/select2/select2.full.min.js');
		
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Setup', base_url().'setup/index');
		$this->breadcrumb->add('menu', base_url().'menu');
		$this->breadcrumb->add('Edit', base_url().'edit');		
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='Menu Entry';
		
		$data['edit']=$this->base_model->edit('tbl_menu',array('menu_id'=>$id));
		
		$config_validation=array(
			array('field' => 'menu_name','label' => 'menu Name','rules' => 'trim|required|xss_clean'),
			array('field' => 'menu_icon','label' => 'Menu Icon','rules' => 'trim|required|xss_clean'),
			array('field' => 'menu_active','label' => 'Menu Active','rules' => 'trim|required|xss_clean'),
			array('field' => 'menu_category','label' => 'Menu Category','rules' => 'trim|required|xss_clean'),
			array('field' => 'status','label' => 'Status','rules' => 'trim|required|xss_clean'));
		
		$this->form_validation->set_rules($config_validation);
		if ($this->form_validation->run() == FALSE) {
			$this->template->view('menu/form',$this->data,$data);
      } else {
			
			$data = array(
			'menu_name' => $this->input->post('menu_name'),
			'menu_category' => $this->input->post('menu_category'),
			'menu_icon' => $this->input->post('menu_icon'),	
			'menu_active' => $this->input->post('menu_active'),
			'menu_route' => $this->input->post('menu_route'),
			'status' => $this->input->post('status'));
			
			$this->base_model->update('tbl_menu',$data,array('menu_id'=>$id));
			$this->session->set_flashdata('success', 'menu Updated Successfully..!'); 
			redirect(base_url().'menu/edit/'.$id);
		}
	}
	
	public function delete($id=''){
		if(!empty($id)){
			$this->base_model->delete('tbl_menu',array('menu_id'=>$id));
			echo 'Success';
		}
		else
			echo 'Failure';
	}
	
	
	public function test(){
		$this->db->select('*');
		$this->db->from('tbl_menu');
		$this->db->where('menu_type','0');
		$this->db->order_by('menu_id','Asc');
		$q = $this->db->get()->result();
		$i=0;
		$j=0;
		$menuItems = array();  
		foreach($q as $value){ 
			$menuItems[$i]['name'] = $value->menu_name;
			$menuItems[$i]['icon'] = $value->menu_icon;
			$menuItems[$i]['active'] = $value->menu_active;
			$menuItems[$i]['route'] = (is_null($value->menu_route)) ? '' : base_url().$value->menu_route;
			
			$this->db->select('*');
			$this->db->from('tbl_menu');
			$this->db->where('menu_type',$value->menu_id);
			$rowcount = $this->db->get();
			
			if($rowcount->num_rows() > 0) { 
			$rowresult = $rowcount->result();
		 	foreach ($rowresult as $fields) {
				$menuItems[$i]['items'][$j]['url'][] = $fields->menu_link;
				$menuItems[$i]['items'][$j]['label'] = $fields->menu_name;
				$menuItems[$i]['items'][$j]['active'] = '';
				$j++; } } 
		 	
		 $i++; }
		print_r($menuItems); exit;
	}
	
}