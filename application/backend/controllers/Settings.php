<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Settings extends MY_Controller {
	
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
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Settings', base_url().'settings');
		$this->breadcrumb->add('Home', base_url().'index');		
		$this->data['breadcrumb']=$this->breadcrumb->output();
		
		$this->data['main_title']='Settings';
		
			$this->template->view('settings/dashboard',$this->data);
		//redirect(base_url().'settings/change-password');
	}
	
	
	public function change_password(){
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Settings', base_url().'settings');
		$this->breadcrumb->add('Change Password', base_url().'change-password');		
		$this->data['breadcrumb']=$this->breadcrumb->output();
		
		$this->data['main_title']='Password Section';
		
		$config_validation=array(
			 array('field' => 'current_password','label' => 'Current Password','rules' => 'trim|required|xss_clean'),
			 array('field' => 'new_password','label' => 'New Pasword','rules' => 'trim|required|xss_clean'),
		);
        $this->form_validation->set_rules($config_validation);
		
		$data = array(
			'upassword' => md5($this->input->post('current_password')),
			'user_id' => $this->session->userdata['logged_in']['user_id'],
			'username' => $this->session->userdata['logged_in']['username'],
		);
		if ($this->form_validation->run() == FALSE) {
			$this->template->view('settings/change_password',$this->data);
		}else {
			$return_data=$this->base_model->get_count('tbl_user',$data);
			if($return_data==0){
				$this->session->set_flashdata('failure', 'Wrong Password ..!'); 
				redirect(base_url().'settings/change-password');
			}
			else{
				$data = array(
					'upassword' => md5($this->input->post('new_password')),
				);
				$this->base_model->update('tbl_user',$data,array('user_id'=>$this->session->userdata['logged_in']['user_id']));
				$this->session->set_flashdata('success', 'Password changed sucessfully..!'); 
				redirect(base_url().'settings/change-password');
			}
			//$this->output->enable_profiler(TRUE);
		}
	}
	public function layout(){
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Settings', base_url().'settings');
		$this->breadcrumb->add('Layout', base_url().'layout');		
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='Layout Section';
		
		
		$config_validation=array(
			 array('field' => 'display_layout','label' => 'Layout','rules' => 'trim|required|xss_clean'),
			 array('field' => 'display_skin','label' => 'Skin','rules' => 'trim|required|xss_clean'),
		);
        $this->form_validation->set_rules($config_validation);
		$data = array(
			'display_layout' => $this->input->post('display_layout'),
			'display_skin' => $this->input->post('display_skin'),
		);
		if ($this->form_validation->run() == FALSE) {
			$edit=$this->base_model->edit('tbl_user',array('user_id'=>$this->session->userdata['logged_in']['user_id']));
			$this->template->view('settings/layout',$this->data,$edit);
		}else {
			$this->base_model->update('tbl_user',$data, array('user_id'=>$this->session->userdata['logged_in']['user_id']));
			$this->session->set_flashdata('success', 'Layout settings updated sucessfully..!'); 
			redirect(base_url().'settings/layout');
		}
	}
}