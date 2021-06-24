<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Department extends MY_Controller {
    function __construct(){
        parent::__construct();
		$this->load->model('Base_model');
		$this->load->helper(array('custom'));
		$this->load->library('email');
		$this->load->library('pagination');
		$this->_init();
    
    }

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
	}

	public function index(){

        $this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.css');
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Setup', base_url().'setup/index');
		$this->breadcrumb->add('Company', base_url().'company');
		$this->breadcrumb->add('List', base_url().'index');		
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='List Department';
		$this->db->select('*');
		$this->db->from('tbl_department');
		if($this->input->get_post('search'))
			$this->db->where('department_name',$this->input->get_post('search'));
		$data['result'] = $this->db->get()->result();	
    	$this->template->view('department/list',$this->data,$data);
	}
	
	public function create(){
		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.css');
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Setup', base_url().'setup/index');
		$this->breadcrumb->add('Company', base_url().'company');
		$this->breadcrumb->add('List', base_url().'index');		
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='List Department';
        $config_validation = array(
            array('field'=>'department_name','label'=>'Department Name','rules'=>'trim|required')
        );
        $this->form_validation->set_rules($config_validation);
        if($this->form_validation->run()==FALSE){
            $this->template->view('department/form',$this->data,$data);
        }
        else{
            $data = array(
                'department_name' => $this->input->post('department_name') 
            );
            $this->Base_model->add('tbl_department',$data);
            $this->session->set_flashdata('success','Data inserted successfully');
            redirect(base_url().'Department/create');
        }
    }


	public function delete($id){
		if(!empty($id)){
			$this->base_model->delete('tbl_department',array('department_id'=>$id));
			echo 'Success';
		}
		else
			echo 'Failure';
	}
	public function edit($id=''){
		$this->template->addCss(base_url().'assets/dist/css/jQueryUI/jquery-ui-1.10.3.custom.css');
		$this->breadcrumb->add('<i class="fa fa-dashboard"></i> Home', base_url());
		$this->breadcrumb->add('Setup', base_url().'setup/index');
		$this->breadcrumb->add('Company', base_url().'company');
		$this->breadcrumb->add('List', base_url().'index');		
		$this->data['breadcrumb']=$this->breadcrumb->output();
		$this->data['main_title']='List Department';
		
        $config_validation =array(
			array('field'=>'department_name','label'=>'Department Name','rules'=>'trim|required'
		));
        $this->form_validation->set_rules($config_validation);
        if($this->form_validation->run()==FALSE){
            $data['edit']=$this->base_model->edit('tbl_department',array('department_id'=>$id));
            $this->template->view('department/form',$this->data,$data);
        }else{
            $data = array(
                'department_name' => $this->input->post('department_name') 
            );
            // $this->Base_model->update($data,$id);
			$this->base_model->update('tbl_department',$data,array('department_id'=>$id));
            $this->session->set_flashdata('success','Data inserted successfully');
            // redirect(base_url().'Department/');
			redirect(base_url().'department/edit/'.$id);
        }
    }
}
?>