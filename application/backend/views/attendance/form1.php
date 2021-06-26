<section class="content">
  <div class="row">
    <!-- left column -->
    <div class="col-md-12">
    <div class="box box-primary">
					<div class="box-header">
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

                    <div class="form-group col-md-3 <?php echo (form_error('employee_department')? 'has-error':'')?>">
                      <label for="exampleInputEmail1" class="">Year*</label>
                      <select class="form-control" placeholder="Year" name="employee_department" id="employee_department">
                         <option value="">Year</option>
                       <?php foreach($data['department'] as $department){?>
                        <option value="<?php echo $department->department_name ?>" <?php echo (isset($data['edit'][0]->employee_department) && $department->department_name == $data['edit'][0]->employee_department) ? 'selected="selected"' : "" ?>
                        <?php if($department->department_name == set_value("employee_department")) { echo 'selected="selected"';}  ?>><?php echo $department->department_name?></option>
                        <?php } ?>
                        </select> 
                    </div>
                    <div class="clearfix"><!-- --></div>
                    <div class="form-group col-md-3 <?php echo (form_error('employee_department')? 'has-error':'')?>">
                      <label for="exampleInputEmail1" class="">Month*</label>
                      <select class="form-control" placeholder="Country" name="employee_department" id="employee_department">
                         <option value="">Month</option>
                       <?php foreach($data['department'] as $department){?>
                        <option value="<?php echo $department->department_name ?>" <?php echo (isset($data['edit'][0]->employee_department) && $department->department_name == $data['edit'][0]->employee_department) ? 'selected="selected"' : "" ?>
                        <?php if($department->department_name == set_value("employee_department")) { echo 'selected="selected"';}  ?>><?php echo $department->department_name?></option>
                        <?php } ?>
                        </select> 
                    </div>
                    <div class="clearfix"><!-- --></div>
                    <div class="form-group col-md-3 <?php echo (form_error('work_end_time')? 'has-error':'')?>">
					          <label for="exampleInputEmail1" class="">In Time*</label>
                    <input type="text" class="form-control timepicker" id="work_end_time"  name="work_end_time" placeholder="work end Time"value="<?php echo  isset($data['edit'][0]->work_end_time) ? date('h:i A',strtotime($data['edit'][0]->work_end_time)) : date("h:i A") ?>"/>
				            </div> 
                    <div class="clearfix"><!-- --></div>
                    <div class="form-group col-md-3 <?php echo (form_error('work_end_time')? 'has-error':'')?>">
					          <label for="exampleInputEmail1" class="">Out Time*</label>
                    <input type="text" class="form-control timepicker" id="work_end_time"  name="work_end_time" placeholder="Out Time"value="<?php echo  isset($data['edit'][0]->work_end_time) ? date('h:i A',strtotime($data['edit'][0]->work_end_time)) : date("h:i A") ?>"/>
				            </div> 
                    <div class="box-footer">
                    <div class="pull-right"><button type="submit" class="btn btn-primary btn-submit" data-loading-text="Please wait, processing">Details</button></div>
                    <div class="clearfix"><!-- --></div>
                    











        </div>         
          </div>
          </div>
    </div>
  </div>
</section>
