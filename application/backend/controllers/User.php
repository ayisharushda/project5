<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends MY_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->model('base_model');
		$this->load->helper(array('custom'));
		$this->load->library('email');
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
		
		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.css');
		
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Settings', base_url().'settings');
		$this->breadcrumb->add('User', base_url().'user/index');
		$this->breadcrumb->add('List', base_url().'index');		
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='List Users';
		
		$where = array('company_division'=>$this->data['division']);
		
		$this->load->library('pagination');
		$config['base_url'] = base_url().'/user/index';
		$config['total_rows'] = $this->base_model->get_count('tbl_user',$where,'','');
		$config['per_page'] = 150;
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
		
		//$data['result']=$this->base_model->get_paged_list('tbl_user','','','firstname asc',$config['per_page'],$offset);

		//var_dump($data['result']); exit;
		
		
		$data['search'] = '';


		if($this->input->get_post('search')){
			$search = $this->base_model->search_handler($this->input->get_post('search', TRUE));
			$config['total_rows'] = $this->base_model->get_count('tbl_user','',array('firstname'),$search);
		}
		
		$this->db->select('*');
		$this->db->from('tbl_user');
		$this->db->join('tbl_user_type as c', 'tbl_user.user_type = c.user_type_id','inner');
		if($this->input->get_post('search')){
			$search = $this->base_model->search_handler($this->input->get_post('search', TRUE));
			$config['suffix'] = '?'.urldecode(http_build_query($_GET, '', "&"));
			$data['search']=$search;
			$offset = 0;
			$this->db->like('firstname',$search);
		}
		$this->db->order_by('firstname','asc');
		$this->db->limit($config['per_page'],$offset);
		$data['result'] = $this->db->get()->result();


		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();


