<section class="content">
  <div class="row">
    <!-- left column -->  
    <div class="col-md-12">
    <?php   if($this->session->flashdata('success')) {  ?>
                <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                	<?php  echo $this->session->flashdata('success');  ?>
                </div>
                <?php } ?>
      <div class="box box-primary">
        <div class="box-header">
        
        <div class="box-header">
          <h3 class="box-title">&nbsp;</h3>

          <div class="box-tools">
            <a href="" class="btn btn-success btn-xs" id="test" value="test" data-toggle="modal" data-target="#modal-primary" onclick = "GetSelected()">Bulk Check In</a>
            <a href="<?php echo base_url()?><?php echo $this->uri->segment(1);?>/attendence_form" class="btn btn-success btn-xs">Bulk Export</a>
          </div>

        </div>
          <ul class="nav nav-tabs">
            <li class="active">
            <a href="#checkin" data-toggle="tab" aria-expanded="false">Check In</a>
            </li>
            <li>
            <a href="#checkout" data-toggle="tab" aria-expanded="false">Check Out</a>
            </li>
            
          </ul>
        </div>
        <div class="tab-content">
          <div class="tab-pane active" id="checkin">
          <form role="form" method="post" autocomplete="off" action="" enctype="multipart/form-data">
                  <div class="box-body">
                  <div class="form-group col-md-3 <?php echo (form_error('employee_name')? 'has-error':'')?>">
                      <label for="exampleInputEmail1" class="">Employee Name*</label>
                      <select class="form-control" placeholder="Employee Name" name="employee_name" id="employee_name">
                         <option value="">Employee Name</option>
                       <?php foreach($data['employee_name'] as $employee_name){?>
                        <option value="<?php echo $employee_name->employee_name ?>" <?php echo (isset($data['edit'][0]->employee_department) && $employee_name->employee_name == $data['edit'][0]->employee_name) ? 'selected="selected"' : "" ?>
                        <?php if($employee_name->employee_name == set_value("employee_name")) { echo 'selected="selected"';}  ?>><?php echo $employee_name->employee_name?></option>
                        <?php } ?>
                        </select> 
                    </div>
                    <div class="clearfix"><!-- --></div>
                    <div class="form-group col-md-4 <?php echo (form_error('check_in_time')? 'has-error':'')?>">
					          <label for="exampleInputEmail1" class="container">Punch Time*</label>
                    <input type="text" class="form-control datetimepicker" id="check_in_time"  name="check_in_time" placeholder="work end Time"value="<?php echo  isset($data['edit'][0]->work_end_time) ? date('h:i A',strtotime($data['edit'][0]->work_end_time)) : date("h:i A") ?>"/>
				            </div> 
                </div>
                <!-- /.box-body -->
                <div class="clearfix"><!-- --></div>
            

<div class="box-footer">
  <div class="pull-right"><button type="submit" class="btn btn-primary btn-submit" data-loading-text="Please wait, processing">Check In</button></div>
  <div class="clearfix"><!-- --></div>
                  </div>
                  </form>
          </div>
          <div class="tab-pane" id="checkout">
          <form role="form" method="post" autocomplete="off" action="" enctype="multipart/form-data">
                  <div class="box-body">
                  <div class="form-group col-md-3 <?php echo (form_error('employee_department')? 'has-error':'')?>">
                      <label for="exampleInputEmail1" class="">Employee Name*</label>
                      <select class="form-control" placeholder="Country" name="employee_department" id="employee_department">
                         <option value="">Employee Name</option>
                       <?php foreach($data['department'] as $department){?>
                        <option value="<?php echo $department->department_name ?>" <?php echo (isset($data['edit'][0]->employee_department) && $department->department_name == $data['edit'][0]->employee_department) ? 'selected="selected"' : "" ?>
                        <?php if($department->department_name == set_value("employee_department")) { echo 'selected="selected"';}  ?>><?php echo $department->department_name?></option>
                        <?php } ?>
                        </select> 
                    </div>
                    <div class="clearfix"><!-- --></div>
                    <div class="form-group col-md-4 <?php echo (form_error('check_out_time')? 'has-error':'')?>">
					          <label for="exampleInputEmail1" class="container">Punch Time*</label>
                    <input type="text" class="form-control datetimepicker" id="check_out_time"  name="check_out_time" placeholder="check_out_time"value="<?php echo  isset($data['edit'][0]->work_end_time) ? date('h:i A',strtotime($data['edit'][0]->work_end_time)) : date("h:i A") ?>"/>
				            </div> 
                </div>
                <!-- /.box-body -->
                <div class="clearfix"><!-- --></div>

<div class="box-footer">
<input type="hidden" value="<?php echo isset($data['edit'][0]->employee_id) ? $data['edit'][0]->employee_id : set_value('employee_id'); ?>" name="employee_id" id="employee_id">
  <div class="pull-right"><button type="submit" class="btn btn-primary btn-submit" data-loading-text="Please wait, processing">Check Out</button></div>
  <div class="clearfix"><!-- --></div>
                  </div>
                  </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section
