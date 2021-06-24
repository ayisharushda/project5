<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sub_menu extends MY_Controller {
	
	function __construct() {
		parent::__construct(); 
		$this->load->model('base_model');
		$this->load->helper('my_helper');
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
		$this->breadcrumb->add('sub menu', base_url().'sub_menu');
		$this->breadcrumb->add('List', base_url().'sub_menu/index');		
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='List sub menu';
		
		$where = array('menu_type <>'=>'0');
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
			'base_url' 	=> base_url().'sub_menu/index',
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
		$this->template->view('sub_menu/list',$this->data,$data);
	}
	
	function create(){
    
		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.min.css');
		$this->template->addCss(base_url().'assets/plugins/iCheck/all.css');
		$this->template->addJs(base_url().'assets/plugins/select2/select2.full.min.js');
			$this->template->addJs(base_url().'assets/dist/js/menulist.js');
		
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Sub menu', base_url().'sub_menu');
		$this->breadcrumb->add('Create', base_url().'create');		
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title'] = 'Sub Menu Entry';  
		
		$config_validation=array(
			array('field' => 'menu_name','label' => 'Sub Menu Name','rules' => 'trim|required|xss_clean'),
			array('field' => 'menu_link','label' => 'Sub Menu Link','rules' => 'trim|required|xss_clean'),
			array('field' => 'menu_id','label' => 'Menu Name','rules' => 'trim|required|xss_clean'),
			array('field' => 'sub_menu_category','label' => 'menu Category','rules' => 'trim|required|xss_clean'),
			array('field' => 'status','label' => 'Status','rules' => 'trim|required|xss_clean'));
		
$this->form_validation->set_rules($config_validation);
		
$this->db->select('*');
$this->db->from('tbl_menu');
$this->db->where(array('menu_type'=>'0'));
$data['menu'] = $this->db->get()->result(); 
	 	
if ($this->form_validation->run() == FALSE) {
	  $this->template->view('sub_menu/form',$this->data,$data);
} else {
	    
		$data = array(
			'menu_name' => $this->input->post('menu_name'),
			'menu_link' => $this->input->post('menu_link'),
			'menu_type' => $this->input->post('menu_id'),
			'menu_category' => $this->input->post('sub_menu_category'),
			'status' => $this->input->post('status'));
	
			$this->base_model->add('tbl_menu',$data);
			$this->session->set_flashdata('success', 'Sub Menu Created Successfully..!'); 
			redirect(base_url().'sub_menu/create');
			//$this->output->enable_profiler(TRUE);
		}
	}
	
	public function edit($id=''){
		
		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.min.css');
		$this->template->addCss(base_url().'assets/plugins/iCheck/all.css');
		$this->template->addJs(base_url().'assets/plugins/select2/select2.full.min.js');
		$this->template->addJs(base_url().'assets/dist/js/menulist.js');
		
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Sub Menu', base_url().'sub_menu');
		$this->breadcrumb->add('Edit', base_url().'edit');		
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='Sub Menu Entry';            
		
		$data['edit']=$this->base_model->edit('tbl_menu',array('menu_id'=>$id));
		$this->db->select('*');
		$this->db->from('tbl_menu');
		$this->db->where(array('menu_type'=>'0'));
		$data['menu'] = $this->db->get()->result(); 
		
		$config_validation=array(
			array('field' => 'menu_name','label' => 'Sub Menu Name','rules' => 'trim|required|xss_clean'),
			array('field' => 'menu_link','label' => 'Sub Menu Link','rules' => 'trim|required|xss_clean'),
			array('field' => 'menu_name','label' => 'Menu Name','rules' => 'trim|required|xss_clean'),
			array('field' => 'sub_menu_category','label' => 'menu Category','rules' => 'trim|required|xss_clean'),
			array('field' => 'status','label' => 'Status','rules' => 'trim|required|xss_clean'));
		
		$this->form_validation->set_rules($config_validation);
		if ($this->form_validation->run() == FALSE) {
			$this->template->view('sub_menu/form',$this->data,$data);
      } else {
			
			$data = array(
			'menu_name' => $this->input->post('menu_name'),
			'menu_link' => $this->input->post('menu_link'),
			'menu_type' => $this->input->post('menu_id'),
			'menu_category' => $this->input->post('sub_menu_category'),
			'status' => $this->input->post('status'));
			
			$this->base_model->update('tbl_menu',$data,array('menu_id'=>$id));
			$this->session->set_flashdata('success', 'Sub Menu Updated Successfully..!'); 
			redirect(base_url().'sub_menu/edit/'.$id);
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
	
	public function get_menu(){
		
		$data = array();
		$this->db->where(array('menu_category' => $this->input->post('sub_menu_category'),'menu_type'=>'0'));
		
		$q = $this->db->get('tbl_menu');
		
		if( $q->num_rows() > 0 ){
			foreach($q->result() as $row){
				$data[]=$row;       
			}                        
			echo json_encode($data);    
		}
	  //$this->output->enable_profiler(TRUE);
	}
	
	public function data_sort(){
		
		//echo 'aaa'; exit;
		
		$data = array('menu_order' =>$this->input->post('sortnum'));
		$this->base_model->update('tbl_menu',$data,array('menu_id'=>$this->input->post('id')));
		echo 'Success';
	}

	
}