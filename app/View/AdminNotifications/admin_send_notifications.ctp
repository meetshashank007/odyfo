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
						
						<form role="form" id="sendNotification" method="post" action="<?php echo SITE_PATH.'admin/Notifications/send_notifications';?>" enctype="multipart/form-data">
							<div class="card-body">
								<div class="form-group">
									<label>UserType</label>
									<select class="form-control required" name="user_type">
										<option value="">Select User</option>
										<option value="1">Player</option>
										<option value="2">Organiser</option>
									</select>
									
								</div>
								<div class="form-group">
									<label for="exampleInputEmail1">Message</label>
									<textarea name = "message" placeholder="Enter Message.." class="form-control required"></textarea>
								</div>
								<div class="form-group">
									<label for="exampleInputEmail1">Description</label>
									<textarea name = "description" placeholder="Enter Description.." class="form-control required"></textarea>
								</div>
								<div class="form-group">
									<label for="exampleInputEmail1">Upload Image</label>
									<input type="file" class="form-control" name="image">
								</div>
							
								<div class="card-footer">
									<input type="submit" name="upload" class="btn btn-primary" value="Send">
								</div>
							</div>
						</form>
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
		$("#sendNotification").validate();
	});
	
</script>
 