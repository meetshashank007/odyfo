<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h4><?php if(isset($this->params['pass'][1]) && !empty($this->params['pass'][1])){echo "View News";}elseif(isset($this->params['pass'][0]) && !empty($this->params['pass'][0])){echo "Edit News";}else{echo 'Add News';}?></h4>
          </div>
          <div class="col-sm-6">
            <button class="btn btn-primary" onclick="window.location.href='<?php echo SITE_PATH."admin/News/manage_news"?>'" style="float:right">Manage News</button>
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
						<form role="form" id="addGame" method="post" action ="<?php echo SITE_PATH.'admin/News/view_news/'.$this->params['pass'][0];?>" enctype="multipart/form-data">
							<div class="card-body">
								<div class="form-group">
									<label>Name</label>
									<input type="text" class="form-control" name="name" <?php echo (isset($view)&& !empty($view)) ? 'readonly':''?>  value="<?php echo $result['News']['name'];?>">
								</div>
								
								<div class="form-group">
									<label>Image</label>
									<?php if(empty($view)){?>
									<input type="file" class="form-control image" name="image"><?php }?>
									<img src="<?php echo SITE_PATH."img/news/".$result['News']['image'];?>" width="50px" height="50px">
									<input type="hidden" name="news_image" value="<?php echo $result['News']['image'];?>">
									
								</div>
								
								<div class="form-group">
									<label>Organised By</label>
									<input type="text" class="form-control required" readonly <?php echo (isset($view)&& !empty($view)) ? 'readonly':''?> name="user_id"  value="<?php $name = $this->My->getuserbyId($result['News']['user_id']); echo $name['full_name'];?>">
								</div>
								<div class="form-group">
									<label for="exampleInputEmail1">Description</label>
									<textarea name = "description" placeholder="Enter Description" class="form-control required"><?php echo $result['News']['description'];?></textarea>
								</div>
								<div class="form-group">
									<label>Status</label>
									<?php if(!empty($view)){?>
										<input type="text" class="form-control required" id="type" placeholder="Enter type" name="type" value="<?php echo (($result['News']['status']==0)?'Inactive':'Active');?>"readonly>
									<?php }else{?>
									<select class="form-control required" name="status">
										<option value="1"<?php echo (!empty($result)?($result['News']['status']==1 ? 'selected':''):'');?>>Active</option>
										<option value="0"<?php echo (!empty($result)?($result['News']['status']==0 ? 'selected':''):'');?>>Inactive</option>
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
		$("#addGame").validate();
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
