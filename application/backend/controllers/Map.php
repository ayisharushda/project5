<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Map extends MY_Controller {
	
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
		$this->template->addCss('https://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css');
		$this->template->addCss(base_url().'assets/plugins/select2/select2.min.css');
		$this->template->addCss(base_url().'assets/dist/css/AdminLTE.min.css');
		$this->template->addCss(base_url().'assets/dist/css/skins/_all-skins.min.css');
		
		$this->template->addJs(base_url().'assets/plugins/jQuery/jQuery-2.1.3.min.js');
		$this->template->addJs(base_url().'assets/plugins/jQueryUI/jquery-ui-1.10.3.min.js');
		$this->template->addJs(base_url().'assets/bootstrap/js/bootstrap.min.js');
		$this->template->addJs(base_url().'assets/plugins/slimScroll/jquery.slimscroll.min.js');
		$this->template->addJs(base_url().'assets/plugins/fastclick/fastclick.min.js');
		
		$this->template->addJs(base_url().'assets/dist/js/site.js');
		$this->template->addJs(base_url().'assets/dist/js/app.min.js');
	}
	
	function index($offset=0){
		
		$this->template->addCss(base_url().'assets/map/assets/css/w3css.css');
		
		$this->template->addJs(base_url().'assets/map/build/polyfills.js');
		$this->template->addJs(base_url().'assets/map/build/vendor.js');
		$this->template->addJs(base_url().'assets/map/build/main.js');
		
		$this->template->view('map/list',$this->data,$data);
		//$this->output->enable_profiler(TRUE);
	}
	
	
	
}