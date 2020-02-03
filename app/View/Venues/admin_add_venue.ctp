<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
   <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h4><?php if(isset($this->params['pass'][0]) && !empty($this->params['pass'][0])){echo "Edit Venue";}else{echo "Add Venue";}?></h4>
          </div>
          <div class="col-sm-6">
            <button class="btn btn-primary" onclick="window.location.href='<?php echo SITE_PATH."admin/Venues/manage_venues"?>'" style="float:right">Manage Venues</button>
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
						<?php
							if(isset($this->params['pass'][1]) && !empty($this->params['pass'][1])){
								$view	= 	$this->params['pass'][1];
								$url 	=	'';
								$edit	=	0;
							}elseif(isset($this->params['pass'][0]) && !empty($this->params['pass'][0])){
								$url 	=	SITE_PATH.'admin/Venues/add_venue/'.$this->params['pass'][0];
								$edit	=	1;
							}else{
								$url 	=	'';
								$edit	=	2;
							}
							if(!empty($result)){
								$image 		= 	$this->My->getVenueImageById($result['Venue']['id']);
								$imag		=	array();
								foreach($image as $img){
									$imag[] 	= 	$img['VenueImage']['image'];
									$imagId[] 	= 	$img['VenueImage']['id'];
								}
							}
						?>
						<!-- /.card-header -->
						<!-- form start -->
						<form role="form" id="addVenue" method="post" action ="<?php echo $url;?>" enctype="multipart/form-data">
							<div class="card-body">
							
								<div class="form-group">
									<label>Sport Type</label>
									<select class="form-control required" name="sport_id">
										<option value="">Select Sport</option>
									<?php foreach($getSports as $getSport){ 
										$selected = (($getSport['Sport']['id'] == $result['Venue']['sports_id'])?'selected':"");
									?>
										<option value="<?php echo $getSport['Sport']['id']; ?>"<?php echo $selected;?>><?php echo $getSport['Sport']['name']; ?></option>
									<?php }?>
									</select>
								</div>
								<div class="form-group">
									<label>Venue Name</label>
									<input type="text" class="form-control required" name="name" <?php echo (isset($view)&& !empty($view)) ? 'readonly':''?> title="Please enter venue name" value="<?php echo ((isset($result) && !empty($result))?$result['Venue']['name']:'');?>">
								</div>
							
								<div class="form-group">
									<label>Image1</label>
									<?php if(!isset($this->params['pass'][1]) && empty($this->params['pass'][1])){?>
									<input type="file" class="form-control image required" name="image[]">
									
									<input type="hidden" name="old_image[]" value="<?php echo ((isset($imag) && !empty($imag[0]))? $imag[0]:'');?>">
									
									<input type="hidden" name="imageId[]" value="<?php echo ((isset($imagId) && !empty($imagId[0]))? $imagId[0]:'');?>">
									<?php }?>
								</div>
								<?php if(isset($imag) && !empty($imag[0])){?><img src="<?php echo SITE_PATH."img/venue/".$imag[0];?>" width="50px" height="50px"><?php }?>
								
								<div class="form-group">
									<label>Image2</label>
									<?php if(!isset($this->params['pass'][1]) && empty($this->params['pass'][1])){?>
									<input type="file" class="form-control image" name="image[]">
									
									<input type="hidden" name="image[]" value="<?php echo ((isset($imag) && !empty($imag[1]))? $imag[1]:'');?>">
									<input type="hidden" name="imageId[]" value="<?php echo ((isset($imagId) && !empty($imagId[1]))? $imagId[1]:'');?>">
									
									<?php }?>
								</div>
								<?php if(isset($imag) && !empty($imag[1])){?><img src="<?php echo SITE_PATH."img/venue/".$imag[1];?>" width="50px" height="50px"><?php }?>
								<div class="form-group">
									<label>Image3</label>
									<?php if(!isset($this->params['pass'][1]) && empty($this->params['pass'][1])){?>
									<input type="file" class="form-control image" name="image[]">
									
									<input type="hidden" name="image[]" value="<?php echo ((isset($imag) && !empty($imag[2]))? $imag[2]:'');?>">
									
									<input type="hidden" name="imageId[]" value="<?php echo ((isset($imagId) && !empty($imagId[2]))? $imagId[2]:'');?>">
									
									<?php }?>
								</div>
								<?php if(isset($imag) && !empty($imag[2])){?><img src="<?php echo SITE_PATH."img/venue/".$imag[2];?>" width="50px" height="50px"><?php }?>
			
								<div class="form-group">
									<label>Address</label>
									<textarea name = "address" placeholder="Enter Address" class="form-control required" ><?php echo ((isset($result) && !empty($result))?$result['Venue']['address']:'');?></textarea>
								</div>
							</div>
							<?php if(!isset($this->params['pass'][1]) && empty($this->params['pass'][1])){?>
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
			$("#addVenue").validate();
		});
		
		$(".image").change(function(){
			var fileName = $(this).val();
			var fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1); 
			if(fileExtension == 'jpeg' || fileExtension == 'jpg'|| fileExtension == 'png' || fileExtension == 'gif' || fileExtension == 'GIF'|| fileExtension == 'JPEG' || fileExtension == 'JPG'){
				//alert('ok');
			}else{
				alert('Please upload only image of type jpeg, jpg, png, gif');
				$(".image").val('');
				return false;
			}
			
		});	
	</script>

