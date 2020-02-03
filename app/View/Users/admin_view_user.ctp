<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
    </section>

    <!-- Main content -->
    <section class="content">
		<div class="container-fluid">
			<div class="row">
				<!-- left column -->
				<div class="col-md-6">
					<!-- general form elements -->

					<div class="card card-primary">
						<?php if(isset($this->params['pass'][1]) && !empty($this->params['pass'][1])){
							$view	= $this->params['pass'][1];
						}?>
						<!-- /.card-header -->
						<!-- form start -->
						<form role="form" id="addUser" method="post" action ="<?php echo SITE_PATH.'admin/Users/view_user/'.$this->params['pass'][0];?>" >
							<div class="card-body">
								<div class="form-group">
									<label>Name</label>
									<input type="text" class="form-control required" name="full_name" <?php echo (isset($view)&& !empty($view)) ? 'readonly':''?>  value="<?php echo $result['User']['full_name'];?>">
								</div>
								
								<div class="form-group">
									<label>Email</label>
									<input type="text" class="form-control required" name="email" readonly  value="<?php echo $result['User']['email'];?>">
								</div>
								<div class="form-group">
									<label>Gender</label>
									<?php if(isset($view)&& !empty($view)){?>
									<input type="text" class="form-control required" name="gender" readonly  value="<?php echo ($result['User']['gender']=1?'Male':'Female');?>">
									<?php }else{?>
									<select class="form-control required" name="gender">
										<option value="1"<?php echo (!empty($result)?($result['User']['gender']==1 ? 'selected':''):'');?>>Male</option>
										<option value="0"<?php echo (!empty($result)?($result['User']['gender']==0 ? 'selected':''):'');?>>Female</option>
									</select>
									<?php }?>
								</div>
								
								
								<div class="form-group">
									<label>Phone</label>
									<input type="text" class="form-control required" <?php echo (isset($view)&& !empty($view)) ? 'readonly':''?> name="mobile"  value="<?php echo $result['User']['mobile'];?>">
								</div>
								<div class="form-group">
									<label>User Type</label>
									<input type="text" class="form-control required" readonly name="user_type"  value="<?php echo ($result['User']['user_type']==1?'Player':'Organiser');?>">
								</div>
								<div class="form-group">
									<label>Status</label>
									<?php if(isset($view)&& !empty($view)){?>
									<input type="text" class="form-control required" name="gender" readonly  value="<?php echo ($result['User']['status']==1?'Active':'Inactive');?>">
									<?php }else{?>
									<select class="form-control required" name="status">
										<option value="1"<?php echo (!empty($result)?($result['User']['status']==1 ? 'selected':''):'');?>>Active</option>
										<option value="0"<?php echo (!empty($result)?($result['User']['status']==0 ? 'selected':''):'');?>>Inactive</option>
									</select>
									<?php }?>
								</div>
								
								
							</div>
							<?php if(empty($view)){?>
							<div class="card-footer">
								<input type="submit" name="submit" class="btn btn-primary" value="Submit">
							</div>
							<?php }?>
						</form>
						
					</div>
				</div>
			</div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
	<script>
	$( document ).ready(function() {
		$("#addUser").validate();
	});
	</script>
