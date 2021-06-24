<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_type extends MY_Controller {
	
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
		//optional
		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.css');
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Setup', base_url().'setup/index');
		$this->breadcrumb->add('User Type', base_url().'user_type');	
		$this->breadcrumb->add('List', base_url().'index');	  
		$this->data['breadcrumb']=$this->breadcrumb->output();

        //var_dump($breadcrumb); exit; 

		$this->data['main_title']='User Type';
		
		$this->load->library('pagination');
		$config['base_url'] = base_url().'/user_type/index';
		$config['total_rows'] = $this->base_model->count_all('tbl_user_type');
		$config['per_page'] = $data['per_page'] = 50;
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
		$data['result']=$this->base_model->get_paged_list('tbl_user_type','','','user_type_id asc',$config['per_page'],$offset);
		
		$data['search'] = '';
		if($this->input->get_post('search')){
			$config['suffix'] = '?'.http_build_query($_REQUEST, '', "&");
			$search = $this->base_model->search_handler($this->input->get_post('search', TRUE));
			$config['total_rows'] = $this->base_model->get_count('tbl_user_type','',array('user_type'),$search);
			$data['search']=$search;
			$data['result'] = $this->base_model->search('tbl_user_type',array('user_type'),$search,$config['per_page'],$offset);
		}
		
		$this->pagination->initialize($config);

		$data['pagination'] = $this->pagination->create_links();
		
		$this->template->view('setup/user_type/list',$this->data,$data);

	}



function create(){
		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.min.css');
		$this->template->addCss(base_url().'assets/plugins/iCheck/all.css');
		$this->template->addJs(base_url().'assets/plugins/select2/select2.full.min.js');
		$this->template->addJs(base_url().'assets/dist/js/user_type.js');
		
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Setup ', base_url().'index');
		$this->breadcrumb->add('User Type', base_url().'user_type');	
		$this->breadcrumb->add('Create', base_url().'create');	
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='User Type Master';
		
		$config_validation=array(
			array('field' => 'user_type','label' => 'User type','rules' => 'trim|required|xss_clean')
		);



        $this->form_validation->set_rules($config_validation);
		if ($this->form_validation->run() == FALSE) {
			$this->template->view('setup/user_type/form',$this->data,$data);
		}else {
			$data = array(
				'user_type' => $this->input->post('user_type')
			);

			$this->base_model->add('tbl_user_type',$data);
			$this->session->set_flashdata('success', 'User type created sucessfully..!'); 
			redirect(base_url().'user_type/create');
			//$this->output->enable_profiler(TRUE);
		}
		
	}
	
	function edit($id=''){
		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.min.css');
		$this->template->addCss(base_url().'assets/plugins/iCheck/all.css');
		$this->template->addJs(base_url().'assets/plugins/select2/select2.full.min.js');
		$this->template->addJs(base_url().'assets/dist/js/user_type.js');
		
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Setup ', base_url().'index');
		$this->breadcrumb->add('User Type', base_url().'user_type');	
		$this->breadcrumb->add('Edit', base_url().'index');		
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='User type Master';
		
		$data['edit']=$this->base_model->edit('tbl_user_type',array('user_type_id'=>$id));
		
		$config_validation=array(
			array('field' => 'user_type','label' => 'user_type','rules' => 'trim|required|xss_clean')
		);	
		
        $this->form_validation->set_rules($config_validation);

                

		if ($this->form_validation->run() == FALSE) {
			$this->template->view('setup/user_type/form',$this->data,$data);
		}else {
			$data = array(
				'user_type' => $this->input->post('user_type')
			);	
			

			$this->base_model->update('tbl_user_type',$data,array('user_type_id'=>$id));
			$this->session->set_flashdata('success', 'User type updated sucessfully..!'); 
			redirect(base_url().'user_type/edit/'.$id);
			
		}
		
	}
	
    

	public function delete($id=''){
		if(!empty($id)){
			$this->base_model->delete('tbl_user_type',array('user_type_id'=>$id));
			echo 'Success';
			//exit;
		}
		else
			echo 'Failure';
		    //exit;
	}


}
