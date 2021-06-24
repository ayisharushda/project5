<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Login extends MY_Controller {
	function __construct(){
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->model('base_model');
	}
	public function index(){
		
		
		
		if($this->session->userdata('logged_in')){
			$this->load->view('dashboard/dashboard',array('theme'=>$this->data));
			redirect(base_url().'dashboard', 'refresh');
		}

		$config_validation=array(
			 array('field' => 'username','label' => 'Username','rules' => 'trim|required|xss_clean'),
			 array('field' => 'password','label' => 'Password','rules' => 'trim|required|xss_clean'),
		);
		
        $this->form_validation->set_rules($config_validation);
		if ($this->form_validation->run() == FALSE) {
			$this->load->view('guest/login',array('theme'=>$this->data));
		}else {
			
			$data = array(
				'username' => $this->input->post('username'),
				'upassword' => md5($this->input->post('password'))
			);
			
			$result = $this->base_model->login('tbl_user',$data);
			
			
			
			
			if($result == TRUE){
				
				foreach($result as $row){
					$sess_array = array(
						'firstname' => $row->firstname,
						'user_id' => $row->user_id,
						'username' => $row->username
					);
				}
				
				$this->session->set_userdata('logged_in', $sess_array);
				
				$this->load->view('guest/login',array('theme'=>$this->data));
				redirect(base_url().'dashboard/index');
			}else{
				$this->session->set_flashdata('error', 'Invalid username and password..!'); 
				redirect(base_url().'login');
			}
		}
	}
	public function logout(){
		$this->session->unset_userdata('logged_in');
		$this->session->sess_destroy();
  	 	redirect(base_url().'login', 'refresh');
	}
}
