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
                   <th>SrNo</th>
					<th>Image</th>
					<th>Name</th>
					<th>Team</th>
					<th>Status</th>
				</tr>
                </thead>
                <tbody>
				<?php 
				if($result){
					$i=1;
					foreach($result as $res){?>
						<tr>
							<td><?php echo $i;?></td>
							<td>
							<img src="<?php echo !empty($res['User']['social_id'])?$res['User']['user_image']:(!empty($res['User']['user_image'])?SITE_PATH."img/user/".$res['User']['user_image']:SITE_PATH."img/no-user.jpg");?>" width="50px" height="50px"></td>
							<td><?php echo $res['User']['full_name'];?></td>
							<td><?php echo (($res['Team']['team_id']==1)?"Blue":"Red");?></td>
							<td><?php if($res['Team']['status']==1){?>
								<div>
									<span class="badge badge-success">Active</span>
								</div><?php }else{?>
								<div>
									<span class="badge badge-danger">Inactive</span>
								</div><?php }?>
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
	
  
 