//echo $this->db->last_query(); exit;

		$this->template->view('users/list',$this->data,$data);
		
		//$this->output->enable_profiler(TRUE);
		
	}

	function create(){

		

		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.min.css');
		$this->template->addCss(base_url().'assets/plugins/iCheck/all.css');
		$this->template->addJs(base_url().'assets/plugins/select2/select2.full.min.js');
		
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Settings', base_url().'settings');
		$this->breadcrumb->add('User', base_url().'user/index');
		$this->breadcrumb->add('create', base_url().'Create');		
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='User Entry';
		$config_validation=array(
			array('field' => 'user_code','label' => 'User Code','rules' => 'trim|required|xss_clean'),
			array('field' => 'username','label' => 'Username','rules' => 'trim|required|xss_clean|callback_check_username'),
			array('field' => 'upassword','label' => 'Password','rules' => 'trim|required|xss_clean'),
			array('field' => 'firstname','label' => 'First Name','rules' => 'trim|required|xss_clean'),
			array('field' => 'email','label' => 'Email','rules' => 'trim|required|xss_clean|valid_email'),
			array('field' => 'active','label' => 'Status','rules' => 'trim|required|xss_clean'),
			array('field' => 'user_type','label' => 'User Type','rules' => 'trim|required|xss_clean')
			
		);
        
        $this->form_validation->set_rules($config_validation);

		if ($this->form_validation->run() == FALSE) {

			$chkadmin = array('user_type_id !=' => '1');

			$data['usertype_master']=$this->base_model->edit('tbl_user_type', $chkadmin);

			$this->template->view('users/form',$this->data,$data);
		} else {
			$data = array(
				'user_code' => $this->input->post('user_code'),
				'username' => $this->input->post('username'),
				'upassword' => md5($this->input->post('upassword')),
				'firstname' => $this->input->post('firstname'),
				'email' => $this->input->post('email'),
				'company_division' => 1,
				'active' => $this->input->post('active'),
				'user_type' => $this->input->post('user_type'),
				'display_layout' => $this->input->post('display_layout'),
				'display_skin' => $this->input->post('display_skin'),
				'register_date' =>date('Y-m-d'),
			);

			$this->base_model->add('tbl_user',$data);
			$this->session->set_flashdata('success', 'User created sucessfully..!'); 
			redirect(base_url().'user/create');
			//$this->output->enable_profiler(TRUE);
		}
	}
	
	function edit($id=''){
		$data['edit']=$this->base_model->edit('tbl_user',array('user_id'=>$id));
		
		if($data['edit']['enable_edit']==0) { redirect(base_url().'user/index');}
		
		
		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.min.css');
		$this->template->addCss(base_url().'assets/plugins/iCheck/all.css');
		$this->template->addJs(base_url().'assets/plugins/select2/select2.full.min.js');
		
		
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Settings', base_url().'settings');
		$this->breadcrumb->add('User ', base_url().'user/index');
		$this->breadcrumb->add('Edit', base_url().'edit');		
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='User Entry';
		
		$where = array('company_division_id'=>$this->data['division']);
		
		$config_validation=array(
			array('field' => 'user_code','label' => 'User Code','rules' => 'trim|required|xss_clean'),
			array('field' => 'username','label' => 'Username','rules' => 'trim|required|xss_clean|callback_check_username'),
			array('field' => 'firstname','label' => 'First Name','rules' => 'trim|required|xss_clean'),
			array('field' => 'email','label' => 'Email','rules' => 'trim|required|xss_clean|valid_email'),
			array('field' => 'active','label' => 'Status','rules' => 'trim|required|xss_clean'),
			array('field' => 'user_type','label' => 'User Type','rules' => 'trim|required|xss_clean')
		);
        $this->form_validation->set_rules($config_validation);
		if ($this->form_validation->run() == FALSE) {

			$chkadmin = array('user_type_id !=' => '1');
			$data['usertype_master']=$this->base_model->edit('tbl_user_type', $chkadmin);
			
			$this->template->view('users/form',$this->data,$data);

		}else {
			$data = array(
				'user_code' => $this->input->post('user_code'),
				'username' => $this->input->post('username'),
				'firstname' => $this->input->post('firstname'),
				'email' => $this->input->post('email'),
				'company_division' => 1,
				'active' => $this->input->post('active'),
				'user_type' => $this->input->post('user_type'),
				'display_layout' => $this->input->post('display_layout'),
				'display_skin' => $this->input->post('display_skin'),
			);
			$this->base_model->update('tbl_user',$data,array('user_id'=>$id));
			$this->session->set_flashdata('success', 'User updated sucessfully..!'); 
			redirect(base_url().'user/index');
			//$this->output->enable_profiler(TRUE);
		}
	}
	
	function mail_password($id=''){
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Settings', base_url().'settings');
		$this->breadcrumb->add('User', base_url().'index');
		$this->breadcrumb->add('Mail Password', base_url().'mail-password');		
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='Mail Password';
		
		$data['edit']=$this->base_model->edit('tbl_user',array('user_id'=>$id));
		
		$config_validation=array(
			array('field' => 'company_username','label' => 'Username','rules' => 'trim|required|xss_clean'),
			array('field' => 'company_email','label' => 'Email Id','rules' => 'trim|required|valid_email|xss_clean'),
		);
		$this->form_validation->set_rules($config_validation);
		if ($this->form_validation->run() == FALSE) {
			
			$this->template->view('users/mail_password',$this->data,$data);
			
		}else {
			$newpassword=create_password(12,true, true, false);
			$data = array(
				'upassword' =>md5($newpassword),
			);
			$this->base_model->update('tbl_user',$data,array('user_id'=>$id));
			$user=$this->base_model->edit('tbl_user',array('user_id'=>$id));
			
			$this->email->initialize(array(
				'protocol' => 'sendmail',
				'mailtype' => 'html',
				'crlf' => "\r\n",
				'newline' => "\r\n"
			));
			
			$data['key_logo']=$this->data['company-logo'];
			$data['key_username']=$user[0]->username;
			$data['key_password']=$newpassword;
			$data['key_email']=$user[0]->email;
			
			$this->email->from($this->data['noreply-email'], $this->data['company-name']);
			$this->email->to($user[0]->email);
			$message = $this->load->view('users/mail_password_tpl',array('data'=>$data),TRUE);
			$this->email->subject($this->data['settings-email-subject']);
			$this->email->message($message);
			$this->email->send();
			$this->session->set_flashdata('success', 'Password mailed successfully..!'); 
			redirect(base_url().'user/mail-password/'.$id);
		}
	}
	
	
	public function check_username($username){
		$this->db->select('*');
		$this->db->where('username',$username);
		$id= $this->uri->segment(3);
		if(!empty($id))
			$this->db->where('user_id !=',$this->uri->segment(3));
		$this->db->from('tbl_user');
		$q = $this->db->get();
		
		if ($q->num_rows()>0){
			$this->form_validation->set_message('check_username', 'The %s already exist');
			return FALSE;
		}else{
			return TRUE;
		}
	}
	
	
	public function set_permission($id=''){
		    
		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.min.css');
		$this->template->addCss(base_url().'assets/plugins/iCheck/all.css');
		$this->template->addJs(base_url().'assets/plugins/select2/select2.full.min.js');
		$this->template->addJs(base_url().'assets/dist/js/customer.js');
		                               
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('admin user', base_url().'user/set_permission');
		$this->breadcrumb->add('Edit', base_url().'user/set_permission');		
		$this->data['breadcrumb'] = $this->breadcrumb->output();
		$this->data['main_title'] = 'Admin User Set Permission';   
		$value = '';
		$permission_list = '';
				
		$this->db->select('*');
		$this->db->from('tbl_menu');
		$this->db->where(array('menu_type'=>'0','menu_category'=>'Admin','status'=>'1'));
		$this->db->order_by('menu_order','Asc');
		$menu = $this->db->get()->result();
		
		$permission_list =$this->base_model->get_fields('tbl_user',array('menu_permissions'),array('user_id'=>$id));
		$permission_list = json_decode($permission_list[0]->menu_permissions, true);
		
		$menuInner = array();
		$menukey = array();
		foreach ($permission_list as $key => $value) {
			$perm_value = array_keys($value);
			$menukey[] = $perm_value[0];
			if($perm_value[0] != '1'){
				foreach($value as $listvalue){
					$menuInner[] = $listvalue;
				}
			}
		}
		
		$main_menu = array();     
		$i = 0;   
        foreach ($menu as $items) {
		$menuList .= '<ul line-height: 2>'; 
		$menuList .= '<li><input type="checkbox"';
		if( in_array($items->menu_id, $menukey)) { 
		$menuList .= 'checked="checked"'; }
		$menuList .= 'name="main_menu['.$i.']['.$items->menu_id.']" value="'.$items->menu_id.'"><label for="exampleInputEmail1" class="">'.$items->menu_name.'</label></li>';
		// Sub Menu
		$sub_menu = $this->base_model->edit('tbl_menu',array('menu_type'=>$items->menu_id,'menu_category'=>'Admin','status'=>'1'));
		
		foreach ($sub_menu as $sub) { 
				$menuList .= '<li><ul>';                   
				$menuList .= '<li><input type="checkbox"'; 
				if (in_array($sub->menu_id, call_user_func_array('array_merge', $menuInner))) {
				$menuList .= 'checked="checked"'; }        
				$menuList .= 'name="main_menu['.$i.']['.$items->menu_id.'][]" value='.$sub->menu_id.'>
				<label for="exampleInputEmail1" class="">'.$sub->menu_name.'</label></li>';	
				$menuList .= '</ul></li>';
        }
		$menuList .= '</ul>';  
		$i++; }
		
		$data['menus'] = $menuList;
		if(!empty($this->input->post('main_menu'))){
			$main_menu = array_values($this->input->post('main_menu'));
			$result = json_encode($main_menu);
			$permission = array('menu_permissions'=>$result); 
			$this->base_model->update('tbl_user',$permission,array('user_id'=>$id));
			redirect(base_url().'user/set_permission/'.$id);
		} 
		$this->template->view('users/set_permission',$this->data,$data);
        // $this->output->enable_profiler(TRUE);		
	}
	
	
	public function delete($id=''){
		if(!empty($id)){
			$this->base_model->delete('tbl_user',array('user_id'=>$id));
			echo 'Success';
		}
		else
			echo 'Failure';
	}
	
	
}