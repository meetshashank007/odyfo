<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
	<div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h4><?php if(isset($this->params['pass'][1]) && !empty($this->params['pass'][1])){echo "View Player";}else{echo "Edit Player";}?></h4>
          </div>
          <div class="col-sm-6">
            <button class="btn btn-primary" onclick="window.location.href='<?php echo SITE_PATH."admin/Players/manage_players"?>'" style="float:right">Manage Player</button>
          </div>
        </div>
      </div><!-- /.container-fluid -->
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
						<form role="form" id="addUser" method="post" action ="<?php echo SITE_PATH.'admin/Players/view_player/'.$this->params['pass'][0];?>" >
							<div class="card-body">
								<div class="form-group">
									<?php if(isset($result) && !empty($result)){
											if(!empty($result['User']['user_image'])){
												$image = (!empty($result['User']['social_id'])?$result['User']['user_image']:SITE_PATH."img/user/".$result['User']['user_image']);
											}else{
												$image	=	SITE_PATH."img/no-user.jpg";
											}
									?><img src="<?php echo $image;?>" width="100px" height="100px"><?php }?>
								</div>
								
								<div class="form-group">
									<label>Name</label>
									<input type="text" class="form-control" name="full_name" <?php echo (isset($view)&& !empty($view)) ? 'readonly':''?>  value="<?php echo $result['User']['full_name'];?>">
								</div>
								
								<div class="form-group">
									<label>Email</label>
									<input type="text" class="form-control required" name="email" readonly  value="<?php echo $result['User']['email'];?>">
								</div>
								<div class="form-group">
									<label>Gender</label>
									<?php if(isset($view)&& !empty($view)){?>
									<input type="text" class="form-control required" name="gender" readonly  value="<?php if($result['User']['gender']==1){echo 'Male';}elseif($result['User']['gender']==2){echo 'Female';}else{echo 'Other';}?>">
									<?php }else{?>
									<select class="form-control required" name="gender">
										<option value="1"<?php echo (!empty($result)?($result['User']['gender']==1 ? 'selected':''):'');?>>Male</option>
										<option value="2"<?php echo (!empty($result)?($result['User']['gender']==2 ? 'selected':''):'');?>>Female</option>
									</select>
									<?php }?>
								</div>
								
								
								<div class="form-group">
									<label>Phone</label>
									<input type="text" class="form-control required" readonly <?php echo (isset($view)&& !empty($view)) ? 'readonly':''?> name="mobile"  value="<?php echo $result['User']['mobile'];?>">
								</div>
								
								<div class="form-group">
									<label>City</label>
									<input type="text" class="form-control required" readonly  name="city_id"  value="<?php $city = $this->My->getCityById($result['User']['city_id']);echo (!empty($city)?$city['City']['name']:"");?>">
								</div>
								<div class="form-group">
									<label>Rating</label>
									<input type="text" class="form-control required" readonly  value="<?php echo $rating;?>">
								</div>
								<div class="form-group">
									<label>Ranking</label>
									<input type="text" class="form-control required" readonly value="<?php echo $ranking;?>">
								</div>
								
								<div class="form-group">
									<label>User Type</label>
									<input type="text" class="form-control required" readonly name="user_type"  value="<?php echo ($result['User']['user_type']==1?'Player':'Organiser');?>">
								</div>
								<div class="form-group">
									<label>OTP Verify</label>
									<?php if(isset($view)&& !empty($view)){?>
									<input type="text" class="form-control required" name="gender" readonly  value="<?php echo ($result['User']['status']==1?'Yes':'No');?>">
									<?php }else{?>
									<select class="form-control required" name="status">
										<option value="1"<?php echo (!empty($result)?($result['User']['status']==1 ? 'selected':''):'');?>>Yes</option>
										<option value="0"<?php echo (!empty($result)?($result['User']['status']==0 ? 'selected':''):'');?>>No</option>
									</select>
									<?php }?>
								</div>
								<div class="form-group">
									<label>Status</label>
									<?php if(isset($view)&& !empty($view)){?>
									<input type="text" class="form-control required" name="gender" readonly  value="<?php echo ($result['User']['admin_status']==1?'Yes':'No');?>">
									<?php }else{?>
									<select class="form-control required" name="admin_status">
										<option value="1"<?php echo (!empty($result)?($result['User']['admin_status']==1 ? 'selected':''):'');?>>Yes</option>
										<option value="0"<?php echo (!empty($result)?($result['User']['admin_status']==0 ? 'selected':''):'');?>>No</option>
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
	/* $( document ).ready(function() {
		$("#addUser").validate();
	}); */
	
	
	$(document).ready(function() {
		jQuery.validator.addMethod("lettersonly", function(value, element) {
			 return this.optional(element) || /^[a-z\s]+$/i.test(value);
		});

		jQuery.validator.addMethod("noSpace", function(value, element) { 
			return value == '' || value.trim().length != 0;  
		}, "No space please and don't leave it empty");
		
		$("#addUser").validate({
			ignore: [],
			rules: {
			   full_name:{
					required: true,
					noSpace: true,
					lettersonly: true
				},
			  },
			   messages: {
				full_name: {
					required:"Please enter name",
					noSpace: "Please enter name",
					lettersonly:"Please enter only letter"
				}

			  }

		});
	});
	</script>
