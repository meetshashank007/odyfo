<script src="https://cdn.ckeditor.com/4.11.1/standard/ckeditor.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.css" rel="stylesheet" type="text/css" />
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
   <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <h4><?php if(isset($this->params['pass'][1]) && !empty($this->params['pass'][1])){echo "View Page";}elseif(isset($this->params['pass'][0]) && !empty($this->params['pass'][0])){echo "Edit Page";}else{echo 'Add Page';}?></h4>
          </div>
          <div class="col-sm-6">
            <button class="btn btn-primary" onclick="window.location.href='<?php echo SITE_PATH."admin/StaticPages/manage_pages"?>'" style="float:right">Manage Pages</button>
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
							$url 	=	SITE_PATH.'admin/StaticPages/add_pages/'.$this->params['pass'][0];
							//$view	= 	$this->params['pass'][0];
							$edit	=	1;
						}else{
							$url 	=	SITE_PATH.'admin/StaticPages/add_pages';
							$edit	=	2;
						}
					?>
						<!-- /.card-header -->
						<!-- form start -->
						<form role="form" id="addPage" method="post" action ="<?php echo $url;?>">
							<div class="card-body">
								<div class="form-group">
									<label>Title</label>
									<input type="text" class="form-control required" name="title"  <?php echo (!empty($result)?'readonly':'');?> value="<?php echo (!empty($result)?$result['StaticPage']['title']:'');?>">
								</div>
								<div class="form-group">
									<label>Language</label>
									<?php if(isset($result) && !empty($result)){ ?>
										<input type="text" class="form-control required" name=""  readonly value="<?php  echo $this->My->getLanguage($result['StaticPage']['language']);?>">
										<input type="hidden" class="form-control " name="language"  readonly value="<?php  echo $result['StaticPage']['language'];?>">
									<?php }else{?>
									<select class="form-control required"  name="language">
										<option value="">Select Language</option>
										<option value="1" <?php echo (!empty($result)?($result['StaticPage']['language']==1 ?'selected':''):'');?>>English</option>
										<option value="2"<?php echo (!empty($result)?($result['StaticPage']['language']==2 ?'selected':''):'');?>>Spanish</option>
										<option value="3"<?php echo (!empty($result)?($result['StaticPage']['language']==3 ?'selected':''):'');?>>Itallian</option>
										<option value="4"<?php echo (!empty($result)?($result['StaticPage']['language']==4 ?'selected':''):'');?>>German</option>
										<option value="5"<?php echo (!empty($result)?($result['StaticPage']['language']==5 ?'selected':''):'');?>>French</option>
										
									</select>
									<?php }?>
								</div>
								<div class="form-group">
									<label>Description</label>
									<textarea name = "description" class="form-control" <?php echo ((isset($view) && !empty($view))?'readonly':'');?> ><?php echo (!empty($result)?$result['StaticPage']['description']:'');?></textarea>
								</div>
								<div class="form-group">
									<label>Status</label>
									<?php if(isset($view) && !empty($view)){ ?>
										<input type="text" class="form-control" name=""  readonly value="<?php  echo ($result['StaticPage']['status']==1 ? 'Active':'Inactive');?>">
									<?php }else{?>
									<select class="form-control required" name="status">
										<option value="1" <?php echo (!empty($result)?($result['StaticPage']['status']==1 ?'selected':''):'');?>>Active</option>
										<option value="0" <?php echo (!empty($result)?($result['StaticPage']['status']==0 ?'selected':''):'');?>>Inactive</option>
									</select>
									<?php }?>
								</div>
								
								<div class="card-footer">
									<input type="submit" name="submit" class="btn btn-primary" value="Submit">
								</div>
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
		$("#addPage").validate();
	});
	CKEDITOR.replace( 'description' );
	</script>