<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Box extends MY_Controller {

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
		$this->template->addCss(base_url().'assets/dist/css/validate.css');
		$this->template->addJs(base_url().'assets/dist/js/jquery.min.js');
		$this->template->addJs(base_url().'assets/dist/js/jquery-ui.min.js');
		$this->template->addJs(base_url().'assets/plugins/slimScroll/jquery.slimscroll.min.js');
		$this->template->addJs(base_url().'assets/plugins/fastclick/fastclick.min.js');
		$this->template->addJs(base_url().'assets/bootstrap/js/bootstrap.min.js');
		$this->template->addJs(base_url().'assets/plugins/input-mask/jquery.inputmask.js');
		$this->template->addJs(base_url().'assets/plugins/input-mask/jquery.inputmask.date.extensions.js');
		$this->template->addJs(base_url().'assets/plugins/input-mask/jquery.inputmask.extensions.js');
		$this->template->addJs(base_url().'assets/dist/js/site.js');
		$this->template->addJs(base_url().'assets/dist/js/app.min.js');
		$this->template->addJs(base_url().'assets/dist/js/validate.js');
		$this->template->addJs(base_url().'assets/dist/js/box.js');
	}


public function dimention_box() {

	$package = $this->base_model->get_fields('tbl_package_type',array('package_division_value'),array('package_type'=>$this->input->post('package_type')));

	$height = explode(",",trim($this->input->post('height'), '"'));
	$width = explode(",",trim($this->input->post('width'), '"'));
	$length = explode(",",trim($this->input->post('length'), '"'));
	$vweight = explode(",",trim($this->input->post('vweight'), '"'));
    $nbox = $this->input->post('nbox');

	$responce = '';
	$responce .='<div id="popweight" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg popup-width">
		<div class="modal-content">

			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<div class="modal-body weight-pop-main">
			<div class="admin-form">

			<input type="checkbox" id="duplicate_row" name="duplicate_row" onclick="duplicate_row()">
            <label for="vehicle1" style="color:#008000">Copy First Row To other Fields</label><br>

			<div class="form-group col-md-2">
			<label class="control-label label-booking">Sl-No</label></div>
			<div class="form-group col-md-2">
            <label class="control-label label-booking">Height</label></div>
			<div class="form-group col-md-2">
			<label class="control-label label-booking">Width</label></div>
			<div class="form-group col-md-3">
			<label class="control-label label-booking">Length</label></div>
			<div class="form-group col-md-3">
			<label class="control-label label-booking">V.Weight</label></div>
			<input type="hidden" name="package_dvalue" id="package_dvalue" value="'.$package[0]->package_division_value.'">
			<input type="hidden" name="boxcount" id="boxcount" value="'.$nbox.'">';
	        $j=0; for($i=1; $i<=$nbox; $i++) { 
				
$responce .='<div id="myForm">
<div class="form-group col-md-2">
<input type="text" readonly  autocomplete="off" class="bookingbox form-control input-sm" id="bookingbox"  name="bookingbox" value='.$i.'>
</div>

<div class="form-group col-md-2 onlyRegex ">
<input type="text" onkeyup="volumetricWeight('.$j.')" autocomplete="off" class="bookingheight form-control input-sm" id="bookingheight_'.$j.'" value="'.$height[$j].'" autofocus name="bookingheight"/>
</div>

<div class="form-group col-md-2 onlyRegex ">
<input type="text" onkeyup="volumetricWeight('.$j.')"  autocomplete="off" class="bookingwidth form-control input-sm" id="bookingwidth_'.$j.'" value="'.$width[$j].'" autofocus name="bookingwidth"/>
</div>

<div class="form-group col-md-3 onlyRegex ">
<input type="text" onkeyup="volumetricWeight('.$j.')"  autocomplete="off" class="bookinglength form-control input-sm" id="bookinglength_'.$j.'" value="'.$length[$j].'" autofocus name="bookinglength"/>
</div>

<div class="form-group col-md-3 onlyRegex ">
	<input type="text" tabindex="-1" readonly autocomplete="off" class="bookingvweight form-control input-sm" id="bookingvweight_'.$j.'" value="'.$vweight[$j].'" autofocus name="bookingvweight"/>
</div>';
			$j++; } 
			$responce .='</div>
			</div>
			<div class="modal-footer popupweight">
			<div class="number-box"><b style="float: left;">Total Number Of Boxes : <span id="total_no_boxes">'.$nbox.'</span></b><br>';
			$responce .='<b style="float: left;">Total Vol. Weight : <span id="hiddenvweight">'.$this->input->post('volume_weight').'</span></b>';
			$responce .='</div><div class="number-box-button">
			<button type="type" class="btn btn-lg btn-primary" onclick="getWeightShipping();">Submit</button>
			<button type="button" class="btn btn-lg btn-danger" data-dismiss="modal">Close</button>
			</div>
			</div>
		</div>
	  </div>
    </div>
  </div>
</div>';
	 echo $responce; exit;
}

public function storeBoxEntry() {
	                        
	 $bookingvweight = str_replace('bookingvweight=', '', json_decode($this->input->post('vweight')));
	 $bookingheight = str_replace('bookingheight=', '', json_decode($this->input->post('bookingheight')));
	 $bookingwidth = str_replace('bookingwidth=', '', json_decode($this->input->post('bookingwidth')));
	 $bookinglength = str_replace('bookinglength=', '', json_decode($this->input->post('bookinglength')));
	 
	 $bookingheighthidden = str_replace('&', ',', $bookingheight);
	 $bookingwidthhidden = str_replace('&', ',', $bookingwidth);
	 $bookinglengthhidden = str_replace('&', ',', $bookinglength);
	 $bookingvweighthidden = str_replace('&', ',', $bookingvweight);
	 
	echo $bookingheighthidden."|||"
		.$bookingwidthhidden."|||"
		.$bookinglengthhidden."|||"
		.$bookingvweighthidden."|||"
		.$this->input->post('finalvolumeweight');
	exit;
} 


}