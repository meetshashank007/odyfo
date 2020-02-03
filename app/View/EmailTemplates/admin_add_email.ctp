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
						
						<!-- /.card-header -->
						<!-- form start -->
						<?php //echo "<pre>";print_r($getData);die;
						if(isset($this->params['pass'][1]) && !empty($this->params['pass'][1])){
							$view	= 	$this->params['pass'][1];
							$url	=	'';
						}if(isset($this->params['pass'][0]) && !empty($this->params['pass'][0])){
							$url	=	SITE_PATH.'admin/EmailTemplates/add_email/'.$this->params['pass'][0];
						}else{
							$url	=	SITE_PATH.'admin/EmailTemplates/add_email/';
						}?>
						<form role="form" id="addEmail" method="post" action="<?php echo $url;?>" enctype="multipart/form-data">
							<div class="card-body">
								<div class="form-group brand_name">
									<label for="exampleInputEmail1">Slug</label>
									<input type="text" class="form-control required" id="slug" name="slug" placeholder="Enter Slug" value="<?php echo (!empty($getData)?$getData['slug']:'')?>" <?php echo (!empty($getData)?'readonly':'')?> >
								</div>
								
								<div class="form-group brand_name">
									<label for="exampleInputEmail1">Subject</label>
									<input type="text" class="form-control required" id="subject" name="subject" placeholder="Enter Subject" value="<?php echo (!empty($getData)?$getData['subject']:'')?>" <?php echo (!empty($view)?'readonly':'')?>>
								</div>
								<div class="form-group brand_name">
									<label for="exampleInputEmail1">Mail Title</label>
									<input type="text" class="form-control required" id="mail_title" name="mail_title" placeholder="Enter Mail Title" value="<?php echo (!empty($getData)?$getData['mail_title']:'')?>" <?php echo (!empty($view)?'readonly':'')?>>
								</div>
								<div class="form-group brand_name">
									<label for="exampleInputEmail1">Variable</label>
									<input type="text" class="form-control required" id="var" name="var" placeholder="Enter Variable" value="<?php echo (!empty($getData)?$getData['var']:'')?>" <?php echo (!empty($view)?'readonly':'')?>>
								</div>
							
							
								<div class="form-group">
									<label for="exampleInputEmail1">Description</label>
									<textarea name="description" <?php echo (!empty($view)?'readonly':'')?>><?php echo (!empty($getData)?$getData['description']:'');?></textarea>
								</div>
								
							
							</div>
							<!-- /.card-body -->
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
			$("#addEmail").validate();
		});
		CKEDITOR.replace( 'description' );
	</script>
	
	
		