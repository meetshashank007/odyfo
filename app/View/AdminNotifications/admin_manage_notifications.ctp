<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="/resources/demos/style.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
		
        <div class="col-12">
        <center><div class="text-success message"><?php echo $this->Session->flash();?></div></center>
          <div class="card">
            
            <!-- /.card-header -->
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Sr. No.</th>
				  <th>Image</th>
                  <th>Name</th>
                  <th>Message</th>
				  <th>Description</th>
				  <th>Date</th>
				  <th>Action</th>
			    </tr>
                </thead>
                <tbody>
				<?php 
				if($result){
					$i=1;
					foreach($result as $res){?>
						<tr>
							<td><?php echo $i;?></td>
							<td><?php if(!empty($res['AdminNotification']['image'])){?><img src="<?php echo $res['AdminNotification']['image'];?>" width="100px" height="100px"><?php }else{echo '';}?></td>
							<td><?php $userName = $this->My->getuserbyId($res['AdminNotification']['receiver_id']); 
							echo $userName['username'];?></td>
							<td><?php echo $res['AdminNotification']['message'];?></td>
							<td><?php echo ((strlen($res['AdminNotification']['description'])>100)?substr($res['AdminNotification']['description'],0,150)."..":$res['AdminNotification']['description']);?></td>
							<td><?php echo $res['AdminNotification']['created'];?></td>
							<td>
								<a href="#" onclick="del_confirm('Are you Sure want to delete this record?','<?php echo SITE_PATH.'admin/Notifications/delete/'.$res['AdminNotification']['id'];?>')" title="Delete"><i class="fa fa-trash"></i></a>
							</td>
						</tr>
				<?php	$i++;
						
					}
				}?>
               
                </tfoot>
              </table>
			
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
	<script>
		function del_confirm(msg,url){
			if(confirm(msg)){
				window.location.href=url
			}
			else{
				false;
			}
		}
		
	</script>
  
 
