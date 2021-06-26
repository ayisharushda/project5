<section class="content">
  <div class="row">
    <!-- left column -->
    <div class="col-md-12">
    <div class="box box-primary">
					<div class="box-header">
                    <div class="form-group col-md-3 <?php echo (form_error('employee_department')? 'has-error':'')?>">
                      <label for="exampleInputEmail1" class="">Employee:</label>
                      <select class="form-control" placeholder="Country" name="employee_department" id="employee_department">
                         <option value="">Select Option</option>
                       <?php foreach($data['department'] as $department){?>
                        <option value="<?php echo $department->department_name ?>" <?php echo (isset($data['edit'][0]->employee_department) && $department->department_name == $data['edit'][0]->employee_department) ? 'selected="selected"' : "" ?>
                        <?php if($department->department_name == set_value("employee_department")) { echo 'selected="selected"';}  ?>><?php echo $department->department_name?></option>
                        <?php } ?>
                        </select> 
                    </div>
                    <div class="form-group col-md-3 <?php echo (form_error('employee_join_date')? 'has-error':'')?>">
						<label for="exampleInputEmail1" class="">From:</label>
                        <input type="text" class="form-control" id="employee_join_date" name="employee_join_date" placeholder="join Date" value="<?php echo  isset($data['edit'][0]->employee_join_date) ? date('m/d/Y',strtotime($data['edit'][0]->employee_join_date)) : date('m/d/Y'); ?>">
				    </div>
                    <div class="form-group col-md-3 <?php echo (form_error('employee_join_date')? 'has-error':'')?>">
						<label for="exampleInputEmail1" class="">To:</label>
                        <input type="text" class="form-control" id="employee_join_date" name="employee_join_date" placeholder="join Date" value="<?php echo  isset($data['edit'][0]->employee_join_date) ? date('m/d/Y',strtotime($data['edit'][0]->employee_join_date)) : date('m/d/Y'); ?>">
				    </div>
                    <div class="box-footer">
                    <div class="pull-right"><button type="submit" class="btn btn-primary btn-submit" data-loading-text="Please wait, processing">Search</button></div>
                    <div class="clearfix"><!-- --></div>

          </div>
          </div>
    </div>
  </div>
</section>