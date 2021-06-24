<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Forgotpassword extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->library('email');
		$this->load->model('base_model');
		$this->load->helper('custom_helper');
	}
    
    
	public function index(){


        $config_validation=array(
			 array('field' => 'email','label' => 'Email','rules' => 'trim|required|xss_clean')
		);

        $this->form_validation->set_rules($config_validation);

		if ($this->form_validation->run() == FALSE) {
			$this->load->view('guest/forgot',array('theme'=>$this->data));
		}else {
			
			$data = array('email' => $this->input->post('email'));

			$result = $this->base_model->get_count('tbl_user',$data);

			
			if($result == TRUE){


              $id = $this->base_model->edit('tbl_user',$data);
              
              //var_dump($id);

                $data_id = array('user_id' => $id['0']->user_id);

		        //echo $id['0']->user_id; exit;
              
                  $new_password = create_password();

                 $this->email->initialize(array(
					'protocol' => 'sendmail',
					'mailtype' => 'html',
					'crlf' => "\r\n",
					'newline' => "\r\n"
				));	
				
                $this->email->from('jbr266@gmail.com', 'Admin');
				$this->email->to($this->input->post('email'));
				$message = "new password is".$new_password;
				$this->email->subject('new password');
				$this->email->message($message);
				$this->email->send();

                $data_password = array('upassword' => md5($new_password));

				if($this->base_model->update('tbl_user',$data_password,$data_id))

				{
                    
					$this->session->set_flashdata('success', 'Password successfully updated please check you mail..!'.$new_password); 
					
					$this->load->view('guest/login',array('theme'=>$this->data));

				}


			}else{
				
				$this->session->set_flashdata('error', 'Invalid username and password..!'); 
				$this->load->view('guest/forgot',array('theme'=>$this->data));

			}
		}

	}
	
}
