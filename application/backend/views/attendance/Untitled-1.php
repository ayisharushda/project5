<section class="content">
  <div class="row">
    <!-- left column -->
    <div class="col-md-12">
    <div class="box box-primary">
					<div class="box-header">
  <ul class="nav nav-tabs">
    <li class="active">
    <a href="#checkin" data-toggle="tab" aria-expanded="false">Check In</a>
    </li>
    <li>
    <a href="#checkout" data-toggle="tab" aria-expanded="false">Check Out</a>
    </li>
    <div class="pull-right"><a class="btn btn-primary btn-xs" title="Cancel" href="<?php echo base_url();?>employee/index">Cancel</a></div>
  </ul>
  </div>
        <div class="tab-content">
          <div class="tab-pane active" id="checkin">
          <form role="form" method="post" autocomplete="off" action="" enctype="multipart/form-data">
                  <div class="box-body">
                  <div class="form-group col-md-4 <?php echo (form_error('employee_code')? 'has-error':'')?>">
                      <label for="exampleInputEmail1" class="">Employee Name*&nbsp;<?php echo form_error('employee_code','(<code>','</code>)'); ?></label>
                      <input type="text" class="form-control" id="employee_code"  name="employee_code" placeholder="Employee Name*" 
                      value="<?php echo  isset($data['edit'][0]->employee_code) ? $data['edit'][0]->employee_code : set_value("employee_code") ?>"/>
                    </div>
                    <div class="clearfix"><!-- --></div>
                    <div class="form-group col-md-4 <?php echo (form_error('employee_code')? 'has-error':'')?>">
                      <label for="exampleInputEmail1" class="">Punch Time*&nbsp;<?php echo form_error('employee_code','(<code>','</code>)'); ?></label>
                      <input type="text" class="form-control" id="employee_code"  name="employee_code" placeholder="Punch Time*" 
                      value="<?php echo  isset($data['edit'][0]->employee_code) ? $data['edit'][0]->employee_code : set_value("employee_code") ?>"/>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="clearfix"><!-- --></div>

<div class="box-footer">
  <div class="pull-right"><button type="submit" class="btn btn-primary btn-submit" data-loading-text="Please wait, processing">Check In</button></div>
  <div class="clearfix"><!-- --></div>
                  </div>
                  </form>
                  <div class="tab-pane" id="checkout">
                    <form role="form" method="post" autocomplete="off" action="" enctype="multipart/form-data">
                  <div class="box-body">
                  <div class="form-group col-md-4 <?php echo (form_error('employee_code')? 'has-error':'')?>">
                      <label for="exampleInputEmail1" class="">Employee Name*&nbsp;<?php echo form_error('employee_code','(<code>','</code>)'); ?></label>
                      <input type="text" class="form-control" id="employee_code"  name="employee_code" placeholder="Employee Name*" 
                      value="<?php echo  isset($data['edit'][0]->employee_code) ? $data['edit'][0]->employee_code : set_value("employee_code") ?>"/>
                    </div>
                    <div class="clearfix"><!-- --></div>
                    <div class="form-group col-md-4 <?php echo (form_error('employee_code')? 'has-error':'')?>">
                      <label for="exampleInputEmail1" class="">Punch Time*&nbsp;<?php echo form_error('employee_code','(<code>','</code>)'); ?></label>
                      <input type="text" class="form-control" id="employee_code"  name="employee_code" placeholder="Punch Time*" 
                      value="<?php echo  isset($data['edit'][0]->employee_code) ? $data['edit'][0]->employee_code : set_value("employee_code") ?>"/>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="clearfix"><!-- --></div>

<div class="box-footer">
  <div class="pull-right"><button type="submit" class="btn btn-primary btn-submit" data-loading-text="Please wait, processing">Check Out</button></div>
  <div class="clearfix"><!-- --></div>
                  </div>
                  </form>





          </div>
          </div>
    </div>
  </div>
</section
